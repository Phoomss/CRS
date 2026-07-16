<?php
$currentUser = auth();
$role = $currentUser['role_name'] ?? '';
$isOwner = (int)$res['user_id'] === (int)$currentUser['id'];
$isAdmin = $role === 'Super Administrator' || $role === 'Department Administrator' || $role === 'Staff';
?>

<style>
    .view-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .view-card-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .view-card-header h5 {
        color: #ffffff;
        font-weight: 700;
    }
    .view-card-header .btn-outline-light {
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        font-weight: 500;
        border-radius: 10px;
        padding: 6px 14px;
        transition: all 0.2s ease;
    }
    .view-card-header .btn-outline-light:hover {
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
    .pc-info-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card view-card mb-4">
            <div class="view-card-header">
                <h5 class="m-0"><i class="fa-regular fa-id-card me-2"></i>รายละเอียดใบจองเครื่อง</h5>
                <a href="/reservations" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> กลับไปยังหน้ารายการ</a>
            </div>
            
            <div class="card-body p-4">
                <!-- Header Details -->
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">ใบจองรหัส #<?= $res['id'] ?></h4>
                        <span class="text-secondary small">ยื่นเมื่อ <?= date('d M Y H:i A', strtotime($res['created_at'])) ?></span>
                    </div>
                    <div>
                        <?php
                            // Pending=1, Approved=2, Rejected=3, Cancelled=4, Completed=5, Expired=6
                            $class = 'bg-warning-subtle text-warning-emphasis border border-warning';
                            if ($res['status_id'] == 2) $class = 'bg-info-subtle text-info-emphasis border border-info';
                            if ($res['status_id'] == 3) $class = 'bg-danger-subtle text-danger-emphasis border border-danger';
                            if ($res['status_id'] == 4) $class = 'bg-secondary-subtle text-secondary-emphasis border border-secondary';
                            if ($res['status_id'] == 5) $class = 'bg-success-subtle text-success-emphasis border border-success';
                            if ($res['status_id'] == 6) $class = 'bg-light-subtle text-muted border border-secondary-subtle';
                        ?>
                        <span class="badge fs-6 py-2 px-3 <?= $class ?>" style="font-weight: 600; border-radius: 8px;"><?= esc($res['status_name']) ?></span>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Column 1: User / Lab details -->
                    <div class="col-md-6">
                        <h6 class="section-title">รายละเอียดผู้ขอจอง</h6>
                        <div class="mb-2 text-dark"><strong>ชื่อ-นามสกุล:</strong> <span class="text-secondary"><?= esc($res['first_name'] . ' ' . $res['last_name']) ?></span></div>
                        <div class="mb-2 text-dark"><strong>ที่อยู่อีเมล:</strong> <span class="text-secondary"><?= esc($res['email']) ?></span></div>
                        <?php if ($res['student_id']): ?>
                            <div class="mb-2 text-dark"><strong>รหัสนักศึกษา:</strong> <span class="badge bg-secondary-subtle text-secondary fw-semibold"><?= esc($res['student_id']) ?></span></div>
                        <?php elseif ($res['employee_id']): ?>
                            <div class="mb-2 text-dark"><strong>รหัสพนักงาน:</strong> <span class="badge bg-secondary-subtle text-secondary fw-semibold"><?= esc($res['employee_id']) ?></span></div>
                        <?php endif; ?>

                        <h6 class="section-title mt-4">รายละเอียดห้องปฏิบัติการ</h6>
                        <div class="mb-2 text-dark"><strong>ห้องปฏิบัติการ:</strong> <span class="text-secondary"><?= esc($res['laboratory_code']) ?> - <?= esc($res['laboratory_name']) ?></span></div>
                        <div class="mb-2 text-dark"><strong>สถานที่ตั้ง:</strong> <span class="text-secondary"><?= esc($res['building']) ?>, ชั้น <?= esc($res['floor']) ?></span></div>
                    </div>

                    <!-- Column 2: Booking Schedule details -->
                    <div class="col-md-6">
                        <h6 class="section-title">ช่วงเวลาการจอง</h6>
                        <div class="mb-2 text-dark"><strong>วันที่จอง:</strong> <span class="text-secondary"><?= date('d M Y', strtotime($res['start_time'])) ?></span></div>
                        <div class="mb-2 text-dark"><strong>เวลาเริ่มต้น:</strong> <span class="text-dark fw-semibold"><?= date('H:i A', strtotime($res['start_time'])) ?></span></div>
                        <div class="mb-2 text-dark"><strong>เวลาสิ้นสุด:</strong> <span class="text-dark fw-semibold"><?= date('H:i A', strtotime($res['end_time'])) ?></span></div>
                        <div class="mb-2 text-dark"><strong>ระยะเวลา:</strong> <span class="text-secondary"><?= round((strtotime($res['end_time']) - strtotime($res['start_time'])) / 3600, 1) ?> ชั่วโมง</span></div>

                        <h6 class="section-title mt-4">วัตถุประสงค์</h6>
                        <div class="text-muted italic" style="font-size: 0.92rem;">"<?= esc($res['purpose']) ?>"</div>
                    </div>
                </div>

                <!-- Selected PC specifications -->
                <div class="mb-4 pc-info-box">
                    <h6 class="section-title" style="margin-bottom: 15px;">เครื่องคอมพิวเตอร์ที่จัดสรรให้</h6>
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center border-end border-slate-200 pe-3 mb-3 mb-md-0">
                            <i class="fa-solid fa-display text-indigo mb-2" style="font-size: 2.2rem;"></i>
                            <div class="fw-bold text-dark"><?= esc($res['computer_code']) ?></div>
                            <div class="small text-secondary"><?= esc($res['computer_name']) ?></div>
                        </div>
                        <div class="col-md-9 ps-md-4">
                            <div class="row g-2 small text-secondary">
                                <div class="col-6"><strong>รหัสครุภัณฑ์:</strong> <span class="text-dark fw-medium"><?= esc($res['computer_asset_number']) ?></span></div>
                                <div class="col-6"><strong>ที่อยู่ IP:</strong> <span class="text-dark fw-medium"><?= esc($res['ip_address']) ?></span></div>
                                <div class="col-6"><strong>หน่วยประมวลผล (CPU):</strong> <span class="text-dark fw-medium"><?= esc($res['cpu']) ?></span></div>
                                <div class="col-6"><strong>หน่วยความจำ (RAM):</strong> <span class="text-dark fw-medium"><?= esc($res['ram']) ?></span></div>
                                <div class="col-6"><strong>ความจุ (Storage):</strong> <span class="text-dark fw-medium"><?= esc($res['storage']) ?></span></div>
                                <div class="col-6"><strong>ระบบปฏิบัติการ (OS):</strong> <span class="text-dark fw-medium"><?= esc($res['operating_system']) ?></span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Check in status box -->
                <?php if ($res['status_id'] == 2 || $res['status_id'] == 5): ?>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.15);">
                                <span class="text-secondary small d-block">เวลาเช็คอินเข้าใช้งาน</span>
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;"><?= $res['check_in_time'] ? date('d M Y H:i A', strtotime($res['check_in_time'])) : 'รอการเช็คอินเข้าใช้งานเครื่อง...' ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.15);">
                                <span class="text-secondary small d-block">เวลาเช็คเอาท์เสร็จสิ้นการใช้งาน</span>
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;"><?= $res['check_out_time'] ? date('d M Y H:i A', strtotime($res['check_out_time'])) : 'ยังไม่มีการเช็คเอาท์ออกจากระบบ' ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Approver Remarks -->
                <?php if ($res['approved_by']): ?>
                    <div class="mb-4 p-3 rounded-3 border border-secondary border-opacity-10" style="background-color: #f8fafc;">
                        <h6 class="section-title" style="margin-bottom: 12px; font-size: 0.8rem;">บันทึกการพิจารณาคำขอ</h6>
                        <div class="small text-dark">
                            <strong>ผู้ตรวจสอบอนุมัติ:</strong> <span class="text-secondary"><?= esc($res['approver_first_name'] . ' ' . $res['approver_last_name']) ?></span> <span class="text-muted small">(วันที่พิจารณา: <?= date('d M Y H:i A', strtotime($res['approved_at'])) ?>)</span>
                        </div>
                        <?php if ($res['remarks']): ?>
                            <div class="small mt-2 text-dark"><strong>หมายเหตุของผู้ตรวจสอบ:</strong> <span class="text-secondary">"<?= esc($res['remarks']) ?>"</span></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Action Triggers -->
                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4 pt-3 border-top border-secondary border-opacity-10">
                    
                    <!-- Owner Check In / Check Out -->
                    <?php if ($res['status_id'] == 2 && $isOwner): ?>
                        <?php if (empty($res['check_in_time'])): ?>
                            <button id="btn-checkin-action" class="btn btn-success px-4 py-2" data-res-id="<?= $res['id'] ?>" data-comp-id="<?= $res['computer_id'] ?>" style="border-radius: 10px; font-weight: 600;"><i class="fa-solid fa-right-to-bracket me-2"></i> เช็คอินเข้าใช้งานเครื่อง</button>
                        <?php elseif (empty($res['check_out_time'])): ?>
                            <button id="btn-checkout-action" class="btn btn-warning text-dark px-4 py-2" data-res-id="<?= $res['id'] ?>" data-comp-id="<?= $res['computer_id'] ?>" style="border-radius: 10px; font-weight: 600;"><i class="fa-solid fa-right-from-bracket me-2"></i> เช็คเอาท์เสร็จสิ้นการใช้งาน</button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Owner/Admin Cancel Reservation -->
                    <?php if (in_array($res['status_id'], [1, 2]) && ($isOwner || $isAdmin)): ?>
                        <button id="btn-cancel-action" class="btn btn-outline-danger px-4 py-2" data-id="<?= $res['id'] ?>" style="border-radius: 10px; font-weight: 600;"><i class="fa-solid fa-ban me-2"></i> ยกเลิกการจองเครื่อง</button>
                    <?php endif; ?>

                    <!-- Administrator Decisions Panel (Approve/Reject) -->
                    <?php if ($res['status_id'] == 1 && $isAdmin): ?>
                        <button class="btn btn-danger px-4 py-2" data-bs-toggle="modal" data-bs-target="#rejectModal" style="border-radius: 10px; font-weight: 600;"><i class="fa-solid fa-circle-xmark me-2"></i> ปฏิเสธคำขอการจอง</button>
                        <button class="btn btn-success px-4 py-2" data-bs-toggle="modal" data-bs-target="#approveModal" style="border-radius: 10px; font-weight: 600;"><i class="fa-solid fa-circle-check me-2"></i> อนุมัติคำขอการจอง</button>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal: Approve Reservation -->
