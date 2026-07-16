<?php
// Functions to translate role and status names for display
function get_thai_role_name($role_name) {
    $roles_map = [
        'Super Administrator' => 'ผู้ดูแลระบบสูงสุด',
        'Department Administrator' => 'ผู้ดูแลระบบภาควิชา',
        'Lecturer' => 'อาจารย์ผู้สอน',
        'Staff' => 'เจ้าหน้าที่',
        'Student' => 'นักศึกษา'
    ];
    return $roles_map[$role_name] ?? $role_name;
}
?>

<style>
    .form-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .form-card-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .form-card-header h5 {
        color: #ffffff;
        font-weight: 700;
    }
    .form-card-header .btn-outline-light {
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        font-weight: 500;
        border-radius: 10px;
        padding: 6px 14px;
        transition: all 0.2s ease;
    }
    .form-card-header .btn-outline-light:hover {
        background-color: #ffffff !important;
        color: #4f46e5 !important;
        transform: translateY(-1px);
    }
    .section-title {
        position: relative;
        padding-left: 12px;
        color: #4f46e5;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 2px;
        bottom: 2px;
        width: 4px;
        background-color: #6366f1;
        border-radius: 4px;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 6px;
    }
    .form-control, .form-select {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.92rem;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        background-color: #ffffff;
    }
    .input-group-icon {
        position: relative;
    }
    .input-group-icon i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .input-group-icon .form-control {
        padding-left: 40px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card form-card mb-4">
            <div class="form-card-header">
                <h5 class="m-0"><i class="fa-solid fa-pen-to-square me-2"></i>แก้ไขรายละเอียดบัญชีผู้ใช้งาน</h5>
                <a href="/users" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> กลับไปยังหน้ารายการ</a>
            </div>
            <div class="card-body p-4">
                
                <?php $errors = session()->getFlash('errors', []); ?>

                <form action="/users/update/<?= $targetUser['id'] ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- Section: Personal Info -->
                    <div class="row g-3 mb-5">
                        <div class="col-12">
                            <h6 class="section-title">ข้อมูลส่วนตัว</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">ชื่อจริง <span class="text-danger">*</span></label>
                            <div class="input-group-icon">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= esc($targetUser['first_name']) ?>" required>
                            </div>
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['first_name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <div class="input-group-icon">
                                <i class="fa-regular fa-user"></i>
                                <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= esc($targetUser['last_name']) ?>" required>
                            </div>
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['last_name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">ที่อยู่อีเมล <span class="text-danger">*</span></label>
                            <div class="input-group-icon">
                                <i class="fa-regular fa-envelope"></i>
                                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= esc($targetUser['email']) ?>" required>
                            </div>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['email'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="phone_number" class="form-label">เบอร์โทรศัพท์ (ถ้ามี)</label>
                            <div class="input-group-icon">
                                <i class="fa-solid fa-phone"></i>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= esc($targetUser['phone_number']) ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Role, ID & Status -->
                    <div class="row g-3 mb-5">
                        <div class="col-12">
                            <h6 class="section-title">บทบาทและสถานะบัญชี</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="role_id" class="form-label">บทบาทในระบบ <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['role_id']) ? 'is-invalid' : '' ?>" id="role_id" name="role_id" required>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= $targetUser['role_id'] == $r['id'] ? 'selected' : '' ?>><?= esc(get_thai_role_name($r['name'])) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['role_id'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['role_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Dynamic Field: Student ID -->
                        <div class="col-md-4" id="student-id-wrapper" style="display: none;">
                            <label for="student_id" class="form-label">รหัสนักศึกษา <span class="text-danger">*</span></label>
                            <div class="input-group-icon">
                                <i class="fa-solid fa-id-card"></i>
                                <input type="text" class="form-control <?= isset($errors['student_id']) ? 'is-invalid' : '' ?>" id="student_id" name="student_id" value="<?= esc($targetUser['student_id']) ?>">
                            </div>
                            <?php if (isset($errors['student_id'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['student_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Dynamic Field: Employee ID -->
                        <div class="col-md-4" id="employee-id-wrapper" style="display: none;">
                            <label for="employee_id" class="form-label">รหัสพนักงาน <span class="text-danger">*</span></label>
                            <div class="input-group-icon">
                                <i class="fa-solid fa-id-card-clip"></i>
                                <input type="text" class="form-control <?= isset($errors['employee_id']) ? 'is-invalid' : '' ?>" id="employee_id" name="employee_id" value="<?= esc($targetUser['employee_id']) ?>">
                            </div>
                            <?php if (isset($errors['employee_id'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['employee_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label">สถานะบัญชี</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= $targetUser['status'] === 'active' ? 'selected' : '' ?>>ใช้งานอยู่</option>
                                <option value="inactive" <?= $targetUser['status'] === 'inactive' ? 'selected' : '' ?>>ปิดใช้งาน</option>
                                <option value="suspended" <?= $targetUser['status'] === 'suspended' ? 'selected' : '' ?>>ระงับการใช้งาน</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section: Change Password -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="section-title">เปลี่ยนรหัสผ่าน (เว้นว่างไว้หากไม่ต้องการเปลี่ยน)</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">รหัสผ่านใหม่</label>
                            <div class="input-group-icon">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" class="form-control" id="password" name="password" placeholder="ระบุรหัสผ่านใหม่ (ขั้นต่ำ 6 ตัวอักษร)">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top border-secondary border-opacity-10 pt-4 mt-5">
                        <a href="/users" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 10px;">ยกเลิก</a>
                        <button type="submit" class="btn btn-indigo px-4 py-2" style="border-radius: 10px;"><i class="fa-solid fa-save me-1"></i> บันทึกการเปลี่ยนแปลง</button>
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
