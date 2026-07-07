<div class="row g-4">
    <!-- Left Column: System Configurations -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-gears text-indigo me-2"></i>System Configuration Panel</h5>
            </div>
            <div class="card-body">
                
                <form action="/settings/update" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">Department & Term details</h6>
                        <div class="col-md-6">
                            <label for="dept_name" class="form-label">Department Name</label>
                            <input type="text" class="form-control" id="dept_name" name="dept_name" value="<?= esc($settings['dept_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="academic_year" class="form-label">Academic Year</label>
                            <input type="text" class="form-control" id="academic_year" name="academic_year" placeholder="e.g. 2026-2027" value="<?= esc($settings['academic_year'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="semester" class="form-label">Semester</label>
                            <input type="text" class="form-control" id="semester" name="semester" placeholder="e.g. Semester 1" value="<?= esc($settings['semester'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">Reservation Constraints</h6>
                        <div class="col-md-4">
                            <label for="max_reservation_hours" class="form-label">Max Booking Duration (Hours)</label>
                            <input type="number" class="form-control" id="max_reservation_hours" name="max_reservation_hours" value="<?= esc($settings['max_reservation_hours'] ?? '3') ?>" required>
                            <span class="text-secondary small" style="font-size: 0.75rem;">Limit per single booking.</span>
                        </div>
                        <div class="col-md-4">
                            <label for="max_reservations_per_user" class="form-label">Max Active Bookings (User)</label>
                            <input type="number" class="form-control" id="max_reservations_per_user" name="max_reservations_per_user" value="<?= esc($settings['max_reservations_per_user'] ?? '5') ?>" required>
                            <span class="text-secondary small" style="font-size: 0.75rem;">Limit for Students.</span>
                        </div>
                        <div class="col-md-4">
                            <label for="check_in_expiry_minutes" class="form-label">Check-in Expiry Window (Min)</label>
                            <input type="number" class="form-control" id="check_in_expiry_minutes" name="check_in_expiry_minutes" value="<?= esc($settings['check_in_expiry_minutes'] ?? '15') ?>" required>
                            <span class="text-secondary small" style="font-size: 0.75rem;">Minutes to auto-expire.</span>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">Localization</h6>
                        <div class="col-md-6">
                            <label for="timezone" class="form-label">System Timezone</label>
                            <select class="form-select" id="timezone" name="timezone" required>
                                <option value="Asia/Bangkok" <?= ($settings['timezone'] ?? '') === 'Asia/Bangkok' ? 'selected' : '' ?>>Asia/Bangkok (UTC+7)</option>
                                <option value="Asia/Singapore" <?= ($settings['timezone'] ?? '') === 'Asia/Singapore' ? 'selected' : '' ?>>Asia/Singapore (UTC+8)</option>
                                <option value="UTC" <?= ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC / Greenwich</option>
                                <option value="America/New_York" <?= ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>Eastern Time (US & Canada)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="notification_email_enabled" class="form-label">Notification Email Alerts</label>
                            <select class="form-select" id="notification_email_enabled" name="notification_email_enabled">
                                <option value="0" <?= ($settings['notification_email_enabled'] ?? '0') === '0' ? 'selected' : '' ?>>Disabled (Log Simulated mail.log only)</option>
                                <option value="1" <?= ($settings['notification_email_enabled'] ?? '0') === '1' ? 'selected' : '' ?>>Enabled (Send actual server mail)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">SMTP / Email Configuration (If Enabled)</h6>
                        <div class="col-md-5">
                            <label for="smtp_host" class="form-label">SMTP Server Host</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?= esc($settings['smtp_host'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="smtp_port" class="form-label">SMTP Port</label>
                            <input type="text" class="form-control" id="smtp_port" name="smtp_port" value="<?= esc($settings['smtp_port'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="smtp_encryption" class="form-label">Encryption Protocol</label>
                            <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                <option value="tls" <?= ($settings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (Standard)</option>
                                <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (Legacy)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_user" class="form-label">SMTP Username</label>
                            <input type="text" class="form-control" id="smtp_user" name="smtp_user" value="<?= esc($settings['smtp_user'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_pass" class="form-label">SMTP Password</label>
                            <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" value="<?= esc($settings['smtp_pass'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_from_email" class="form-label">System Sender Email</label>
                            <input type="email" class="form-control" id="smtp_from_email" name="smtp_from_email" value="<?= esc($settings['smtp_from_email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_from_name" class="form-label">Sender Name Alias</label>
                            <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" value="<?= esc($settings['smtp_from_name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end border-top border-secondary border-opacity-10 pt-3">
                        <button type="submit" class="btn btn-indigo px-5 py-2"><i class="fa-solid fa-floppy-disk me-1"></i> Save Configurations</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Right Column: Security Profile & Database Backups -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- Card 1: User Security Password Update -->
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="fw-bold m-0"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Security Profile Settings</h6>
            </div>
            <div class="card-body">
                <form id="change-pass-form">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100 mt-2">Update Password</button>
                </form>
            </div>
        </div>

        <!-- Card 2: Database Backups -->
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="fw-bold m-0"><i class="fa-solid fa-database text-indigo me-2"></i>Database Backups</h6>
            </div>
            <div class="card-body small text-secondary">
                <p>Generate a complete SQL file dump of all tables, schema structures, settings, and reservation logs for restoration or transfer.</p>
                <a href="/settings/backup" class="btn btn-indigo btn-sm w-100 mb-3"><i class="fa-solid fa-download me-1"></i> Download SQL Backup</a>
                
                <hr class="border-secondary opacity-25">
                
                <!-- Restore backup -->
                <form action="/settings/restore" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <label class="form-label text-secondary small">Restore System Database</label>
                    <input type="file" name="backup_file" class="form-control form-control-sm mb-2" accept=".sql" required>
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100"><i class="fa-solid fa-upload me-1"></i> Upload & Restore DB</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Change password handler
        $('#change-pass-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: '/settings/change-password',
                method: 'POST',
                data: {
                    old_password: $('#old_password').val(),
                    new_password: $('#new_password').val(),
                    confirm_new_password: $('#confirm_new_password').val(),
                    csrf_token: '<?= csrf_token() ?>'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Password Updated!',
                        text: response.message,
                        confirmButtonColor: '#6366f1',
                        background: '#111827',
                        color: '#fff'
                    }).then(() => {
                        $('#change-pass-form')[0].reset();
                    });
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON?.error || 'Failed to update password.';
                    if (xhr.status === 422) {
                        errorMsg = '';
                        var errors = xhr.responseJSON.errors;
                        for (var field in errors) {
                            errorMsg += errors[field].join('<br>') + '<br>';
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errorMsg,
                        confirmButtonColor: '#6366f1',
                        background: '#111827',
                        color: '#fff'
                    });
                }
            });
        });
    });
</script>
