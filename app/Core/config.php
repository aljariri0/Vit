<?php

require_once __DIR__ . '/Env.php';
Env::load();

return [
    'stripe_secret_key' => getenv('STRIPE_SECRET_KEY') ?: '',
    'stripe_publishable_key' => getenv('STRIPE_PUBLISHABLE_KEY') ?: '',
    'stripe_currency' => 'jod',
];
