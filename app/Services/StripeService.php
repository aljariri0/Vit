<?php
// app/Core/Services/StripeService.php

// 1. Load the manually downloaded Stripe SDK
require_once __DIR__ . '/../Libraries/stripe-php-22.1.0/init.php';

// 2. Load config
$config = require __DIR__ . '/../Core/config.php';

class StripeService
{
    public function __construct()
    {
        global $config;
        \Stripe\Stripe::setApiKey($config['stripe_secret_key']);
    }

    /**
     * Create a PaymentIntent (tells Stripe to expect a payment)
     */
    public function createPaymentIntent($orderId, $amount)
    {
        try {
            // Stripe requires amount in cents (e.g., $10.00 = 1000)
            $amountInCents = (int) round($amount * 100);

            $intent = \Stripe\PaymentIntent::create([
                'amount'   => $amountInCents,
                'currency' => 'usd', // Change to your currency (e.g., 'eur', 'gbp')
                'metadata' => ['order_id' => $orderId],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            return [
                'ok'            => true,
                'client_secret' => $intent->client_secret,
                'intent_id'     => $intent->id, // This goes into provider_transaction_id
            ];
        } catch (\Exception $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Verify the payment after the frontend confirms it
     */
    public function verifyPayment($intentId)
    {
        try {
            $intent = \Stripe\PaymentIntent::retrieve($intentId);

            // Extract card details if it was a card payment
            $last4 = null;
            if (isset($intent->charges->data[0]->payment_method_details->card)) {
                $last4 = $intent->charges->data[0]->payment_method_details->card->last4;
            }

            return [
                'ok'         => ($intent->status === 'succeeded'),
                'status'     => $intent->status,
                'last4'      => $last4,
                'raw_data'   => $intent->toArray(), // This full array goes into gateway_metadata
            ];
        } catch (\Exception $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }
}