<?php if ($res['status_id'] == 1 && $isAdmin): ?>
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="/reservations/approve/<?= $res['id'] ?>" method="POST" class="modal-content" style="border-radius: 14px;">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">อนุมัติคำขอการจองเครื่องคอมพิวเตอร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="approve_remarks" class="form-label text-secondary small fw-medium">หมายเหตุ / คำแนะนำการใช้งาน (ระบุหรือไม่ก็ได้)</label>
                    <textarea class="form-control" name="remarks" id="approve_remarks" rows="3" placeholder="เช่น กรุณาเช็คอินเข้าใช้งานเครื่องภายใน 15 นาทีตามกำหนดเวลา..." style="border-radius: 8px;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">ปิด</button>
                <button type="submit" class="btn btn-success" style="border-radius: 8px;"><i class="fa-solid fa-circle-check me-1"></i> ยืนยันอนุมัติคำขอ</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reject Reservation -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="/reservations/reject/<?= $res['id'] ?>" method="POST" class="modal-content" style="border-radius: 14px;">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">ปฏิเสธคำขอการจองเครื่องคอมพิวเตอร์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="reject_remarks" class="form-label text-secondary small fw-medium">เหตุผลในการปฏิเสธคำขอการจอง (จำเป็นต้องระบุ)</label>
                    <textarea class="form-control" name="remarks" id="reject_remarks" rows="3" placeholder="เช่น ในช่วงเวลาดังกล่าวเครื่องคอมพิวเตอร์ติดชั่วโมงการเรียนการสอน..." required style="border-radius: 8px;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">ปิด</button>
                <button type="submit" class="btn btn-danger" style="border-radius: 8px;"><i class="fa-solid fa-circle-xmark me-1"></i> ยืนยันปฏิเสธคำขอ</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- AJAX scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cancel Booking
        $('#btn-cancel-action').on('click', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'ต้องการยกเลิกการจองเครื่อง?',
                text: "คุณแน่ใจหรือไม่ว่าต้องการยกเลิกรายการจองเครื่องคอมพิวเตอร์นี้?",
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
                        url: '/reservations/cancel/' + id,
                        method: 'POST',
                        data: { csrf_token: '<?= csrf_token() ?>' },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ยกเลิกรายการจองสำเร็จแล้ว',
                                text: 'ยกเลิกคำขอจองเครื่องคอมพิวเตอร์เรียบร้อยแล้ว',
                                confirmButtonColor: '#6366f1',
                                background: '#ffffff',
                                color: '#1e293b'
                            }).then(() => { window.location.reload(); });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'ข้อผิดพลาด',
                                text: xhr.responseJSON?.error || 'เกิดปัญหาไม่สามารถยกเลิกการจองเครื่องได้',
                                confirmButtonColor: '#6366f1',
                                background: '#ffffff',
                                color: '#1e293b'
                            });
                        }
                    });
                }
            });
        });

        // Check-in
        $('#btn-checkin-action').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> กำลังทำรายการเช็คอิน...');
            
            var resId = btn.data('res-id');
            var compId = btn.data('comp-id');
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
                        title: 'เช็คอินสำเร็จ',
                        text: 'เช็คอินเข้าใช้งานเครื่องคอมพิวเตอร์เรียบร้อยแล้ว',
                        confirmButtonColor: '#28a745',
                        background: '#ffffff',
                        color: '#1e293b'
                    }).then(() => { window.location.reload(); });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-right-to-bracket me-2"></i> เช็คอินเข้าใช้งานเครื่อง');
                    Swal.fire({
                        icon: 'error',
                        title: 'เช็คอินไม่สำเร็จ',
                        text: xhr.responseJSON?.error || 'เกิดข้อผิดพลาดในการเช็คอินเข้าใช้งาน',
                        confirmButtonColor: '#6366f1',
                        background: '#ffffff',
                        color: '#1e293b'
                    });
                }
            });
        });

        // Check-out
        $('#btn-checkout-action').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> กำลังทำรายการเช็คเอาท์...');
            
            var resId = btn.data('res-id');
            var compId = btn.data('comp-id');
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
                        title: 'เช็คเอาท์สำเร็จ',
                        text: 'เช็คเอาท์และปล่อยสิทธิ์เครื่องเรียบร้อยแล้ว',
                        confirmButtonColor: '#28a745',
                        background: '#ffffff',
                        color: '#1e293b'
                    }).then(() => { window.location.reload(); });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-right-from-bracket me-2"></i> เช็คเอาท์เสร็จสิ้นการใช้งาน');
                    Swal.fire({
                        icon: 'error',
                        title: 'เช็คเอาท์ไม่สำเร็จ',
                        text: xhr.responseJSON?.error || 'เกิดข้อผิดพลาดในการเช็คเอาท์ออกจากระบบ',
                        confirmButtonColor: '#6366f1',
                        background: '#ffffff',
                        color: '#1e293b'
                    });
                }
            });
        });
    });
</script>
