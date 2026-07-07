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
                <h5 class="fw-bold m-0"><i class="fa-regular fa-id-card text-indigo me-2"></i>Reservation Details</h5>
                <a href="/reservations" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
            </div>
            
            <div class="card-body">
                <!-- Header Details -->
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Reservation #<?= $res['id'] ?></h4>
                        <span class="text-secondary small">Submitted on <?= date('M d, Y H:i A', strtotime($res['created_at'])) ?></span>
                    </div>
                    <div>
                        <?php
                            $class = 'bg-warning text-dark';
                            if ($res['status_id'] == 2) $class = 'bg-info text-dark';
                            if ($res['status_id'] == 3) $class = 'bg-danger';
                            if ($res['status_id'] == 4) $class = 'bg-secondary';
                            if ($res['status_id'] == 5) $class = 'bg-success';
                            if ($res['status_id'] == 6) $class = 'bg-dark border border-secondary text-secondary';
                        ?>
                        <span class="badge badge-status <?= $class ?> fs-6 py-2 px-3"><?= esc($res['status_name']) ?></span>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <!-- Column 1: User / Lab details -->
                    <div class="col-md-6">
                        <h6 class="text-secondary small text-uppercase fw-semibold mb-3">Reservist Details</h6>
                        <div class="mb-2"><strong>Full Name:</strong> <?= esc($res['first_name'] . ' ' . $res['last_name']) ?></div>
                        <div class="mb-2"><strong>Email Address:</strong> <?= esc($res['email']) ?></div>
                        <?php if ($res['student_id']): ?>
                            <div class="mb-2"><strong>Student ID:</strong> <span class="badge bg-secondary"><?= esc($res['student_id']) ?></span></div>
                        <?php elseif ($res['employee_id']): ?>
                            <div class="mb-2"><strong>Employee ID:</strong> <span class="badge bg-secondary"><?= esc($res['employee_id']) ?></span></div>
                        <?php endif; ?>

                        <h6 class="text-secondary small text-uppercase fw-semibold mt-4 mb-3">Laboratory Details</h6>
                        <div class="mb-2"><strong>Lab Room:</strong> <?= esc($res['laboratory_code']) ?> - <?= esc($res['laboratory_name']) ?></div>
                        <div class="mb-2"><strong>Location:</strong> <?= esc($res['building']) ?>, <?= esc($res['floor']) ?></div>
                    </div>

                    <!-- Column 2: Booking Schedule details -->
                    <div class="col-md-6">
                        <h6 class="text-secondary small text-uppercase fw-semibold mb-3">Schedule Timing</h6>
                        <div class="mb-2"><strong>Date:</strong> <?= date('M d, Y', strtotime($res['start_time'])) ?></div>
                        <div class="mb-2"><strong>Start Time:</strong> <span class="text-white fw-semibold"><?= date('H:i A', strtotime($res['start_time'])) ?></span></div>
                        <div class="mb-2"><strong>End Time:</strong> <span class="text-white fw-semibold"><?= date('H:i A', strtotime($res['end_time'])) ?></span></div>
                        <div class="mb-2"><strong>Duration:</strong> <?= round((strtotime($res['end_time']) - strtotime($res['start_time'])) / 3600, 1) ?> Hours</div>

                        <h6 class="text-secondary small text-uppercase fw-semibold mt-4 mb-3">Purpose</h6>
                        <div class="text-muted italic">"<?= esc($res['purpose']) ?>"</div>
                    </div>
                </div>

                <!-- Selected PC specifications -->
                <div class="mb-4 bg-dark bg-opacity-20 p-3 rounded-4 border border-secondary border-opacity-10">
                    <h6 class="text-secondary small text-uppercase fw-semibold mb-3">Assigned Workstation</h6>
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center border-end border-secondary border-opacity-20 pe-3 mb-3 mb-md-0">
                            <i class="fa-solid fa-display text-indigo mb-2" style="font-size: 2.2rem;"></i>
                            <div class="fw-bold text-white"><?= esc($res['computer_code']) ?></div>
                            <div class="small text-secondary"><?= esc($res['computer_name']) ?></div>
                        </div>
                        <div class="col-md-9 ps-md-4">
                            <div class="row g-2 small text-secondary">
                                <div class="col-6"><strong>Asset ID:</strong> <?= esc($res['computer_asset_number']) ?></div>
                                <div class="col-6"><strong>IP Address:</strong> <?= esc($res['ip_address']) ?></div>
                                <div class="col-6"><strong>Processor:</strong> <?= esc($res['cpu']) ?></div>
                                <div class="col-6"><strong>RAM:</strong> <?= esc($res['ram']) ?></div>
                                <div class="col-6"><strong>Storage:</strong> <?= esc($res['storage']) ?></div>
                                <div class="col-6"><strong>Operating System:</strong> <?= esc($res['operating_system']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Check in status box -->
                <?php if ($res['status_id'] == 2 || $res['status_id'] == 5): ?>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: rgba(40, 167, 69, 0.05); border: 1px solid rgba(40, 167, 69, 0.15);">
                                <span class="text-secondary small d-block">Checked In Time</span>
                                <span class="fw-bold text-white small"><?= $res['check_in_time'] ? date('M d, Y H:i A', strtotime($res['check_in_time'])) : 'Awaiting Check-in...' ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: rgba(220, 53, 69, 0.05); border: 1px solid rgba(220, 53, 69, 0.15);">
                                <span class="text-secondary small d-block">Checked Out Time</span>
                                <span class="fw-bold text-white small"><?= $res['check_out_time'] ? date('M d, Y H:i A', strtotime($res['check_out_time'])) : 'N/A' ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Approver Remarks -->
                <?php if ($res['approved_by']): ?>
                    <div class="mb-4 p-3 rounded-3 border border-secondary border-opacity-10" style="background: rgba(15, 23, 42, 0.3);">
                        <h6 class="text-secondary small text-uppercase fw-semibold mb-2">Decision Log Details</h6>
                        <div class="small">
                            <strong>Reviewed By:</strong> <?= esc($res['approver_first_name'] . ' ' . $res['approver_last_name']) ?> <span class="text-muted">(on <?= date('M d, Y H:i A', strtotime($res['approved_at'])) ?>)</span>
                        </div>
                        <?php if ($res['remarks']): ?>
                            <div class="small mt-2"><strong>Remarks:</strong> <span class="text-secondary">"<?= esc($res['remarks']) ?>"</span></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Action Triggers -->
                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4 pt-3 border-top border-secondary border-opacity-10">
                    
                    <!-- Owner Check In / Check Out -->
                    <?php if ($res['status_id'] == 2 && $isOwner): ?>
                        <?php if (empty($res['check_in_time'])): ?>
                            <button id="btn-checkin-action" class="btn btn-success px-4" data-res-id="<?= $res['id'] ?>" data-comp-id="<?= $res['computer_id'] ?>"><i class="fa-solid fa-right-to-bracket me-2"></i> Check In Workstation</button>
                        <?php elseif (empty($res['check_out_time'])): ?>
                            <button id="btn-checkout-action" class="btn btn-warning text-dark px-4" data-res-id="<?= $res['id'] ?>" data-comp-id="<?= $res['computer_id'] ?>"><i class="fa-solid fa-right-from-bracket me-2"></i> Check Out Workstation</button>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Owner/Admin Cancel Reservation -->
                    <?php if (in_array($res['status_id'], [1, 2]) && ($isOwner || $isAdmin)): ?>
                        <button id="btn-cancel-action" class="btn btn-outline-danger" data-id="<?= $res['id'] ?>"><i class="fa-regular fa-trash-can me-2"></i> Cancel Reservation</button>
                    <?php endif; ?>

                    <!-- Administrator Decisions Panel (Approve/Reject) -->
                    <?php if ($res['status_id'] == 1 && $isAdmin): ?>
                        <button class="btn btn-danger px-4" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="fa-solid fa-circle-xmark me-2"></i> Reject</button>
                        <button class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#approveModal"><i class="fa-solid fa-circle-check me-2"></i> Approve Booking</button>
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
                <h5 class="modal-title text-white fw-bold">Approve Booking Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="approve_remarks" class="form-label text-secondary small">Optional Remarks / Instructions</label>
                    <textarea class="form-control" name="remarks" id="approve_remarks" rows="3" placeholder="e.g. Please check in on time and clean up the workstation before leaving."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-circle-check me-1"></i> Confirm Approval</button>
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
                <h5 class="modal-title text-white fw-bold">Decline Booking Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="reject_remarks" class="form-label text-secondary small">Rejection Reason (Required)</label>
                    <textarea class="form-control" name="remarks" id="reject_remarks" rows="3" placeholder="e.g. Workstation is reserved for lecturer courses, or scheduling overlapping." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Reject Booking</button>
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
                        title: 'Success',
                        text: response.message,
                        confirmButtonColor: '#28a745',
                        background: '#111827',
                        color: '#fff'
                    }).then(() => { window.location.reload(); });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.error || 'Check-in failed.',
                        confirmButtonColor: '#6366f1',
                        background: '#111827',
                        color: '#fff'
                    });
                }
            });
        });

        // Check-out
        $('#btn-checkout-action').on('click', function() {
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
                        title: 'Success',
                        text: response.message,
                        confirmButtonColor: '#28a745',
                        background: '#111827',
                        color: '#fff'
                    }).then(() => { window.location.reload(); });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.error || 'Check-out failed.',
                        confirmButtonColor: '#6366f1',
                        background: '#111827',
                        color: '#fff'
                    });
                }
            });
        });
    });
</script>
