<div class="auth-card">
    <div class="auth-logo">
        <i class="fa-solid fa-desktop"></i>
        <h2>LabReserve</h2>
        <p>Computer Booking System</p>
    </div>

    <!-- Session Notifications -->
    <?php if (session()->getFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background: rgba(40, 167, 69, 0.2); border-color: rgba(40, 167, 69, 0.3); color: #2ecc71; font-size: 0.85rem;">
            <?= session()->getFlash('success') ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background: rgba(220, 53, 69, 0.15); border-color: rgba(220, 53, 69, 0.25); color: #ea868f; font-size: 0.85rem;">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <?= esc(session()->getFlash('error')) ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php 
        $errors = session()->getFlash('errors', []); 
        $old = session()->getFlash('old_inputs', []);
    ?>

    <form action="/login" method="POST">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= esc($old['email'] ?? '') ?>" placeholder="name@lab.edu" required>
            </div>
            <?php if (isset($errors['email'])): ?>
                <div class="text-danger mt-1" style="font-size: 0.75rem;"><?= esc($errors['email'][0]) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label for="password" class="form-label m-0">Password</label>
                <a href="/forgot-password" style="font-size: 0.8rem; color: var(--accent-color); text-decoration: none;">Forgot Password?</a>
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="••••••••" required>
            </div>
            <?php if (isset($errors['password'])): ?>
                <div class="text-danger mt-1" style="font-size: 0.75rem;"><?= esc($errors['password'][0]) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" style="background-color: rgba(15, 23, 42, 0.6); border-color: var(--glass-border);">
            <label class="form-check-label" for="remember_me" style="font-size: 0.85rem; color: var(--text-secondary); user-select: none;">Remember this device</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">Sign In</button>
    </form>
    
    <div class="auth-footer">
        <p>Use default credentials to explore:<br>
        Admin: <code>admin@lab.edu</code> / <code>admin123</code><br>
        Student: <code>student@lab.edu</code> / <code>admin123</code></p>
    </div>
</div>
