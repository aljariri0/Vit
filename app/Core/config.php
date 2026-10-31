<?php

return [
    'stripe_secret_key' => getenv('STRIPE_SECRET_KEY') ?: 'sk_test_YourSecretKeyHere',
    'stripe_publishable_key' => getenv('STRIPE_PUBLISHABLE_KEY') ?: 'pk_test_YourPublishableKeyHere',
    'stripe_currency' => strtolower(getenv('STRIPE_CURRENCY') ?: 'jod'),
];
