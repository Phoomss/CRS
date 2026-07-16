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

function get_thai_status_name($status) {
    $status_map = [
        'active' => 'ใช้งานอยู่',
        'inactive' => 'ปิดใช้งาน',
        'suspended' => 'ระงับการใช้งาน'
    ];
    return $status_map[strtolower($status)] ?? $status;
}
?>

<style>
    .user-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .user-card-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .user-card-header h5 {
        color: #ffffff;
        font-weight: 700;
    }
    .user-card-header .btn-primary {
        background-color: #ffffff !important;
        color: #4f46e5 !important;
        border: none !important;
        font-weight: 600;
        border-radius: 10px;
        padding: 8px 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    .user-card-header .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }
    .user-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.8px;
        background-color: #f8fafc !important;
        color: #64748b;
        padding: 18px 20px;
        border-bottom: 2px solid #e2e8f0;
    }
    .user-table td {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #334155;
    }
    .user-table tbody tr {
        transition: all 0.25s ease;
    }
    .user-table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.015) !important;
        transform: translateY(-1px);
    }
    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }
    .avatar-status {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 11px;
        height: 11px;
        border-radius: 50%;
        border: 2px solid #fff;
    }
    .status-active { background-color: #10b981; }
    .status-inactive { background-color: #64748b; }
    .status-suspended { background-color: #ef4444; }
    
    .badge-role {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .role-super-admin { background-color: rgba(139, 92, 246, 0.1); color: #7c3aed; }
    .role-dept-admin { background-color: rgba(59, 130, 246, 0.1); color: #2563eb; }
    .role-lecturer { background-color: rgba(20, 184, 166, 0.1); color: #0d9488; }
    .role-staff { background-color: rgba(245, 158, 11, 0.1); color: #d97706; }
    .role-student { background-color: rgba(99, 102, 241, 0.1); color: #4f46e5; }

    .badge-status-thai {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
    }
    .status-active-badge { background-color: rgba(16, 185, 129, 0.12); color: #059669; }
    .status-inactive-badge { background-color: rgba(100, 116, 139, 0.12); color: #475569; }
    .status-suspended-badge { background-color: rgba(239, 68, 68, 0.12); color: #dc2626; }

    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .action-btn:hover {
        transform: scale(1.08);
    }
    .btn-edit { background-color: rgba(99, 102, 241, 0.08); color: #4f46e5; border: none; }
    .btn-edit:hover { background-color: #4f46e5; color: white; }
    .btn-delete { background-color: rgba(239, 68, 68, 0.08); color: #dc2626; border: none; }
    .btn-delete:hover { background-color: #dc2626; color: white; }

    .search-filter-card {
        border-radius: 12px;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 20px;
        margin-bottom: 24px;
    }
</style>

<div class="card user-card mb-4">
    <div class="user-card-header">
        <h5 class="m-0"><i class="fa-solid fa-users me-2"></i>จัดการบัญชีผู้ใช้งาน</h5>
        <a href="/users/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> เพิ่มบัญชีผู้ใช้งานใหม่</a>
    </div>
    <div class="card-body p-4">
        
        <!-- Filter Form -->
        <form action="/users" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-secondary small fw-medium">ค้นหาข้อมูล</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="ชื่อ, อีเมล, รหัสนักศึกษา, รหัสพนักงาน..." value="<?= esc($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-medium">บทบาทผู้ใช้</label>
                <select name="role_id" class="form-select bg-light">
                    <option value="">ทุกบทบาท</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($filters['role_id'] ?? '') == $r['id'] ? 'selected' : '' ?>><?= esc(get_thai_role_name($r['name'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-medium">สถานะบัญชี</label>
                <select name="status" class="form-select bg-light">
                    <option value="">ทุกสถานะ</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>ใช้งานอยู่</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>ปิดใช้งาน</option>
                    <option value="suspended" <?= ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>ระงับการใช้งาน</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-indigo flex-grow-1"><i class="fa-solid fa-filter me-1"></i> กรองข้อมูล</button>
                <a href="/users" class="btn btn-outline-secondary" title="ล้างการค้นหา"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive table-container">
            <table class="table user-table align-middle m-0" id="users-table">
                <thead>
                    <tr>
                        <th>รายละเอียดบัญชีผู้ใช้</th>
                        <th>รหัสประจำตัว</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>บทบาทระบบ</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-end">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">ไม่พบบัญชีผู้ใช้งานที่ตรงกับเงื่อนไขการค้นหา</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-wrapper">
                                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($u['first_name'] . ' ' . $u['last_name']) ?>&background=6366f1&color=fff&bold=true" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px;">
                                            <span class="avatar-status status-<?= strtolower($u['status']) ?>"></span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= esc($u['first_name']) ?> <?= esc($u['last_name']) ?></div>
                                            <div class="text-secondary small"><?= esc($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($u['student_id']): ?>
                                        <div class="small text-muted">รหัสนักศึกษา:</div>
                                        <span class="badge bg-secondary-subtle text-secondary fw-semibold" style="font-size: 0.8rem;"><?= esc($u['student_id']) ?></span>
                                    <?php elseif ($u['employee_id']): ?>
                                        <div class="small text-muted">รหัสพนักงาน:</div>
                                        <span class="badge bg-secondary-subtle text-secondary fw-semibold" style="font-size: 0.8rem;"><?= esc($u['employee_id']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($u['phone_number'] ?? '-') ?></td>
                                <td>
                                    <?php
                                        $roleClass = 'role-student';
                                        $roleVal = strtolower($u['role_name']);
                                        if (strpos($roleVal, 'super') !== false) $roleClass = 'role-super-admin';
                                        elseif (strpos($roleVal, 'department') !== false) $roleClass = 'role-dept-admin';
                                        elseif (strpos($roleVal, 'lecturer') !== false) $roleClass = 'role-lecturer';
                                        elseif (strpos($roleVal, 'staff') !== false) $roleClass = 'role-staff';
                                    ?>
                                    <span class="badge-role <?= $roleClass ?>">
                                        <i class="fa-solid <?= $roleClass === 'role-student' ? 'fa-graduation-cap' : ($roleClass === 'role-lecturer' ? 'fa-chalkboard-user' : ($roleClass === 'role-staff' ? 'fa-briefcase' : 'fa-user-shield')) ?>"></i>
                                        <?= esc(get_thai_role_name($u['role_name'])) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $statusClass = 'status-active-badge';
                                        if ($u['status'] === 'inactive') $statusClass = 'status-inactive-badge';
                                        if ($u['status'] === 'suspended') $statusClass = 'status-suspended-badge';
                                    ?>
                                    <span class="badge-status-thai <?= $statusClass ?>">
                                        <?= esc(get_thai_status_name($u['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/users/edit/<?= $u['id'] ?>" class="action-btn btn-edit" title="แก้ไขข้อมูลผู้ใช้"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <button class="action-btn btn-delete btn-delete-user" data-id="<?= $u['id'] ?>" title="ลบบัญชีผู้ใช้"><i class="fa-regular fa-trash-can"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination pagination-dark justify-content-center m-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>"><i class="fa-solid fa-chevron-left"></i></a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1])) ?>"><i class="fa-solid fa-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.btn-delete-user').on('click', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'ต้องการลบบัญชีผู้ใช้นี้?',
                text: "คุณแน่ใจหรือไม่ว่าต้องการลบบัญชีผู้ใช้นี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ยืนยันการลบ!',
                cancelButtonText: 'ยกเลิก',
                background: '#ffffff',
                color: '#1e293b'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/users/delete/' + id,
                        method: 'POST',
                        data: { csrf_token: '<?= csrf_token() ?>' },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบข้อมูลสำเร็จ',
                                text: 'ลบบัญชีผู้ใช้งานเรียบร้อยแล้ว',
                                confirmButtonColor: '#6366f1',
                                background: '#ffffff',
                                color: '#1e293b'
                            }).then(() => { window.location.reload(); });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่สามารถลบได้',
                                text: xhr.responseJSON?.error || 'เกิดข้อผิดพลาดในการลบข้อมูลผู้ใช้',
                                confirmButtonColor: '#6366f1',
                                background: '#ffffff',
                                color: '#1e293b'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
