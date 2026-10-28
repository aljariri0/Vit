<h3 class="fw-bold mb-4">Your Supplement Bag & Checkout</h3>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-clean p-4">
            <table class="table align-middle">
                <thead><tr><th>Supplement</th><th>Size / Flavor</th><th>Qty</th><th>Total</th><th></th></tr></thead>
                <tbody>
                    <tr>
                        <td><strong>100% Whey Isolate Protein</strong></td>
                        <td><small class="text-muted">2.27kg • Double Rich Chocolate</small></td>
                        <td><input type="number" value="1" class="form-control form-control-sm" style="width:65px;"></td>
                        <td class="fw-bold text-vite-green">$51.99</td>
                        <td><button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>
            <div class="d-flex justify-content-between border-top pt-3 fw-bold fs-5">
                <span>Grand Total (with $3.00 Delivery):</span>
                <span class="text-vite-green">$54.99</span>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <form action="/checkout/place-order" method="POST" class="card-clean p-4">
            <?= csrf_field() ?>
            <h5 class="fw-bold mb-3">Delivery & Payment</h5>
            <input type="text" name="ship_name" class="form-control mb-2" placeholder="Recipient Full Name" required>
            <input type="text" name="ship_phone" class="form-control mb-2" placeholder="Phone Number" required>
            <input type="text" name="ship_city" class="form-control mb-2" placeholder="City" required>
            <input type="text" name="ship_address" class="form-control mb-2" placeholder="Street Address" required>
            <input type="text" name="coupon_code" class="form-control mb-3" placeholder="Promo Code (e.g. VITE20)">

            <label class="form-label small fw-bold">Payment Method</label>
            <select name="method" class="form-select mb-3">
                <option value="cash_on_delivery">Cash on Delivery</option>
                <option value="visa">Visa / Mastercard</option>
                <option value="paypal">PayPal</option>
            </select>
            <button type="submit" class="btn btn-vite w-100 py-2">Complete Order ($54.99)</button>
        </form>
    </div>
</div>