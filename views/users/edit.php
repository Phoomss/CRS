<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Modify User Account Details</h5>
                <a href="/users" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
            </div>
            <div class="card-body">
                
                <?php $errors = session()->getFlash('errors', []); ?>

                <form action="/users/update/<?= $targetUser['id'] ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">Personal Profile</h6>
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= esc($targetUser['first_name']) ?>" required>
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['first_name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= esc($targetUser['last_name']) ?>" required>
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['last_name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= esc($targetUser['email']) ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['email'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">Phone Number (Optional)</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= esc($targetUser['phone_number']) ?>">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">Account Role & Status</h6>
                        <div class="col-md-4">
                            <label for="role_id" class="form-label">System Role</label>
                            <select class="form-select <?= isset($errors['role_id']) ? 'is-invalid' : '' ?>" id="role_id" name="role_id" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= $targetUser['role_id'] == $r['id'] ? 'selected' : '' ?>><?= esc($r['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['role_id'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['role_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Dynamic Field: Student ID -->
                        <div class="col-md-4" id="student-id-wrapper" style="display: none;">
                            <label for="student_id" class="form-label">Student ID</label>
                            <input type="text" class="form-control <?= isset($errors['student_id']) ? 'is-invalid' : '' ?>" id="student_id" name="student_id" value="<?= esc($targetUser['student_id']) ?>">
                            <?php if (isset($errors['student_id'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['student_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Dynamic Field: Employee ID -->
                        <div class="col-md-4" id="employee-id-wrapper" style="display: none;">
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <input type="text" class="form-control <?= isset($errors['employee_id']) ? 'is-invalid' : '' ?>" id="employee_id" name="employee_id" value="<?= esc($targetUser['employee_id']) ?>">
                            <?php if (isset($errors['employee_id'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['employee_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label">Account Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= $targetUser['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $targetUser['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="suspended" <?= $targetUser['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 border-top border-secondary border-opacity-10 pt-3">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">Change Password (Leave blank to keep current)</h6>
                        <div class="col-md-6">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Min. 6 characters">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top border-secondary border-opacity-10 pt-4">
                        <a href="/users" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-save me-1"></i> Save Changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function toggleAcademicIds() {
            var roleId = parseInt($('#role_id').val());
            
            if (roleId === 5) { // Student
                $('#student-id-wrapper').show();
                $('#student_id').prop('required', true);
                $('#employee-id-wrapper').hide();
                $('#employee_id').prop('required', false);
            } else if ([1, 2, 3, 4].includes(roleId)) { // Admin, Lecturer, Staff
                $('#student-id-wrapper').hide();
                $('#student_id').prop('required', false);
                $('#employee-id-wrapper').show();
                $('#employee_id').prop('required', true);
            } else {
                $('#student-id-wrapper').hide();
                $('#student_id').prop('required', false);
                $('#employee-id-wrapper').hide();
                $('#employee_id').prop('required', false);
            }
        }

        $('#role_id').on('change', toggleAcademicIds);
        toggleAcademicIds();
    });
</script>
