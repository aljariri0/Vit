<?php

class Env
{
    public static function load(): void
    {
        $file = dirname(__DIR__, 2) . '/.env';
        if (!is_file($file) || !is_readable($file)) return;
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if ($key === '' || getenv($key) !== false) continue;
            if (strlen($value) > 1 && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) {
                $value = substr($value, 1, -1);
            }
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}
