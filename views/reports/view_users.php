<div class="card mb-4">
    <div class="card-header border-0 pb-0">
        <h5 class="fw-bold m-0"><i class="fa-solid fa-users-viewfinder text-indigo me-2"></i>User Accounts Reservation Performance</h5>
        <div class="d-flex gap-2">
            <button onclick="window.print();" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-print me-1"></i> Print Report</button>
            <a href="/reports/export/users" class="btn btn-success btn-sm"><i class="fa-solid fa-file-csv me-1"></i> Export to Excel</a>
            <a href="/reports" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
    </div>
    <div class="card-body">
        
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle m-0" id="report-table">
                <thead>
                    <tr>
                        <th>Identification</th>
                        <th>User Account Full Name</th>
                        <th>Email Address</th>
                        <th>Account Role</th>
                        <th class="text-center">Total Bookings</th>
                        <th class="text-center">Completed Sessions</th>
                        <th class="text-center">Expired Releases</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No user reservations recorded yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td>
                                    <?php if ($row['student_id']): ?>
                                        <span class="badge bg-secondary"><?= esc($row['student_id']) ?></span>
                                    <?php elseif ($row['employee_id']): ?>
                                        <span class="badge bg-secondary"><?= esc($row['employee_id']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">None</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fw-bold text-white"><?= esc($row['first_name']) ?> <?= esc($row['last_name']) ?></span></td>
                                <td><code><?= esc($row['email']) ?></code></td>
                                <td><span class="fw-semibold text-white"><?= esc($row['role_name']) ?></span></td>
                                <td class="text-center"><span class="badge bg-indigo"><?= $row['total_bookings'] ?></span></td>
                                <td class="text-center text-success"><span class="badge bg-success-subtle text-success"><?= $row['completed_bookings'] ?></span></td>
                                <td class="text-center text-danger"><span class="badge bg-danger-subtle text-danger"><?= $row['expired_bookings'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#report-table').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            language: {
                search: "Search logs:",
                paginate: {
                    previous: "<i class='fa-solid fa-chevron-left'></i>",
                    next: "<i class='fa-solid fa-chevron-right'></i>"
                }
            }
        });
    });
</script>
