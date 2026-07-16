<style>
    .settings-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .settings-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
    }
    .settings-header h5, .settings-header h6 {
        color: #ffffff;
        font-weight: 700;
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
    .hr-divider {
        margin: 28px 0;
        border-color: #e2e8f0;
        opacity: 0.8;
    }
</style>

<div class="row g-4">
    <!-- Left Column: System Configurations -->
    <div class="col-lg-8">
        <div class="card settings-card mb-4">
            <div class="settings-header">
                <h5 class="m-0"><i class="fa-solid fa-gears me-2"></i>แผงตั้งค่าการทำงานระบบ</h5>
            </div>
            <div class="card-body p-4">
                
                <form action="/settings/update" method="POST">
                    <?= csrf_field() ?>

                    <!-- Section: Department & Term -->
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="section-title">ข้อมูลโครงสร้างหลักสูตรและปีการศึกษา</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="dept_name" class="form-label">ชื่อภาควิชา / คณะ</label>
                            <input type="text" class="form-control" id="dept_name" name="dept_name" value="<?= esc($settings['dept_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="academic_year" class="form-label">ปีการศึกษา</label>
                            <input type="text" class="form-control" id="academic_year" name="academic_year" placeholder="เช่น 2569-2570" value="<?= esc($settings['academic_year'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="semester" class="form-label">ภาคการศึกษา</label>
                            <input type="text" class="form-control" id="semester" name="semester" placeholder="เช่น ภาคการศึกษาที่ 1" value="<?= esc($settings['semester'] ?? '') ?>" required>
                        </div>
                    </div>

                    <hr class="hr-divider">

                    <!-- Section: Reservation Constraints -->
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="section-title">เงื่อนไขและข้อจำกัดการจองเครื่อง</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="max_reservation_hours" class="form-label">ชั่วโมงการจองสูงสุดต่อครั้ง (ชั่วโมง)</label>
                            <input type="number" class="form-control" id="max_reservation_hours" name="max_reservation_hours" value="<?= esc($settings['max_reservation_hours'] ?? '3') ?>" required>
                            <span class="text-secondary small d-block mt-1" style="font-size: 0.75rem; line-height: 1.3;">จำกัดเวลาใช้งานสูงสุดในการส่งคำขอจอง 1 รายการ</span>
                        </div>
                        <div class="col-md-4">
                            <label for="max_reservations_per_user" class="form-label">สิทธิ์การจองค้างในระบบสูงสุด (ต่อคน)</label>
                            <input type="number" class="form-control" id="max_reservations_per_user" name="max_reservations_per_user" value="<?= esc($settings['max_reservations_per_user'] ?? '5') ?>" required>
                            <span class="text-secondary small d-block mt-1" style="font-size: 0.75rem; line-height: 1.3;">โควตาการจองที่อยู่ระหว่างรอดำเนินการของนักศึกษา</span>
                        </div>
                        <div class="col-md-4">
                            <label for="check_in_expiry_minutes" class="form-label">เวลาหมดเขตการเข้าเช็คอิน (นาที)</label>
                            <input type="number" class="form-control" id="check_in_expiry_minutes" name="check_in_expiry_minutes" value="<?= esc($settings['check_in_expiry_minutes'] ?? '15') ?>" required>
                            <span class="text-secondary small d-block mt-1" style="font-size: 0.75rem; line-height: 1.3;">หากไม่เช็คอินในเวลาที่กำหนด ระบบจะยกเลิกการจองอัตโนมัติ</span>
                        </div>
                    </div>

                    <hr class="hr-divider">

                    <!-- Section: Localization & Notification Mode -->
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="section-title">เขตเวลาและระบบแจ้งเตือน</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="timezone" class="form-label">เขตเวลาของระบบ (Timezone)</label>
                            <select class="form-select" id="timezone" name="timezone" required>
                                <option value="Asia/Bangkok" <?= ($settings['timezone'] ?? '') === 'Asia/Bangkok' ? 'selected' : '' ?>>เอเชีย/กรุงเทพฯ (UTC+7)</option>
                                <option value="Asia/Singapore" <?= ($settings['timezone'] ?? '') === 'Asia/Singapore' ? 'selected' : '' ?>>เอเชีย/สิงคโปร์ (UTC+8)</option>
                                <option value="UTC" <?= ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC / กรีนิช</option>
                                <option value="America/New_York" <?= ($settings['timezone'] ?? '') === 'America/New_York' ? 'selected' : '' ?>>Eastern Time (สหรัฐฯ & แคนาดา)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="notification_email_enabled" class="form-label">ระบบส่งอีเมลแจ้งเตือนการจอง</label>
                            <select class="form-select" id="notification_email_enabled" name="notification_email_enabled">
                                <option value="0" <?= ($settings['notification_email_enabled'] ?? '0') === '0' ? 'selected' : '' ?>>ปิดการใช้งาน (จำลองการบันทึกลงไฟล์ mail.log เท่านั้น)</option>
                                <option value="1" <?= ($settings['notification_email_enabled'] ?? '0') === '1' ? 'selected' : '' ?>>เปิดการใช้งาน (ส่งเมลจริงผ่าน SMTP เซิร์ฟเวอร์)</option>
                            </select>
                        </div>
                    </div>

                    <hr class="hr-divider">

                    <!-- Section: SMTP Config -->
                    <div class="row g-3">
                        <div class="col-12">
                            <h6 class="section-title">การกำหนดค่าอีเมลเซิร์ฟเวอร์ SMTP (หากเปิดใช้งานการส่งเมลจริง)</h6>
                        </div>
                        <div class="col-md-5">
                            <label for="smtp_host" class="form-label">ที่อยู่เซิร์ฟเวอร์ SMTP Host</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host" placeholder="เช่น smtp.mailtrap.io" value="<?= esc($settings['smtp_host'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="smtp_port" class="form-label">พอร์ต SMTP Port</label>
                            <input type="text" class="form-control" id="smtp_port" name="smtp_port" placeholder="เช่น 2525" value="<?= esc($settings['smtp_port'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="smtp_encryption" class="form-label">โปรโตคอลการเข้ารหัส</label>
                            <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                <option value="tls" <?= ($settings['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (มาตรฐานปัจจุบัน)</option>
                                <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (แบบเก่า)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_user" class="form-label">ชื่อผู้ใช้ SMTP Username</label>
                            <input type="text" class="form-control" id="smtp_user" name="smtp_user" value="<?= esc($settings['smtp_user'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_pass" class="form-label">รหัสผ่าน SMTP Password</label>
                            <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" value="<?= esc($settings['smtp_pass'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_from_email" class="form-label">อีเมลผู้ส่งของระบบ (Sender Email)</label>
                            <input type="email" class="form-control" id="smtp_from_email" name="smtp_from_email" placeholder="เช่น noreply@labreserve.edu" value="<?= esc($settings['smtp_from_email'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="smtp_from_name" class="form-label">ชื่อผู้ส่ง (Sender Alias)</label>
                            <input type="text" class="form-control" id="smtp_from_name" name="smtp_from_name" placeholder="เช่น ระบบจองห้องแล็บคอมพิวเตอร์" value="<?= esc($settings['smtp_from_name'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end border-top border-secondary border-opacity-10 pt-4 mt-5">
                        <button type="submit" class="btn btn-indigo px-5 py-2" style="border-radius: 10px;"><i class="fa-solid fa-floppy-disk me-1"></i> บันทึกการตั้งค่าระบบ</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Right Column: Security Profile & Database Backups -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <!-- Card 1: User Security Password Update -->
        <div class="card settings-card">
            <div class="settings-header">
                <h6 class="m-0"><i class="fa-solid fa-shield-halved me-2"></i>ตั้งค่าความปลอดภัยบัญชี</h6>
            </div>
            <div class="card-body p-4">
                <form id="change-pass-form">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">รหัสผ่านปัจจุบัน <span class="text-danger">*</span></label>
                        <div class="input-group-icon">
                            <i class="fa-solid fa-key text-muted"></i>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <div class="input-group-icon">
                            <i class="fa-solid fa-lock text-muted"></i>
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="ขั้นต่ำ 6 ตัวอักษร" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <div class="input-group-icon">
                            <i class="fa-solid fa-lock text-muted"></i>
                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" placeholder="พิมพ์ซ้ำรหัสผ่านใหม่" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-indigo btn-sm w-100 mt-3 py-2" style="border-radius: 8px;">อัปเดตรหัสผ่าน</button>
                </form>
            </div>
        </div>

        <!-- Card 2: Database Backups -->
        <div class="card settings-card">
            <div class="settings-header">
                <h6 class="m-0"><i class="fa-solid fa-database me-2"></i>สำรองและกู้คืนข้อมูลระบบ</h6>
            </div>
            <div class="card-body p-4 small text-secondary">
                <p style="font-size: 0.85rem; line-height: 1.5; margin-bottom: 16px;">
                    สร้างและดาวน์โหลดไฟล์ SQL สำรองของระบบ ซึ่งประกอบด้วยข้อมูลโครงสร้างตารางข้อมูล สิทธิ์การเข้าใช้งาน ค่ากำหนดระบบ และบันทึกประวัติการจองทั้งหมดสำหรับนำไปเก็บบันทึกหรือย้ายฐานข้อมูล
                </p>
                <a href="/settings/backup" class="btn btn-indigo btn-sm w-100 mb-3 py-2" style="border-radius: 8px;"><i class="fa-solid fa-download me-1"></i> ดาวน์โหลดไฟล์ SQL สำรอง</a>
                
                <hr class="border-secondary opacity-25 my-4">
                
                <!-- Restore backup -->
                <form action="/settings/restore" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <label class="form-label text-secondary small fw-semibold">กู้คืนฐานข้อมูลระบบจากไฟล์ SQL</label>
                    <input type="file" name="backup_file" class="form-control form-control-sm mb-3 bg-light" accept=".sql" required style="border-radius: 8px;">
                    <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-2" style="border-radius: 8px;"><i class="fa-solid fa-upload me-1"></i> อัปโหลดและกู้คืน DB</button>
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
                        title: 'อัปเดตรหัสผ่านสำเร็จแล้ว!',
                        text: 'รหัสผ่านของคุณได้รับการเปลี่ยนเรียบร้อยแล้ว',
                        confirmButtonColor: '#6366f1',
                        background: '#ffffff',
                        color: '#1e293b'
                    }).then(() => {
                        $('#change-pass-form')[0].reset();
                    });
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON?.error || 'ไม่สามารถอัปเดตรหัสผ่านได้';
                    if (xhr.status === 422) {
                        errorMsg = '';
                        var errors = xhr.responseJSON.errors;
                        for (var field in errors) {
                            errorMsg += errors[field].join('<br>') + '<br>';
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        html: errorMsg,
                        confirmButtonColor: '#6366f1',
                        background: '#ffffff',
                        color: '#1e293b'
                    });
                }
            });
        });
    });
</script>
