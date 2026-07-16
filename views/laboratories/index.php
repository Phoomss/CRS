<?php
// Function to translate laboratory status to Thai
function get_thai_laboratory_status($status) {
    $status_map = [
        'active' => 'เปิดใช้งานอยู่',
        'inactive' => 'ปิดใช้งาน'
    ];
    return $status_map[strtolower($status)] ?? $status;
}
?>

<style>
    .lab-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .lab-card-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .lab-card-header h5 {
        color: #ffffff;
        font-weight: 700;
    }
    .lab-card-header .btn-primary {
        background-color: #ffffff !important;
        color: #4f46e5 !important;
        border: none !important;
        font-weight: 600;
        border-radius: 10px;
        padding: 8px 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    .lab-card-header .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }
    .lab-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.8px;
        background-color: #f8fafc !important;
        color: #64748b;
        padding: 18px 20px;
        border-bottom: 2px solid #e2e8f0;
    }
    .lab-table td {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #334155;
    }
    .lab-table tbody tr {
        transition: all 0.25s ease;
    }
    .lab-table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.015) !important;
        transform: translateY(-1px);
    }
    
    .badge-status-thai {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
    }
    .status-active { background-color: rgba(16, 185, 129, 0.12); color: #059669; }
    .status-inactive { background-color: rgba(239, 68, 68, 0.12); color: #dc2626; }

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
</style>

<div class="card lab-card mb-4">
    <div class="lab-card-header">
        <h5 class="m-0"><i class="fa-solid fa-door-open me-2"></i>จัดการห้องปฏิบัติการ</h5>
        <a href="/laboratories/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> ลงทะเบียนห้องปฏิบัติการใหม่</a>
    </div>
    <div class="card-body p-4">
        
        <!-- Filter Form -->
        <form action="/laboratories" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-5">
                <label class="form-label text-secondary small fw-medium">ค้นหาข้อมูล</label>
                <input type="text" name="search" class="form-control bg-light" placeholder="รหัสห้อง, ชื่อห้องปฏิบัติการ, ที่ตั้งอาคาร..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label text-secondary small fw-medium">สถานะห้องแล็บ</label>
                <select name="status" class="form-select bg-light">
                    <option value="">ทุกสถานะ</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>เปิดใช้งานอยู่</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>ปิดใช้งาน</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-indigo flex-grow-1"><i class="fa-solid fa-filter me-1"></i> กรองข้อมูล</button>
                <a href="/laboratories" class="btn btn-outline-secondary" title="ล้างค่าตัวกรอง"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive table-container">
            <table class="table lab-table align-middle m-0" id="laboratories-table">
                <thead>
                    <tr>
                        <th>รหัสห้องปฏิบัติการ</th>
                        <th>ชื่อห้องปฏิบัติการ</th>
                        <th>สถานที่ตั้ง</th>
                        <th class="text-center">จำนวนเครื่องที่นั่งใช้งาน</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-end">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($labs)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">ยังไม่มีห้องปฏิบัติการคอมพิวเตอร์ที่ลงทะเบียนในระบบ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($labs as $lab): ?>
                            <tr>
                                <td><span class="fw-bold text-dark"><?= esc($lab['code']) ?></span></td>
                                <td><span class="fw-semibold text-dark"><?= esc($lab['name']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($lab['building']) ?></div>
                                    <div class="small text-secondary"><?= esc($lab['floor']) ?></div>
                                </td>
                                <td class="text-center"><span class="badge bg-indigo-subtle text-indigo fw-semibold" style="font-size: 0.85rem;"><?= $lab['capacity'] ?> เครื่อง</span></td>
                                <td class="text-center">
                                    <?php
                                        $statusClass = $lab['status'] === 'active' ? 'status-active' : 'status-inactive';
                                    ?>
                                    <span class="badge-status-thai <?= $statusClass ?>">
                                        <?= esc(get_thai_laboratory_status($lab['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/laboratories/edit/<?= $lab['id'] ?>" class="action-btn btn-edit" title="แก้ไขข้อมูลห้องแล็บ"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <button class="action-btn btn-delete btn-delete-lab" data-id="<?= $lab['id'] ?>" title="ลบห้องปฏิบัติการ"><i class="fa-regular fa-trash-can"></i></button>
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
                <ul class="pagination justify-content-center m-0">
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
        $('.btn-delete-lab').on('click', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'ต้องการลบห้องปฏิบัติการนี้?',
                text: "คุณแน่ใจหรือไม่ว่าต้องการลบห้องปฏิบัติการนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้",
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
                        url: '/laboratories/delete/' + id,
                        method: 'POST',
                        data: { csrf_token: '<?= csrf_token() ?>' },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: 'ลบห้องปฏิบัติการคอมพิวเตอร์เรียบร้อยแล้ว',
                                confirmButtonColor: '#6366f1',
                                background: '#ffffff',
                                color: '#1e293b'
                            }).then(() => { window.location.reload(); });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'ระงับการลบข้อมูล',
                                text: xhr.responseJSON?.error || 'เกิดข้อผิดพลาดในการลบห้องปฏิบัติการ',
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
