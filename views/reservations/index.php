<?php
// Helper to get Thai status display
function get_thai_status_info($status_id) {
    $statuses = [
        1 => ['name' => 'รออนุมัติ', 'class' => 'bg-warning-subtle text-warning-emphasis border border-warning'],
        2 => ['name' => 'อนุมัติแล้ว', 'class' => 'bg-info-subtle text-info-emphasis border border-info'],
        3 => ['name' => 'ปฏิเสธคำขอ', 'class' => 'bg-danger-subtle text-danger-emphasis border border-danger'],
        4 => ['name' => 'ยกเลิกแล้ว', 'class' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary'],
        5 => ['name' => 'เสร็จสิ้น', 'class' => 'bg-success-subtle text-success-emphasis border border-success'],
        6 => ['name' => 'หมดเวลาเช็คอิน', 'class' => 'bg-light-subtle text-muted border border-secondary-subtle']
    ];
    return $statuses[$status_id] ?? ['name' => 'ไม่ระบุ', 'class' => 'bg-light text-dark'];
}
?>

<style>
    .res-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .res-card-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .res-card-header h5 {
        color: #ffffff;
        font-weight: 700;
    }
    .res-card-header .btn-primary {
        background-color: #ffffff !important;
        color: #4f46e5 !important;
        border: none !important;
        font-weight: 600;
        border-radius: 10px;
        padding: 8px 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    .res-card-header .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }
    .table-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }
    .res-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.8px;
        background-color: #f8fafc !important;
        color: #64748b;
        padding: 18px 20px;
        border-bottom: 2px solid #e2e8f0;
    }
    .res-table td {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #334155;
    }
    .res-table tbody tr {
        transition: all 0.25s ease;
    }
    .res-table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.015) !important;
        transform: translateY(-1px);
    }
    .badge-status-custom {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-block;
    }
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
    .btn-view { background-color: rgba(99, 102, 241, 0.08); color: #4f46e5; border: none; }
    .btn-view:hover { background-color: #4f46e5; color: white; }
    .btn-checkin-action { background-color: rgba(16, 185, 129, 0.1); color: #059669; border: none; }
    .btn-checkin-action:hover { background-color: #059669; color: white; }
    .btn-checkout-action { background-color: rgba(245, 158, 11, 0.1); color: #d97706; border: none; }
    .btn-checkout-action:hover { background-color: #d97706; color: white; }
    .btn-cancel-action { background-color: rgba(239, 68, 68, 0.08); color: #dc2626; border: none; }
    .btn-cancel-action:hover { background-color: #dc2626; color: white; }
</style>

<div class="card res-card mb-4">
    <div class="res-card-header">
        <h5 class="m-0"><i class="fa-regular fa-calendar-check me-2"></i>รายการจองเครื่องคอมพิวเตอร์ทั้งหมด</h5>
        <?php if (has_permission('create_reservations')): ?>
            <a href="/reservations/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> จองเครื่องคอมพิวเตอร์</a>
        <?php endif; ?>
    </div>
    <div class="card-body p-4">
        <!-- Filter Form -->
        <form action="/reservations" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-secondary small fw-medium">ค้นหาข้อมูล</label>
                <input type="text" name="search" class="form-control bg-light" placeholder="ผู้จอง, อีเมล, รหัสเครื่อง..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small fw-medium">ห้องปฏิบัติการ</label>
                <select name="laboratory_id" class="form-select bg-light">
                    <option value="">ทุกห้องปฏิบัติการ</option>
                    <?php foreach ($labs as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= ($filters['laboratory_id'] ?? '') == $l['id'] ? 'selected' : '' ?>><?= esc($l['code']) ?> - <?= esc($l['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small fw-medium">สถานะการจอง</label>
                <select name="status_id" class="form-select bg-light">
                    <option value="">ทุกสถานะ</option>
                    <option value="1" <?= ($filters['status_id'] ?? '') == 1 ? 'selected' : '' ?>>รออนุมัติ</option>
                    <option value="2" <?= ($filters['status_id'] ?? '') == 2 ? 'selected' : '' ?>>อนุมัติแล้ว</option>
                    <option value="3" <?= ($filters['status_id'] ?? '') == 3 ? 'selected' : '' ?>>ปฏิเสธคำขอ</option>
                    <option value="4" <?= ($filters['status_id'] ?? '') == 4 ? 'selected' : '' ?>>ยกเลิกแล้ว</option>
                    <option value="5" <?= ($filters['status_id'] ?? '') == 5 ? 'selected' : '' ?>>เสร็จสิ้น</option>
                    <option value="6" <?= ($filters['status_id'] ?? '') == 6 ? 'selected' : '' ?>>หมดเวลาเช็คอิน</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small fw-medium">วันที่จอง</label>
                <input type="date" name="date" class="form-control bg-light" value="<?= esc($filters['date'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-indigo flex-grow-1"><i class="fa-solid fa-filter me-1"></i> กรองข้อมูล</button>
                <a href="/reservations" class="btn btn-outline-secondary" title="ล้างตัวกรอง"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive table-container">
            <table class="table res-table align-middle m-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">รหัสจอง</th>
                        <th>ผู้จองบัญชี</th>
                        <th>ห้องปฏิบัติการ</th>
                        <th>เครื่องคอมฯ</th>
                        <th>กำหนดเวลาใช้งาน</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-end">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">ไม่พบรายการจองที่ตรงกับเงื่อนไขการค้นหา</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $r): ?>
                            <tr>
                                <td><span class="text-secondary small fw-bold">#<?= $r['id'] ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></div>
                                    <div class="small text-secondary"><?= esc($r['email']) ?></div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark"><?= esc($r['laboratory_code']) ?></span>
                                </td>
                                <td>
                                    <?php if ($r['computer_code']): ?>
                                        <span class="badge bg-indigo-subtle text-indigo fw-semibold" style="font-size: 0.8rem;"><?= esc($r['computer_code']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">ไม่ได้เลือก</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark"><?= date('M d, Y', strtotime($r['start_time'])) ?></div>
                                    <div class="small text-secondary"><?= date('H:i A', strtotime($r['start_time'])) ?> - <?= date('H:i A', strtotime($r['end_time'])) ?></div>
                                </td>
                                <td class="text-center">
                                    <?php $statusInfo = get_thai_status_info($r['status_id']); ?>
                                    <span class="badge-status-custom <?= $statusInfo['class'] ?>">
                                        <?= esc($statusInfo['name']) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/reservations/view/<?= $r['id'] ?>" class="action-btn btn-view" title="ดูรายละเอียดการจอง"><i class="fa-solid fa-eye"></i></a>
                                        
                                        <?php if ($r['status_id'] == 2 && $r['user_id'] == auth()['id'] && empty($r['check_in_time'])): ?>
                                            <!-- Check In Action -->
                                            <button class="action-btn btn-checkin-action btn-checkin" data-res-id="<?= $r['id'] ?>" data-comp-id="<?= $r['computer_id'] ?>" title="เช็คอินเข้าใช้งานเครื่อง"><i class="fa-solid fa-right-to-bracket"></i></button>
                                        <?php endif; ?>

                                        <?php if ($r['status_id'] == 2 && $r['user_id'] == auth()['id'] && !empty($r['check_in_time']) && empty($r['check_out_time'])): ?>
                                            <!-- Check Out Action -->
                                            <button class="action-btn btn-checkout-action btn-checkout" data-res-id="<?= $r['id'] ?>" data-comp-id="<?= $r['computer_id'] ?>" title="เช็คเอาท์เสร็จสิ้นการใช้งาน"><i class="fa-solid fa-right-from-bracket"></i></button>
                                        <?php endif; ?>

                                        <?php if (in_array($r['status_id'], [1, 2]) && ($r['user_id'] == auth()['id'] || has_role(['Super Administrator', 'Department Administrator', 'Staff']))): ?>
                                            <!-- Cancel Action -->
                                            <button class="action-btn btn-cancel-action btn-cancel-booking" data-id="<?= $r['id'] ?>" title="ยกเลิกการจอง"><i class="fa-solid fa-ban"></i></button>
                                        <?php endif; ?>
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

<!-- Scripts for AJAX Checkin / Checkout / Cancel -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cancel Booking handler
        $('.btn-cancel-booking').on('click', function() {
            var resId = $(this).data('id');
            Swal.fire({
                title: 'ต้องการยกเลิกการจองเครื่อง?',
                text: "คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองเครื่องคอมพิวเตอร์นี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่, ต้องการยกเลิก!',
                cancelButtonText: 'ปิด',
                background: '#ffffff',
                color: '#1e293b'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/reservations/cancel/' + resId,
                        method: 'POST',
                        data: {
                            csrf_token: '<?= csrf_token() ?>'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ยกเลิกสำเร็จ!',
                                text: 'ยกเลิกรายการจองเครื่องเรียบร้อยแล้ว',
                                confirmButtonColor: '#6366f1',
                                background: '#ffffff',
                                color: '#1e293b'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'ข้อผิดพลาด',
                                text: xhr.responseJSON?.error || 'ไม่สามารถยกเลิกการจองได้',
                                confirmButtonColor: '#6366f1',
                                background: '#ffffff',
                                color: '#1e293b'
                            });
                        }
                    });
                }
            });
        });

        // Check In AJAX handler
        $('.btn-checkin').on('click', function() {
            var resId = $(this).data('res-id');
            var compId = $(this).data('comp-id');
            
            $.ajax({
                url: '/reservations/checkin',
                method: 'POST',
                data: {
                    reservation_id: resId,
                    computer_id: compId,
                    csrf_token: '<?= csrf_token() ?>'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'เช็คอินสำเร็จ!',
                        text: 'เช็คอินเข้าใช้งานเครื่องคอมพิวเตอร์เรียบร้อยแล้ว',
                        confirmButtonColor: '#28a745',
                        background: '#ffffff',
                        color: '#1e293b'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เช็คอินล้มเหลว',
                        text: xhr.responseJSON?.error || 'ไม่สามารถเช็คอินได้',
                        confirmButtonColor: '#6366f1',
                        background: '#ffffff',
                        color: '#1e293b'
                    });
                }
            });
        });

        // Check Out AJAX handler
        $('.btn-checkout').on('click', function() {
            var resId = $(this).data('res-id');
            var compId = $(this).data('comp-id');
            
            $.ajax({
                url: '/reservations/checkout',
                method: 'POST',
                data: {
                    reservation_id: resId,
                    computer_id: compId,
                    csrf_token: '<?= csrf_token() ?>'
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'เช็คเอาท์สำเร็จ!',
                        text: 'เช็คเอาท์เสร็จสิ้นการใช้งานเรียบร้อยแล้ว',
                        confirmButtonColor: '#28a745',
                        background: '#ffffff',
                        color: '#1e293b'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เช็คเอาท์ล้มเหลว',
                        text: xhr.responseJSON?.error || 'ไม่สามารถเช็คเอาท์ได้',
                        confirmButtonColor: '#6366f1',
                        background: '#ffffff',
                        color: '#1e293b'
                    });
                }
            });
        });
    });
</script>
