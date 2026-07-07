<div class="auth-card">
    <div class="auth-logo">
        <i class="fa-solid fa-lock-open"></i>
        <h2>Create New Password</h2>
        <p>Set a secure password for your account</p>
    </div>

    <!-- Session Notifications -->
    <?php if (session()->getFlash('error')): ?>
        <div class="alert alert-danger" role="alert" style="background: rgba(220, 53, 69, 0.15); border-color: rgba(220, 53, 69, 0.25); color: #ea868f; font-size: 0.85rem;">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <?= esc(session()->getFlash('error')) ?>
        </div>
    <?php endif; ?>

    <?php $errors = session()->getFlash('errors', []); ?>

    <form action="/reset-password/<?= esc($token) ?>" method="POST">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" placeholder="Min. 6 characters" required>
            </div>
            <?php if (isset($errors['password'])): ?>
                <div class="text-danger mt-1" style="font-size: 0.75rem;"><?= esc($errors['password'][0]) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-check-double"></i></span>
                <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
            </div>
            <?php if (isset($errors['confirm_password'])): ?>
                <div class="text-danger mt-1" style="font-size: 0.75rem;"><?= esc($errors['confirm_password'][0]) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Save Password</button>
    </form>

    <div class="auth-footer m-0">
        <p>Cancel recovery? <a href="/login">Back to Login</a></p>
    </div>
</div>
