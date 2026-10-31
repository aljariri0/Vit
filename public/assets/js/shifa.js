document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.tilt-card').forEach((card) => {
    card.addEventListener('pointermove', (event) => {
      if (matchMedia('(prefers-reduced-motion: reduce)').matches || event.pointerType === 'touch') return;
      const box = card.getBoundingClientRect();
      const x = (event.clientX - box.left) / box.width - 0.5;
      const y = (event.clientY - box.top) / box.height - 0.5;
      card.style.transform = `perspective(850px) rotateY(${x * 3}deg) rotateX(${y * -3}deg) translateY(-2px)`;
    });
    card.addEventListener('pointerleave', () => { card.style.transform = ''; });
  });

  const checkoutForm = document.querySelector('[data-checkout-form]');
  if (checkoutForm) {
    checkoutForm.addEventListener('submit', async (event) => {
      event.preventDefault();
      const button = checkoutForm.querySelector('button[type="submit"]');
      const message = checkoutForm.querySelector('.checkout-message');
      const base = checkoutForm.dataset.base;
      button.disabled = true;
      button.textContent = 'Preparing your secure checkout…';
      message.textContent = '';
      try {
        const tokenResponse = await fetch(`${base}/orders/csrf`, { credentials: 'same-origin' });
        const tokenData = await tokenResponse.json();
        if (!tokenResponse.ok || !tokenData.ok) throw new Error(tokenData.message || 'Please sign in again.');
        const formData = new FormData(checkoutForm);
        const response = await fetch(`${base}/orders`, {
          method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            csrf_token: tokenData.csrf_token,
            items: JSON.parse(checkoutForm.dataset.items),
            shipping_data: Object.fromEntries(['ship_name', 'ship_phone', 'ship_address', 'ship_city'].map((key) => [key, formData.get(key)])),
          }),
        });
        const data = await response.json();
        if (!response.ok || !data.ok) throw new Error(data.message || 'Could not create your order. Please try again.');
        window.location.assign(data.checkout_url);
      } catch (error) {
        message.textContent = error.message;
        button.disabled = false;
        button.innerHTML = 'Continue to secure payment <span>↗</span>';
      }
    });
  }
});
