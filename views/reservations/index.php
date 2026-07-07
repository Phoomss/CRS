<div class="card mb-4">
    <div class="card-header border-0 pb-0">
        <h5 class="fw-bold m-0"><i class="fa-regular fa-calendar-check text-indigo me-2"></i>My Bookings & Reservations</h5>
        <?php if (has_permission('create_reservations')): ?>
            <a href="/reservations/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Reserve Computer</a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <!-- Filter Form -->
        <form action="/reservations" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-secondary small">Search</label>
                <input type="text" name="search" class="form-control" placeholder="User, Email, PC code..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small">Lab Room</label>
                <select name="laboratory_id" class="form-select">
                    <option value="">All Rooms</option>
                    <?php foreach ($labs as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= ($filters['laboratory_id'] ?? '') == $l['id'] ? 'selected' : '' ?>><?= esc($l['code']) ?> - <?= esc($l['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small">Status</label>
                <select name="status_id" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="1" <?= ($filters['status_id'] ?? '') == 1 ? 'selected' : '' ?>>Pending</option>
                    <option value="2" <?= ($filters['status_id'] ?? '') == 2 ? 'selected' : '' ?>>Approved</option>
                    <option value="3" <?= ($filters['status_id'] ?? '') == 3 ? 'selected' : '' ?>>Rejected</option>
                    <option value="4" <?= ($filters['status_id'] ?? '') == 4 ? 'selected' : '' ?>>Cancelled</option>
                    <option value="5" <?= ($filters['status_id'] ?? '') == 5 ? 'selected' : '' ?>>Completed</option>
                    <option value="6" <?= ($filters['status_id'] ?? '') == 6 ? 'selected' : '' ?>>Expired</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label text-secondary small">Date</label>
                <input type="date" name="date" class="form-control" value="<?= esc($filters['date'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-indigo flex-grow-1"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="/reservations" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Account</th>
                        <th>Lab Room</th>
                        <th>Workstation</th>
                        <th>Schedule Timing</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No reservations matching current filters found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $r): ?>
                            <tr>
                                <td><span class="text-secondary small fw-bold">#<?= $r['id'] ?></span></td>
                                <td>
                                    <div class="fw-semibold"><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></div>
                                    <div class="small text-secondary"><?= esc($r['email']) ?></div>
                                </td>
                                <td>
                                    <span class="fw-semibold"><?= esc($r['laboratory_code']) ?></span>
                                </td>
                                <td>
                                    <?php if ($r['computer_code']): ?>
                                        <span class="badge bg-indigo-subtle text-indigo" style="font-size: 0.8rem;"><?= esc($r['computer_code']) ?></span>
                                    </td>
                                    <?php else: ?>
                                        <span class="text-muted small">None selected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small fw-medium"><?= date('M d, Y', strtotime($r['start_time'])) ?></div>
                                    <div class="small text-secondary"><?= date('H:i A', strtotime($r['start_time'])) ?> - <?= date('H:i A', strtotime($r['end_time'])) ?></div>
                                </td>
                                <td class="text-center">
                                    <?php
                                        // Pending=1, Approved=2, Rejected=3, Cancelled=4, Completed=5, Expired=6
                                        $class = 'bg-warning text-dark';
                                        if ($r['status_id'] == 2) $class = 'bg-info text-dark';
                                        if ($r['status_id'] == 3) $class = 'bg-danger text-white';
                                        if ($r['status_id'] == 4) $class = 'bg-secondary text-white';
                                        if ($r['status_id'] == 5) $class = 'bg-success text-white';
                                        if ($r['status_id'] == 6) $class = 'bg-light border border-secondary text-secondary';
                                    ?>
                                    <span class="badge badge-status <?= $class ?>"><?= esc($r['status_name']) ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/reservations/view/<?= $r['id'] ?>" class="btn btn-sm btn-outline-indigo" title="View Reservation Details"><i class="fa-solid fa-eye"></i></a>
                                        
                                        <?php if ($r['status_id'] == 2 && $r['user_id'] == auth()['id']): ?>
                                            <!-- Check In Action -->
                                            <button class="btn btn-sm btn-success btn-checkin" data-res-id="<?= $r['id'] ?>" data-comp-id="<?= $r['computer_id'] ?>" title="Check In Workstation"><i class="fa-solid fa-right-to-bracket"></i></button>
                                        <?php endif; ?>

                                        <?php if ($r['status_id'] == 2 && $r['user_id'] == auth()['id'] && !empty($r['check_in_time']) && empty($r['check_out_time'])): ?>
                                            <!-- Check Out Action -->
                                            <button class="btn btn-sm btn-warning text-dark btn-checkout" data-res-id="<?= $r['id'] ?>" data-comp-id="<?= $r['computer_id'] ?>" title="Check Out Workstation"><i class="fa-solid fa-right-from-bracket"></i></button>
                                        <?php endif; ?>

                                        <?php if (in_array($r['status_id'], [1, 2])): ?>
                                            <button class="btn btn-sm btn-outline-danger btn-cancel-booking" data-id="<?= $r['id'] ?>" title="Cancel Booking"><i class="fa-regular fa-trash-can"></i></button>
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
                title: 'Cancel Booking?',
                text: "Are you sure you want to cancel this reservation? This cannot be undone.",
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
                        url: '/reservations/cancel/' + resId,
                        method: 'POST',
                        data: {
                            csrf_token: '<?= csrf_token() ?>'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cancelled!',
                                text: response.message,
                                confirmButtonColor: '#6366f1',
                                background: '#111827',
                                color: '#fff'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.error || 'Cancellation failed.',
                                confirmButtonColor: '#6366f1',
                                background: '#111827',
                                color: '#fff'
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
                        title: 'Checked In!',
                        text: response.message,
                        confirmButtonColor: '#28a745',
                        background: '#111827',
                        color: '#fff'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Check-in Failed',
                        text: xhr.responseJSON?.error || 'Failed to check in.',
                        confirmButtonColor: '#6366f1',
                        background: '#111827',
                        color: '#fff'
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
                        title: 'Checked Out!',
                        text: response.message,
                        confirmButtonColor: '#28a745',
                        background: '#111827',
                        color: '#fff'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Check-out Failed',
                        text: xhr.responseJSON?.error || 'Failed to check out.',
                        confirmButtonColor: '#6366f1',
                        background: '#111827',
                        color: '#fff'
                    });
                }
            });
        });
    });
</script>
