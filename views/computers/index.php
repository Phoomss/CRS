<?php
// Function to translate computer status to Thai
function get_thai_computer_status($status) {
    $status_map = [
        'available' => 'ว่างพร้อมใช้งาน',
        'reserved' => 'ถูกจองแล้ว',
        'maintenance' => 'อยู่ระหว่างบำรุงรักษา',
        'offline' => 'ปิดใช้งานชั่วคราว',
        'disabled' => 'งดใช้งาน'
    ];
    return $status_map[strtolower($status)] ?? $status;
}
?>

<style>
    .comp-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .comp-card-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .comp-card-header h5 {
        color: #ffffff;
        font-weight: 700;
    }
    .comp-card-header .btn-outline-light {
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        font-weight: 500;
        border-radius: 10px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }
    .comp-card-header .btn-outline-light:hover {
        background-color: #ffffff !important;
        color: #4f46e5 !important;
        transform: translateY(-1px);
    }
    .comp-card-header .btn-primary {
        background-color: #ffffff !important;
        color: #4f46e5 !important;
        border: none !important;
        font-weight: 600;
        border-radius: 10px;
        padding: 8px 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    .comp-card-header .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }
    .comp-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.8px;
        background-color: #f8fafc !important;
        color: #64748b;
        padding: 18px 20px;
        border-bottom: 2px solid #e2e8f0;
    }
    .comp-table td {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #334155;
    }
    .comp-table tbody tr {
        transition: all 0.25s ease;
    }
    .comp-table tbody tr:hover {
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
    .status-available { background-color: rgba(16, 185, 129, 0.12); color: #059669; }
    .status-reserved { background-color: rgba(59, 130, 246, 0.12); color: #1d4ed8; }
    .status-maintenance { background-color: rgba(245, 158, 11, 0.12); color: #b45309; }
    .status-offline { background-color: rgba(100, 116, 139, 0.12); color: #475569; }
    .status-disabled { background-color: rgba(239, 68, 68, 0.12); color: #dc2626; }

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

<div class="card comp-card mb-4">
    <div class="comp-card-header">
        <h5 class="m-0"><i class="fa-solid fa-display me-2"></i>จัดการเครื่องคอมพิวเตอร์</h5>
        <div class="d-flex gap-2">
            <a href="/computers/print" target="_blank" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-print me-1"></i> พิมพ์ฉลาก QR/Barcode</a>
            <a href="/computers/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> ลงทะเบียนเครื่องใหม่</a>
        </div>
    </div>
    <div class="card-body p-4">
        
        <!-- Filter Form -->
        <form action="/computers" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-secondary small fw-medium">ค้นหาข้อมูล</label>
                <input type="text" name="search" class="form-control bg-light" placeholder="รหัสเครื่อง, สเปค, ยี่ห้อ, IP Address..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-medium">ห้องปฏิบัติการ</label>
                <select name="laboratory_id" class="form-select bg-light">
                    <option value="">ทุกห้องปฏิบัติการ</option>
                    <?php foreach ($labs as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= ($filters['laboratory_id'] ?? '') == $l['id'] ? 'selected' : '' ?>><?= esc($l['code']) ?> - <?= esc($l['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-medium">สถานะเครื่อง</label>
                <select name="status" class="form-select bg-light">
                    <option value="">ทุกสถานะ</option>
                    <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>ว่างพร้อมใช้งาน</option>
                    <option value="reserved" <?= ($filters['status'] ?? '') === 'reserved' ? 'selected' : '' ?>>ถูกจองแล้ว</option>
                    <option value="maintenance" <?= ($filters['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>อยู่ระหว่างบำรุงรักษา</option>
                    <option value="offline" <?= ($filters['status'] ?? '') === 'offline' ? 'selected' : '' ?>>ปิดใช้งานชั่วคราว</option>
                    <option value="disabled" <?= ($filters['status'] ?? '') === 'disabled' ? 'selected' : '' ?>>งดใช้งาน</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-indigo flex-grow-1"><i class="fa-solid fa-filter me-1"></i> กรองข้อมูล</button>
                <a href="/computers" class="btn btn-outline-secondary" title="ล้างค่าการกรอง"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive table-container">
            <table class="table comp-table align-middle m-0" id="computers-table">
                <thead>
                    <tr>
                        <th>รหัสเครื่องคอมฯ</th>
                        <th>ข้อมูลสเปคเครื่อง</th>
                        <th>รหัสครุภัณฑ์</th>
                        <th>ห้องที่ตั้ง</th>
                        <th>ที่อยู่ IP</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-end">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($computers)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">ยังไม่มีเครื่องคอมพิวเตอร์ที่ลงทะเบียนในระบบ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($computers as $c): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= $c['image_path'] ? esc($c['image_path']) : 'https://images.unsplash.com/photo-1547082299-de196ea013d6?auto=format&fit=crop&w=80&q=80' ?>" alt="PC Image" class="rounded" style="width: 45px; height: 45px; object-fit: cover; border: 1px solid #e2e8f0;">
                                        <div>
                                            <span class="fw-bold text-dark"><?= esc($c['code']) ?></span>
                                            <div class="text-secondary small"><?= esc($c['name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold small text-dark"><?= esc($c['brand']) ?> <?= esc($c['model']) ?></div>
                                    <div class="small text-secondary" style="font-size: 0.75rem;"><?= esc($c['cpu']) ?> / <?= esc($c['ram']) ?> / <?= esc($c['storage']) ?></div>
                                </td>
                                <td><span class="badge bg-indigo-subtle text-indigo fw-semibold" style="font-size: 0.8rem;"><?= esc($c['asset_number']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($c['laboratory_code']) ?></div>
                                    <div class="small text-secondary text-truncate" style="max-width: 140px;"><?= esc($c['laboratory_name']) ?></div>
                                </td>
                                <td><code><?= esc($c['ip_address']) ?></code></td>
                                <td class="text-center">
                                    <?php
                                        // Available, Reserved, Maintenance, Offline, Disabled
                                        $statusClass = 'status-available';
                                        $statusVal = strtolower($c['status']);
                                        if ($statusVal === 'reserved') $statusClass = 'status-reserved';
                                        if ($statusVal === 'maintenance') $statusClass = 'status-maintenance';
                                        if ($statusVal === 'offline') $statusClass = 'status-offline';
                                        if ($statusVal === 'disabled') $statusClass = 'status-disabled';
                                    ?>
                                    <span class="badge-status-thai <?= $statusClass ?>">
                                        <?= esc(get_thai_computer_status($c['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/computers/edit/<?= $c['id'] ?>" class="action-btn btn-edit" title="แก้ไขข้อมูลเครื่อง"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <button class="action-btn btn-delete btn-delete-computer" data-id="<?= $c['id'] ?>" title="ลบเครื่องคอมพิวเตอร์"><i class="fa-regular fa-trash-can"></i></button>
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
        $('.btn-delete-computer').on('click', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'ต้องการลบเครื่องคอมพิวเตอร์นี้?',
                text: "คุณแน่ใจหรือไม่ว่าต้องการลบเครื่องคอมพิวเตอร์นี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้",
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
                        url: '/computers/delete/' + id,
                        method: 'POST',
                        data: { csrf_token: '<?= csrf_token() ?>' },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ลบสำเร็จ',
                                text: 'ลบเครื่องคอมพิวเตอร์เรียบร้อยแล้ว',
                                confirmButtonColor: '#6366f1',
                                background: '#ffffff',
                                color: '#1e293b'
                            }).then(() => { window.location.reload(); });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'ลบล้มเหลว',
                                text: xhr.responseJSON?.error || 'ไม่สามารถลบเครื่องคอมพิวเตอร์ได้',
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
