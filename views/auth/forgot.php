<div class="auth-card">
    <div class="auth-logo">
        <i class="fa-solid fa-key"></i>
        <h2>Reset Password</h2>
        <p>Recover your LabReserve account access</p>
    </div>

    <!-- Session Notifications -->
    <?php if (session()->getFlash('success')): ?>
        <div class="alert alert-success" role="alert" style="background: rgba(40, 167, 69, 0.2); border-color: rgba(40, 167, 69, 0.3); color: #2ecc71; font-size: 0.85rem;">
            <?= session()->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlash('error')): ?>
        <div class="alert alert-danger" role="alert" style="background: rgba(220, 53, 69, 0.15); border-color: rgba(220, 53, 69, 0.25); color: #ea868f; font-size: 0.85rem;">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <?= esc(session()->getFlash('error')) ?>
        </div>
    <?php endif; ?>

    <?php $errors = session()->getFlash('errors', []); ?>

    <p style="font-size: 0.85rem; color: var(--text-secondary); text-align: center; margin-bottom: 25px;">
        Enter the email address associated with your laboratory account and we will simulate generating a recovery link for you.
    </p>

    <form action="/forgot-password" method="POST">
        <?= csrf_field() ?>

        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" placeholder="name@lab.edu" required>
            </div>
            <?php if (isset($errors['email'])): ?>
                <div class="text-danger mt-1" style="font-size: 0.75rem;"><?= esc($errors['email'][0]) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Send Reset Instructions</button>
    </form>

    <div class="auth-footer m-0">
        <p>Remembered your password? <a href="/login">Back to Sign In</a></p>
    </div>
</div>
