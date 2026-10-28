<?php
session_start();

function e(?string $str): string
{
    return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
}
function final_price(float $price, float $discount = 0): string
{
    return number_format($price * (1 - $discount / 100), 2);
}
function saved_amount(float $price, float $discount = 0): string
{
    return number_format($price * ($discount / 100), 2);
}
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="mock_token_123">';
}

$page = $_GET['page'] ?? 'home';

// Categories inspired by Myvitamins ("Shop By Need") & iHerb ("Sports Nutrition / Supplements")
$categories = [
    ['id' => 1, 'name' => 'Whey & Isolate Proteins', 'icon' => 'fa-dumbbell', 'description' => 'Muscle recovery & strength'],
    ['id' => 2, 'name' => 'Daily Multivitamins', 'icon' => 'fa-capsules', 'description' => 'Immunity & general wellbeing'],
    ['id' => 3, 'name' => 'Pre-Workout & Creatine', 'icon' => 'fa-bolt', 'description' => 'Energy, focus & performance'],
    ['id' => 4, 'name' => 'Collagen & Gummies', 'icon' => 'fa-heart-pulse', 'description' => 'Hair, skin, nails & joints'],
];

// Products matching your `products` SQL table + iHerb/Myvitamins details
$products = [
    [
        'id' => 1,
        'category_id' => 1,
        'category_name' => 'Whey & Isolate Proteins',
        'brand_name' => 'Optimum Gold',
        'name' => '100% Whey Isolate Protein Powder',
        'description' => '25g ultra-filtered whey isolate per serving with 5.5g BCAAs for lean muscle growth and fast recovery.',
        'price' => 64.99,
        'discount_percent' => 20,
        'delivery_price' => 3.00,
        'stock' => 35,
        'sizes' => ['900g (30 Servings)', '2.27kg (75 Servings)'],
        'flavors' => ['Double Rich Chocolate', 'French Vanilla'],
        'weight_kg' => 2.270,
        'rating_avg' => 4.8,
        'rating_count' => 1420,
        'image' => 'https://images.unsplash.com/photo-1593095948071-474c5cc2989d?w=600'
    ],
    [
        'id' => 2,
        'category_id' => 2,
        'category_name' => 'Daily Multivitamins',
        'brand_name' => 'Myvitamins Essentials',
        'name' => 'A-Z Multivitamin & Mineral Tablets',
        'description' => 'Blend of 22 essential vitamins and minerals including Vitamin D3, Zinc, and Iron for daily immunity and metabolism.',
        'price' => 19.99,
        'discount_percent' => 25,
        'delivery_price' => 2.00,
        'stock' => 80,
        'sizes' => ['60 Tablets', '120 Tablets', '240 Tablets'],
        'flavors' => ['Unflavored'],
        'weight_kg' => 0.180,
        'rating_avg' => 4.7,
        'rating_count' => 890,
        'image' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?w=600'
    ],
    [
        'id' => 3,
        'category_id' => 3,
        'category_name' => 'Pre-Workout & Creatine',
        'brand_name' => 'California Gold',
        'name' => 'Micronized Creatine Monohydrate Powder',
        'description' => 'Pure unflavored micronized creatine for explosive gym performance, strength, and cellular hydration.',
        'price' => 29.99,
        'discount_percent' => 15,
        'delivery_price' => 2.50,
        'stock' => 50,
        'sizes' => ['300g (60 Servings)', '600g (120 Servings)'],
        'flavors' => ['Unflavored', 'Blue Raspberry'],
        'weight_kg' => 0.600,
        'rating_avg' => 4.9,
        'rating_count' => 2150,
        'image' => 'https://images.unsplash.com/photo-1579722821273-0f6c7d44362f?w=600'
    ],
    [
        'id' => 4,
        'category_id' => 4,
        'category_name' => 'Collagen & Gummies',
        'brand_name' => 'Vitemines Lab',
        'name' => 'Omega 3-6-9 & Vitamin D3 Softgels',
        'description' => 'High-potency EPA & DHA essential fatty acids supporting heart, brain, and joint wellness.',
        'price' => 24.00,
        'discount_percent' => 0,
        'delivery_price' => 2.00,
        'stock' => 60,
        'sizes' => ['90 Softgels', '180 Softgels'],
        'flavors' => ['Lemon Softgel'],
        'weight_kg' => 0.220,
        'rating_avg' => 4.6,
        'rating_count' => 540,
        'image' => 'https://images.unsplash.com/photo-1550572017-edb959b55104?w=600'
    ]
];

$allowedPages = ['home', 'shop', 'product', 'assistant', 'cart', 'auth', 'profile', 'admin'];
$currentView = in_array($page, $allowedPages) ? $page : 'home';

// Select product by ID if clicked
$selectedId = (int) ($_GET['id'] ?? 1);
$product = $products[0];
foreach ($products as $item) {
    if ($item['id'] === $selectedId) {
        $product = $item;
        break;
    }
}

ob_start();
require __DIR__ . "/../app/Views/{$currentView}.php";
$content = ob_get_clean();
require __DIR__ . "/../app/Views/layout.php";