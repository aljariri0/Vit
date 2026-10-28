<div class="row g-4">
    <div class="col-md-6">
        <div class="card-clean p-4">
            <h4 class="fw-bold mb-3">Sign In</h4>
            <form action="/auth/login" method="POST">
                <?= csrf_field() ?>
                <input type="email" name="email" class="form-control mb-2" placeholder="Email Address" required>
                <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_admin" id="adminCheck">
                    <label class="form-check-label small" for="adminCheck">Sign in as Admin (`admins` table)</label>
                </div>
                <button class="btn btn-vite w-100 py-2 mb-2">Sign In</button>
            </form>
            <a href="/auth/google" class="btn btn-outline-danger rounded-pill w-100"><i class="fa-brands fa-google me-2"></i>Continue with Google</a>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-clean p-4">
            <h4 class="fw-bold mb-3">New Customer Registration</h4>
            <form action="/auth/register" method="POST">
                <?= csrf_field() ?>
                <input type="text" name="name" class="form-control mb-2" placeholder="Full Name" required>
                <input type="email" name="email" class="form-control mb-2" placeholder="Email Address" required>
                <input type="text" name="phone" class="form-control mb-2" placeholder="Phone Number">
                <input type="text" name="city" class="form-control mb-2" placeholder="City">
                <input type="text" name="address" class="form-control mb-2" placeholder="Shipping Address">
                <input type="password" name="password" class="form-control mb-3" placeholder="Create Password" required>
                <button class="btn btn-dark rounded-pill w-100 py-2">Create Account</button>
            </form>
        </div>
    </div>
</div>