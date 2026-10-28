<div class="row g-4">
    <div class="col-md-5">
        <form action="/profile/update" method="POST" class="card-clean p-4">
            <?= csrf_field() ?>
            <h5 class="fw-bold mb-3">My Customer Profile</h5>
            <input type="text" name="name" class="form-control mb-2" value="Customer Name">
            <input type="text" name="phone" class="form-control mb-2" value="0790000000">
            <input type="text" name="city" class="form-control mb-2" value="Amman">
            <input type="text" name="address" class="form-control mb-3" value="Street 10">
            <button class="btn btn-vite w-100">Update Details</button>
        </form>
    </div>
    <div class="col-md-7">
        <div class="card-clean p-4">
            <h5 class="fw-bold mb-3">Order History</h5>
            <table class="table align-middle">
                <thead><tr><th>Order #</th><th>Date</th><th>Status</th><th>Total</th></tr></thead>
                <tbody>
                    <tr><td>#1042</td><td>2026-10-28</td><td><span class="badge bg-success">delivered</span></td><td class="fw-bold">$54.99</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>