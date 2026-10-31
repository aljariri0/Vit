<?php

require_once __DIR__ . '/../Libraries/stripe-php-22.1.0/init.php';

class StripeService
{
    private string $currency;

    public function __construct(?array $config = null)
    {
        $config ??= require __DIR__ . '/../Core/config.php';
        $secretKey = trim((string) ($config['stripe_secret_key'] ?? ''));

        if ($secretKey === '' || str_contains($secretKey, 'YourSecretKeyHere')) {
            throw new RuntimeException('Set STRIPE_SECRET_KEY before accepting payments.');
        }

        $publishableKey = trim((string) ($config['stripe_publishable_key'] ?? ''));
        if ($publishableKey === '' || str_contains($publishableKey, 'YourPublishableKeyHere')) {
            throw new RuntimeException('Set STRIPE_PUBLISHABLE_KEY before accepting payments.');
        }

        $this->currency = strtolower((string) ($config['stripe_currency'] ?? 'jod'));
        \Stripe\Stripe::setApiKey($secretKey);
    }

    public function createPaymentIntent(int $orderId, float $amount): array
    {
        if ($orderId <= 0 || $amount <= 0) {
            return ['ok' => false, 'message' => 'Invalid order amount.'];
        }

        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount' => (int) round($amount * 100),
                'currency' => $this->currency,
                'metadata' => ['order_id' => (string) $orderId],
                'payment_method_types' => ['card'],
            ]);

            return [
                'ok' => true,
                'client_secret' => $intent->client_secret,
                'intent_id' => $intent->id,
            ];
        } catch (\Throwable $e) {
            error_log('Stripe intent creation failed: ' . $e->getMessage());
            return ['ok' => false, 'message' => 'Could not start the payment. Please try again.'];
        }
    }

    public function verifyPayment(string $intentId): array
    {
        try {
            $intent = \Stripe\PaymentIntent::retrieve($intentId, ['expand' => ['latest_charge']]);
            $charge = $intent->latest_charge;
            $last4 = $charge && isset($charge->payment_method_details->card->last4)
                ? $charge->payment_method_details->card->last4
                : null;

            return [
                'ok' => $intent->status === 'succeeded',
                'status' => $intent->status,
                'last4' => $last4,
                'amount' => (int) $intent->amount,
                'currency' => strtolower((string) $intent->currency),
                'order_id' => (string) ($intent->metadata->order_id ?? ''),
                'raw_data' => $intent->toArray(),
            ];
        } catch (\Throwable $e) {
            error_log('Stripe verification failed: ' . $e->getMessage());
            return ['ok' => false, 'status' => 'verification_error'];
        }
    }
}
