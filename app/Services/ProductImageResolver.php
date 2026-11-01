<?php

class ProductImageResolver
{
    private static ?array $productImageFiles = null;

    public function resolveProduct(array $product, string $basePath): array
    {
        $images = $product['image'] ?? [];
        if (is_string($images)) {
            $decoded = json_decode($images, true);
            $images = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($images)) {
            $images = [];
        }

        $basePath = rtrim($basePath, '/');
        $product['image'] = $images;
        $product['image_urls'] = array_values(array_unique(array_filter(array_map(
            fn ($storedImage): string => $this->resolveImage((string) $storedImage, $product, $basePath),
            $images
        ))));
        $product['image_url'] = (string) ($product['image_urls'][0] ?? '');
        $product['image_url_secondary'] = (string) ($product['image_urls'][1] ?? '');
        return $product;
    }

    private function resolveImage(string $storedImage, array $product, string $basePath): string
    {
        $storedImage = trim($storedImage);
        if ($storedImage === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $storedImage)) {
            return $storedImage;
        }
        if (!str_starts_with($storedImage, '/assets/products/')) {
            return str_starts_with($storedImage, '/assets/') ? $basePath . $storedImage : $storedImage;
        }

        $publicRoot = str_replace(chr(92), '/', dirname(__DIR__, 2)) . '/public';
        $relative = substr($storedImage, strlen('/assets/products/'));
        $relative = str_replace("Nature''s Bounty", "Nature's Bounty", $relative);
        $parts = explode('/', $relative);
        $brandFolders = [1 => 'Nature Made', 3 => 'Nature_s Bounty', 4 => 'Solgar DB Staging', 5 => 'gargen_of_life', 9 => 'now_foods'];
        $brandFolder = $brandFolders[(int) ($product['brand_id'] ?? 0)] ?? ($parts[0] ?? '');
        $categoryFolder = $this->categoryFolder((int) ($product['brand_id'] ?? 0), (string) ($parts[1] ?? ''));
        $relativeName = implode('/', array_slice($parts, 2));
        $candidates = [
            '/assets/products/' . $brandFolder . '/' . $categoryFolder . '/' . $relativeName,
            '/assets/products/' . $brandFolder . '/' . implode('/', array_slice($parts, 1)),
            $storedImage,
        ];
        foreach ($candidates as $candidate) {
            $candidates[] = preg_replace('/-cutout(?=\.[^.]+$)/i', '', $candidate);
        }
        foreach (array_unique($candidates) as $candidate) {
            $candidate = str_replace("Nature''s Bounty", "Nature's Bounty", $candidate);
            if (is_file($publicRoot . str_replace('/', DIRECTORY_SEPARATOR, $candidate))) {
                return $basePath . $candidate;
            }
        }

        self::$productImageFiles ??= $this->indexProductImages($publicRoot . '/assets/products');
        $brandRoot = $publicRoot . '/assets/products/' . $brandFolder;
        $categoryRoot = $brandRoot . '/' . $categoryFolder;
        $targetTokens = $this->nameTokens((string) ($product['name'] ?? ''));
        if (!$targetTokens) {
            return '';
        }
        $matches = [];
        foreach (self::$productImageFiles as $file) {
            if (!str_starts_with(strtolower($file), strtolower($brandRoot . '/'))) {
                continue;
            }
            $candidateTokens = $this->nameTokens(pathinfo($file, PATHINFO_FILENAME));
            if (!$candidateTokens) {
                continue;
            }
            $targetNumbers = array_filter($targetTokens, static fn (string $token): bool => ctype_digit($token));
            $candidateNumbers = array_filter($candidateTokens, static fn (string $token): bool => ctype_digit($token));
            if ($targetNumbers && $candidateNumbers && !array_intersect($targetNumbers, $candidateNumbers)) {
                continue;
            }
            $overlap = count(array_intersect($targetTokens, $candidateTokens));
            $score = $overlap / max(1, min(count($targetTokens), count($candidateTokens)));
            $sameCategory = str_starts_with(strtolower($file), strtolower($categoryRoot . '/'));
            if (($overlap >= 2 && $score >= 0.65) || ($overlap === 1 && count($targetTokens) === 1 && $score === 1.0)) {
                $matches[] = ['file' => $file, 'score' => $score + ($sameCategory ? 0.15 : 0)];
            }
        }
        if (!$matches) {
            return '';
        }
        usort($matches, static fn (array $a, array $b): int => ($b['score'] <=> $a['score']) ?: (strlen($a['file']) <=> strlen($b['file'])));
        $relativePath = str_replace('\\', '/', substr($matches[0]['file'], strlen($publicRoot)));
        return $basePath . $relativePath;
    }

    private function categoryFolder(int $brandId, string $storedCategory): string
    {
        $category = strtolower(preg_replace('/[^a-z]/i', '', $storedCategory));
        if ($brandId === 4) {
            return $storedCategory;
        }
        $mapping = [
            5 => ['vitamins' => 'vitamins', 'minerals' => 'minerals', 'multivitamins' => 'multiVitamins', 'omegaandfishoil' => 'omega&fish_oil', 'probiotics' => 'proBiotics', 'specialtysupplements' => 'specialty_supplements'],
            9 => ['vitamins' => 'vitamins', 'minerals' => 'minerals', 'multivitamins' => 'multiVitamins', 'omegaandfishoil' => 'omega&fish_oil', 'probiotics' => 'proBiotics', 'specialtysupplements' => 'specialty_supplements'],
        ];
        return $mapping[$brandId][$category] ?? $storedCategory;
    }

    private function nameTokens(string $name): array
    {
        $name = strtolower($name);
        $name = str_replace(['multivitamin', 'multivitamins'], 'multi', $name);
        preg_match_all('/[a-z]+\d*|\d+/i', $name, $matches);
        $ignored = ['the', 'for', 'with', 'and', 'daily', 'dietary', 'supplement', 'tablets', 'tablet', 'capsules', 'capsule', 'softgels', 'softgel', 'caplets', 'caplet', 'gummies', 'gummy', 'vegetable', 'vegan', 'billion', 'cfu', 'serving', 'pack'];
        return array_values(array_unique(array_filter($matches[0], static fn (string $token): bool => !in_array($token, $ignored, true))));
    }

    private function indexProductImages(string $root): array
    {
        if (!is_dir($root)) {
            return [];
        }
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file->isFile() && preg_match('/\.(png|jpe?g|webp)$/i', $file->getFilename())) {
                $files[] = str_replace('\\', '/', $file->getPathname());
            }
        }
        return $files;
    }
}


