document.addEventListener('DOMContentLoaded', () => {
  const cartDrawer = document.querySelector('#cart-drawer');
  const cartLinks = document.querySelectorAll('[data-cart-open]');
  const closeButtons = cartDrawer?.querySelectorAll('[data-cart-close]') || [];
  const cartCountNodes = document.querySelectorAll('[data-cart-count]');
  let returnFocus = null;

  const updateCartCount = (count) => cartCountNodes.forEach((node) => { node.textContent = String(count); });

  const loadDrawer = async () => {
    if (!cartDrawer) return;
    const lines = cartDrawer.querySelector('[data-cart-lines]');
    const status = cartDrawer.querySelector('[data-cart-status]');
    lines.innerHTML = '<div class="drawer-loading"><span class="button-spinner"></span><span>Loading your bag…</span></div>';
    status.textContent = '';
    try {
      const response = await fetch(`${cartDrawer.dataset.base}/cart?format=json`, {
        credentials: 'same-origin', headers: { Accept: 'application/json' },
      });
      const data = await response.json();
      if (!response.ok || !Array.isArray(data.items)) throw new Error('Your bag could not be loaded. Please open the full bag.');
      updateCartCount(data.count || 0);
      cartDrawer.querySelector('[data-drawer-count]').textContent = String(data.count || 0);
      cartDrawer.querySelector('[data-cart-total]').textContent = Number(data.total || 0).toFixed(2);
      const progress = Math.min(100, (Number(data.total || 0) / 50.01) * 100);
      cartDrawer.querySelector('[data-delivery-progress]').style.width = `${progress}%`;
      cartDrawer.querySelector('.delivery-progress-track').setAttribute('aria-valuenow', String(Math.min(50, Number(data.total || 0))));
      const remainder = Math.max(0, Math.round((50.01 - Number(data.total || 0)) * 100) / 100);
      cartDrawer.querySelector('[data-delivery-message]').textContent = Number(data.total || 0) > 50
        ? 'You unlocked free delivery.'
        : `Add ${remainder.toFixed(2)} JOD for free delivery over 50 JOD.`;
      lines.replaceChildren();
      if (!data.items.length) {
        const empty = document.createElement('div');
        empty.className = 'drawer-empty';
        const title = document.createElement('b');
        title.textContent = 'Your bag is ready for a little care.';
        const link = document.createElement('a');
        link.href = `${cartDrawer.dataset.base}/shop`;
        link.textContent = 'Explore the collection';
        empty.append(title, link);
        lines.append(empty);
        return;
      }
      data.items.forEach((item) => {
        const row = document.createElement('article');
        row.className = 'drawer-item';
        const imageLink = document.createElement('a');
        imageLink.className = 'drawer-item-image';
        imageLink.href = `${cartDrawer.dataset.base}/product?id=${encodeURIComponent(item.id)}`;
        if (item.image_url) {
          const image = document.createElement('img');
          image.src = item.image_url;
          image.alt = '';
          image.loading = 'lazy';
          imageLink.append(image);
        }
        const copy = document.createElement('div');
        copy.className = 'drawer-item-copy';
        const name = document.createElement('b');
        name.textContent = item.name;
        const detail = document.createElement('small');
        detail.textContent = item.size ? `Size ${item.size}` : 'One-time purchase';
        const controls = document.createElement('div');
        controls.className = 'drawer-item-controls';
        const quantity = document.createElement('div');
        quantity.className = 'drawer-quantity';
        const minus = document.createElement('button');
        minus.type = 'button'; minus.textContent = '−'; minus.setAttribute('aria-label', `Decrease ${item.name} quantity`);
        minus.disabled = Number(item.quantity) <= 1;
        const qty = document.createElement('span'); qty.textContent = String(item.quantity);
        const plus = document.createElement('button');
        plus.type = 'button'; plus.textContent = '+'; plus.setAttribute('aria-label', `Increase ${item.name} quantity`);
        const remove = document.createElement('button');
        remove.type = 'button'; remove.className = 'drawer-remove'; remove.textContent = 'Remove';
        quantity.append(minus, qty, plus);
        controls.append(quantity, remove);
        copy.append(name, detail, controls);
        const price = document.createElement('strong');
        price.className = 'drawer-item-price'; price.textContent = `${Number(item.subtotal).toFixed(2)} JOD`;
        row.append(imageLink, copy, price);
        lines.append(row);

        const mutate = async (endpoint, values) => {
          const body = new URLSearchParams({ csrf_token: cartDrawer.dataset.csrf, product_id: String(item.id), size: item.size || '', ...values });
          [minus, plus, remove].forEach((button) => { button.disabled = true; });
          try {
            const result = await fetch(`${cartDrawer.dataset.base}/cart/${endpoint}`, {
              method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json' }, body,
            });
            const payload = await result.json();
            if (!result.ok || !payload.ok) throw new Error(payload.message || 'Could not update your bag.');
            await loadDrawer();
          } catch (error) {
            status.textContent = error.message;
            [minus, plus, remove].forEach((button) => { button.disabled = false; });
          }
        };
        minus.addEventListener('click', () => mutate('update', { quantity: String(Number(item.quantity) - 1) }));
        plus.addEventListener('click', () => mutate('update', { quantity: String(Number(item.quantity) + 1) }));
        remove.addEventListener('click', () => mutate('remove', {}));
      });
    } catch (error) {
      lines.replaceChildren();
      status.textContent = error.message;
    }
  };

  const openDrawer = (trigger) => {
    if (!cartDrawer) return false;
    returnFocus = trigger || document.activeElement;
    cartDrawer.hidden = false;
    cartDrawer.inert = false;
    document.body.classList.add('cart-drawer-open');
    cartLinks.forEach((link) => link.setAttribute('aria-expanded', 'true'));
    cartDrawer.querySelector('[data-cart-close]:not(.cart-drawer-backdrop)')?.focus();
    loadDrawer();
    return true;
  };

  const closeDrawer = () => {
    if (!cartDrawer || cartDrawer.hidden) return;
    cartDrawer.hidden = true;
    cartDrawer.inert = true;
    document.body.classList.remove('cart-drawer-open');
    cartLinks.forEach((link) => link.setAttribute('aria-expanded', 'false'));
    returnFocus?.focus();
  };

  cartLinks.forEach((link) => link.addEventListener('click', (event) => {
    if (openDrawer(link)) event.preventDefault();
  }));
  closeButtons.forEach((button) => button.addEventListener('click', closeDrawer));
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeDrawer();
    if (event.key === 'Tab' && cartDrawer && !cartDrawer.hidden) {
      const focusable = [...cartDrawer.querySelectorAll('a[href],button:not(:disabled),input:not(:disabled),select:not(:disabled)')];
      if (!focusable.length) return;
      if (event.shiftKey && document.activeElement === focusable[0]) { event.preventDefault(); focusable.at(-1).focus(); }
      else if (!event.shiftKey && document.activeElement === focusable.at(-1)) { event.preventDefault(); focusable[0].focus(); }
    }
  });

  document.querySelectorAll('form[data-ajax-add]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
      if (!cartDrawer) return;
      event.preventDefault();
      const button = form.querySelector('button[type="submit"]');
      const label = form.querySelector('[data-button-label]');
      if (!button || button.disabled) return;
      const originalLabel = label?.textContent || 'Add to bag';
      button.disabled = true;
      button.classList.add('is-loading');
      if (label) label.textContent = 'Adding…';
      try {
        const response = await fetch(form.action, {
          method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json' },
          body: new FormData(form),
        });
        const data = await response.json();
        if (!response.ok || !data.ok) throw new Error(data.message || 'Could not add this product.');
        updateCartCount(data.count || 0);
        openDrawer(button);
      } catch (error) {
        if (cartDrawer) {
          openDrawer(button);
          cartDrawer.querySelector('[data-cart-status]').textContent = error.message;
        }
      } finally {
        button.disabled = false;
        button.classList.remove('is-loading');
        if (label) label.textContent = originalLabel;
      }
    });
  });

  document.querySelectorAll('[data-product-image]').forEach((thumbnail) => {
    thumbnail.addEventListener('click', () => {
      const image = document.querySelector('#product-main-image');
      if (!image) return;
      image.style.opacity = '0';
      window.setTimeout(() => { image.src = thumbnail.dataset.productImage; image.style.opacity = '1'; }, 100);
      document.querySelectorAll('.pdp-thumbnail').forEach((item) => {
        const selected = item === thumbnail;
        item.classList.toggle('active', selected);
        item.setAttribute('aria-pressed', String(selected));
      });
    });
  });

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
