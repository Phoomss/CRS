<style>
    .profile-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .profile-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
    }
    .profile-header h5 {
        color: #ffffff;
        font-weight: 700;
        margin: 0;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 6px;
    }
    .form-control {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.92rem;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }
    .form-control:focus {
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
    <div class="col-lg-6 col-md-8">
        <div class="card profile-card">
            <div class="profile-header">
                <h5><i class="fa-solid fa-shield-halved me-2"></i>ตั้งค่าความปลอดภัยบัญชี</h5>
            </div>
            <div class="card-body p-4">
                <form id="change-pass-form">
                    <div class="mb-3">
                        <label for="old_password" class="form-label">รหัสผ่านปัจจุบัน <span class="text-danger">*</span></label>
                        <div class="input-group-icon">
                            <i class="fa-solid fa-key"></i>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <div class="input-group-icon">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="ขั้นต่ำ 6 ตัวอักษร" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <div class="input-group-icon">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" placeholder="พิมพ์ซ้ำรหัสผ่านใหม่" required>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 border-top border-secondary border-opacity-10 pt-4 mt-4">
                        <a href="/dashboard" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 10px;">ยกเลิก</a>
                        <button type="submit" class="btn btn-indigo px-4 py-2" style="border-radius: 10px;">อัปเดตรหัสผ่าน</button>
                    </div>
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
