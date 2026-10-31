<?php
$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout | Vitemines</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body { margin: 0; padding: 2rem 1rem; background: #f5f7f5; color: #17251d; font: 16px system-ui, sans-serif; }
        main { max-width: 560px; margin: 3rem auto; padding: 2rem; background: white; border-radius: 14px; box-shadow: 0 8px 28px #163d2512; }
        h1 { margin-top: 0; } #payment-element { margin: 1.5rem 0; }
        button { width: 100%; padding: .85rem; border: 0; border-radius: 8px; background: #15803d; color: white; font: inherit; font-weight: 650; cursor: pointer; }
        button:disabled { opacity: .6; cursor: wait; } #message { min-height: 1.5em; color: #b42318; }
    </style>
</head>
<body>
<main>
    <h1>Secure checkout</h1>
    <p>Order #<?= $escape($order['id']) ?></p>
    <p><strong>Total: <?= $escape(number_format((float) $order['total'], 2)) ?> <?= $escape($currency) ?></strong></p>
    <form id="payment-form">
        <div id="payment-element"></div>
        <button id="submit" type="submit">Pay securely</button>
        <p id="message" role="alert" aria-live="polite"></p>
    </form>
</main>
<script>
    const stripe = Stripe(<?= json_encode($stripe_pub_key, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
    const elements = stripe.elements({clientSecret: <?= json_encode($client_secret, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>});
    elements.create('payment').mount('#payment-element');

    const form = document.querySelector('#payment-form');
    const button = document.querySelector('#submit');
    const message = document.querySelector('#message');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        button.disabled = true;
        message.textContent = '';

        const {error, paymentIntent} = await stripe.confirmPayment({
            elements,
            redirect: 'if_required'
        });

        if (error) {
            message.textContent = error.message || 'Payment could not be completed.';
            button.disabled = false;
            return;
        }

        if (!paymentIntent) {
            message.textContent = 'Payment status could not be confirmed.';
            button.disabled = false;
            return;
        }

        const basePath = <?= json_encode($base_path) ?>;
        const response = await fetch(`${basePath}/checkout/verify`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                payment_intent_id: paymentIntent.id,
                csrf_token: <?= json_encode($csrf_token) ?>
            })
        });
        const result = await response.json();
        message.textContent = result.message || 'Payment could not be confirmed.';
        if (result.ok) {
            message.style.color = '#15803d';
            button.textContent = 'Paid';
        } else {
            button.disabled = false;
        }
    });
</script>
</body>
</html>
