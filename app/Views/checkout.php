<?php
$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secure checkout | Shifa</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $escape($base_path) ?>/assets/css/shifa.css">
    <style>
        body { min-height: 100vh; display:grid; place-items:center; padding: 28px; background: radial-gradient(ellipse at 20% 12%, #e4eadb, transparent 43%), var(--cream); }
        .checkout-card { width:min(100%,600px); padding:clamp(24px,5vw,48px); background:#fbfbf8; border:1px solid var(--line); box-shadow:0 24px 65px #1b352312; }
        .checkout-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:42px; }
        .checkout-head a:last-child { font-size:10px; color:#73836c; }
        .checkout-kicker { color:#829775; font-size:9px; letter-spacing:1.7px; font-weight:700; }
        .checkout-card h1 { font:500 clamp(35px,6vw,48px)/1.06 Georgia,serif; color:var(--forest); margin:9px 0 12px; letter-spacing:-1.5px; }
        .checkout-subtitle { color:#788278; font-size:11px; }
        .checkout-total { display:flex; justify-content:space-between; align-items:center; margin:27px 0; padding:18px 0; border-block:1px solid var(--line); font-size:11px; color:#748072; }
        .checkout-total strong { font:500 22px Georgia,serif; color:var(--forest); }
        #payment-element { margin:1.5rem 0; }
        #payment-form button { width:100%; height:48px; border:0; border-radius:30px; background:var(--forest); color:white; font:600 11px var(--sans); cursor:pointer; transition:.2s; }
        #payment-form button:hover { background:#1a3b31; transform:translateY(-1px); }
        #payment-form button:disabled { opacity:.6; cursor:wait; }
        #message { min-height:1.5em; color:#a14e3e; font-size:11px; }
        .secure-note { text-align:center; font-size:9px; color:#8a9487; margin:14px 0 0; }
    </style>
</head>
<body>
<main class="checkout-card">
    <div class="checkout-head"><a class="brand" href="<?= $escape($base_path) ?>/"><span class="brand-mark">✳</span><span>shifa<span class="brand-dot">.</span><small>WELLNESS, YOUR WAY</small></span></a><a href="<?= $escape($base_path) ?>/cart">← Your bag</a></div>
    <p class="checkout-kicker">ONE LAST LITTLE STEP</p><h1>Checkout, with care.</h1>
    <p class="checkout-subtitle">Order #<?= $escape($order['id']) ?> · Your payment is encrypted and secure.</p>
    <div class="checkout-total"><span>Order total</span><strong><?= $escape(number_format((float) $order['total'], 2)) ?> <?= $escape($currency) ?></strong></div>
    <form id="payment-form">
        <div id="payment-element"></div>
        <button id="submit" type="submit">Pay securely</button>
        <p id="message" role="alert" aria-live="polite"></p>
    </form>
    <p class="secure-note">✳ &nbsp; Secure card payment, powered by Stripe</p>
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
