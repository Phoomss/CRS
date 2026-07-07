<?php
$currentUser = auth();
$role = $currentUser['role_name'] ?? '';
$isOwner = (int)$res['user_id'] === (int)$currentUser['id'];
$isAdmin = $role === 'Super Administrator' || $role === 'Department Administrator' || $role === 'Staff';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="fw-bold m-0"><i class="fa-regular fa-id-card text-indigo me-2"></i>รายละเอียดใบจองเครื่อง</h5>
                <a href="/reservations" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> กลับไปยังหน้ารายการ</a>
            </div>
            
            <div class="card-body">
                <!-- Header Details -->
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div>
                        <h4 class="fw-bold mb-1">ใบจองรหัส #<?= $res['id'] ?></h4>
                        <span class="text-secondary small">ยื่นเมื่อ <?= date('d M Y H:i A', strtotime($res['created_at'])) ?></span>
                    </div>
                    <div>
                        <?php
                            $class = 'bg-warning text-dark';
                            if ($res['status_id'] == 2) $class = 'bg-info text-dark';
                            if ($res['status_id'] == 3) $class = 'bg-danger text-white';
                            if ($res['status_id'] == 4) $class = 'bg-secondary text-white';
                            if ($res['status_id'] == 5) $class = 'bg-success text-white';
                            if ($res['status_id'] == 6) $class = 'bg-light border border-secondary text-secondary';
                        ?>
                        <span class="badge badge-status <?= $class ?> fs-6 py-2 px-3"><?= esc($res['status_name']) ?></span>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Column 1: User / Lab details -->
                    <div class="col-md-6">
                        <h6 class="text-secondary small text-uppercase fw-semibold mb-3">รายละเอียดผู้ขอจอง</h6>
                        <div class="mb-2"><strong>ชื่อ-นามสกุล:</strong> <?= esc($res['first_name'] . ' ' . $res['last_name']) ?></div>
                        <div class="mb-2"><strong>ที่อยู่อีเมล:</strong> <?= esc($res['email']) ?></div>
                        <?php if ($res['student_id']): ?>
                            <div class="mb-2"><strong>รหัสนักศึกษา:</strong> <span class="badge bg-secondary"><?= esc($res['student_id']) ?></span></div>
                        <?php elseif ($res['employee_id']): ?>
                            <div class="mb-2"><strong>รหัสพนักงาน:</strong> <span class="badge bg-secondary"><?= esc($res['employee_id']) ?></span></div>
                        <?php endif; ?>

                        <h6 class="text-secondary small text-uppercase fw-semibold mt-4 mb-3">รายละเอียดห้องปฏิบัติการ</h6>
                        <div class="mb-2"><strong>ห้องปฏิบัติการ:</strong> <?= esc($res['laboratory_code']) ?> - <?= esc($res['laboratory_name']) ?></div>
                        <div class="mb-2"><strong>สถานที่ตั้ง:</strong> <?= esc($res['building']) ?>, ชั้น <?= esc($res['floor']) ?></div>
                    </div>

                    <!-- Column 2: Booking Schedule details -->
                    <div class="col-md-6">
                        <h6 class="text-secondary small text-uppercase fw-semibold mb-3">ช่วงเวลาการจอง</h6>
                        <div class="mb-2"><strong>วันที่จอง:</strong> <?= date('d M Y', strtotime($res['start_time'])) ?></div>
                        <div class="mb-2"><strong>เวลาเริ่มต้น:</strong> <span class="text-dark fw-semibold"><?= date('H:i A', strtotime($res['start_time'])) ?></span></div>
                        <div class="mb-2"><strong>เวลาสิ้นสุด:</strong> <span class="text-dark fw-semibold"><?= date('H:i A', strtotime($res['end_time'])) ?></span></div>
                        <div class="mb-2"><strong>ระยะเวลา:</strong> <?= round((strtotime($res['end_time']) - strtotime($res['start_time'])) / 3600, 1) ?> ชั่วโมง</div>

                        <h6 class="text-secondary small text-uppercase fw-semibold mt-4 mb-3">วัตถุประสงค์</h6>
                        <div class="text-muted italic">"<?= esc($res['purpose']) ?>"</div>
                    </div>
                </div>

                <!-- Selected PC specifications -->
                <div class="mb-4 bg-light bg-opacity-50 p-3 rounded-4 border border-secondary border-opacity-10">
                    <h6 class="text-secondary small text-uppercase fw-semibold mb-3">เครื่องคอมพิวเตอร์ที่จัดสรรให้</h6>
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center border-end border-secondary border-opacity-20 pe-3 mb-3 mb-md-0">
                            <i class="fa-solid fa-display text-indigo mb-2" style="font-size: 2.2rem;"></i>
                            <div class="fw-bold"><?= esc($res['computer_code']) ?></div>
                            <div class="small text-secondary"><?= esc($res['computer_name']) ?></div>
                        </div>
                        <div class="col-md-9 ps-md-4">
                            <div class="row g-2 small text-secondary">
                                <div class="col-6"><strong>รหัสครุภัณฑ์:</strong> <?= esc($res['computer_asset_number']) ?></div>
                                <div class="col-6"><strong>ที่อยู่ IP:</strong> <?= esc($res['ip_address']) ?></div>
                                <div class="col-6"><strong>หน่วยประมวลผล (CPU):</strong> <?= esc($res['cpu']) ?></div>
                                <div class="col-6"><strong>หน่วยความจำ (RAM):</strong> <?= esc($res['ram']) ?></div>
                                <div class="col-6"><strong>ความจุ (Storage):</strong> <?= esc($res['storage']) ?></div>
                                <div class="col-6"><strong>ระบบปฏิบัติการ (OS):</strong> <?= esc($res['operating_system']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Check in status box -->
                <?php if ($res['status_id'] == 2 || $res['status_id'] == 5): ?>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: rgba(40, 167, 69, 0.05); border: 1px solid rgba(40, 167, 69, 0.15);">
                                <span class="text-secondary small d-block">เวลาเช็คอินเข้าใช้งาน</span>
                                <span class="fw-bold small"><?= $res['check_in_time'] ? date('d M Y H:i A', strtotime($res['check_in_time'])) : 'รอการเช็คอิน...' ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: rgba(220, 53, 69, 0.05); border: 1px solid rgba(220, 53, 69, 0.15);">
                                <span class="text-secondary small d-block">เวลาเช็คเอาท์ออกจากการใช้งาน</span>
                                <span class="fw-bold small"><?= $res['check_out_time'] ? date('d M Y H:i A', strtotime($res['check_out_time'])) : 'ไม่มีข้อมูล' ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Approver Remarks -->
                <?php if ($res['approved_by']): ?>
                    <div class="mb-4 p-3 rounded-3 border border-secondary border-opacity-10" style="background: rgba(0, 0, 0, 0.02);">
                        <h6 class="text-secondary small text-uppercase fw-semibold mb-2">บันทึกการพิจารณาคำขอ</h6>
                        <div class="small">
                            <strong>ผู้พิจารณาคำขอ:</strong> <?= esc($res['approver_first_name'] . ' ' . $res['approver_last_name']) ?> <span class="text-muted">(เมื่อวันที่ <?= date('d M Y H:i A', strtotime($res['approved_at'])) ?>)</span>
                        </div>
                        <?php if ($res['remarks']): ?>
                            <div class="small mt-2"><strong>หมายเหตุ:</strong> <span class="text-secondary">"<?= esc($res['remarks']) ?>"</span></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Action Triggers -->
                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4 pt-3 border-top border-secondary border-opacity-10">
                    
                    <!-- Owner Check In / Check Out -->
                    <?php if ($res['status_id'] == 2 && $isOwner): ?>
                        <?php if (empty($res['check_in_time'])): ?>
                            <button id="btn-checkin-action" class="btn btn-success px-4" data-res-id="<?= $res['id'] ?>" data-comp-id="<?= $res['computer_id'] ?>"><i class="fa-solid fa-right-to-bracket me-2"></i> เช็คอินเข้าใช้งาน</button>
                        <?php elseif (empty($res['check_out_time'])): ?>
                            <button id="btn-checkout-action" class="btn btn-warning text-dark px-4" data-res-id="<?= $res['id'] ?>" data-comp-id="<?= $res['computer_id'] ?>"><i class="fa-solid fa-right-from-bracket me-2"></i> เช็คเอาท์ออกจากการใช้งาน</button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Owner/Admin Cancel Reservation -->
                    <?php if (in_array($res['status_id'], [1, 2]) && ($isOwner || $isAdmin)): ?>
                        <button id="btn-cancel-action" class="btn btn-outline-danger" data-id="<?= $res['id'] ?>"><i class="fa-solid fa-ban me-2"></i> ยกเลิกการจอง</button>
                    <?php endif; ?>

                    <!-- Administrator Decisions Panel (Approve/Reject) -->
                    <?php if ($res['status_id'] == 1 && $isAdmin): ?>
                        <button class="btn btn-danger px-4" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="fa-solid fa-circle-xmark me-2"></i> ปฏิเสธคำขอ</button>
                        <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#approveModal"><i class="fa-solid fa-circle-check me-2"></i> อนุมัติการจอง</button>
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
        <form action="/reservations/approve/<?= $res['id'] ?>" method="POST" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">อนุมัติคำขอการจองเครื่อง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="approve_remarks" class="form-label text-secondary small">หมายเหตุ / คำแนะนำการใช้งาน (ระบุหรือไม่ก็ได้)</label>
                    <textarea class="form-control" name="remarks" id="approve_remarks" rows="3" placeholder="เช่น กรุณาเช็คอินให้ตรงเวลาและจัดเก็บอุปกรณ์ให้เรียบร้อยก่อนออกจากห้อง"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-circle-check me-1"></i> ยืนยันอนุมัติ</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reject Reservation -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="/reservations/reject/<?= $res['id'] ?>" method="POST" class="modal-content">
            <?= csrf_field() ?>
            <div class="modal-header">
                <h5 class="modal-title fw-bold">ปฏิเสธคำขอการจองเครื่อง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="reject_remarks" class="form-label text-secondary small">เหตุผลที่ปฏิเสธการจอง (จำเป็นต้องระบุ)</label>
                    <textarea class="form-control" name="remarks" id="reject_remarks" rows="3" placeholder="เช่น เครื่องคอมพิวเตอร์ติดการใช้งานในชั่วโมงการเรียนการสอน หรือช่วงเวลาทับซ้อนกัน" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-circle-xmark me-1"></i> ปฏิเสธคำขอ</button>
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
                title: 'Cancel Reservation?',
                text: "Are you sure you want to cancel this booking?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Cancel it!',
                background: '#111827',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/reservations/cancel/' + id,
                        method: 'POST',
                        data: { csrf_token: '<?= csrf_token() ?>' },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cancelled',
                                text: response.message,
                                confirmButtonColor: '#6366f1',
                                background: '#111827',
                                color: '#fff'
                            }).then(() => { window.location.reload(); });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: xhr.responseJSON?.error || 'Failed to cancel.',
                                confirmButtonColor: '#6366f1',
                                background: '#111827',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        });

        // Check-in
        $('#btn-checkin-action').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> กำลังเช็คอิน...');
            
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
                        title: 'สำเร็จ',
                        text: response.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => { window.location.reload(); });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-right-to-bracket me-2"></i> เช็คอินเข้าใช้งาน');
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        text: xhr.responseJSON?.error || 'Check-in failed.',
                        confirmButtonColor: '#6366f1'
                    });
                }
            });
        });

        // Check-out
        $('#btn-checkout-action').on('click', function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> กำลังเช็คเอาท์...');
            
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
                        title: 'สำเร็จ',
                        text: response.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => { window.location.reload(); });
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-right-from-bracket me-2"></i> เช็คเอาท์ออกจากการใช้งาน');
                    Swal.fire({
                        icon: 'error',
                        title: 'ข้อผิดพลาด',
                        text: xhr.responseJSON?.error || 'Check-out failed.',
                        confirmButtonColor: '#6366f1'
                    });
                }
            });
        });
    });
</script>
