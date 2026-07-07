<div class="card mb-4">
    <div class="card-header border-0 pb-0">
        <h5 class="fw-bold m-0"><i class="fa-solid fa-door-open text-indigo me-2"></i>Laboratory Capacity & Reservations Analytics</h5>
        <div class="d-flex gap-2">
            <button onclick="window.print();" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-print me-1"></i> Print Report</button>
            <a href="/reports/export/laboratories" class="btn btn-success btn-sm"><i class="fa-solid fa-file-csv me-1"></i> Export to Excel</a>
            <a href="/reports" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i></a>
        </div>
    </div>
    <div class="card-body">
        
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle m-0" id="report-table">
                <thead>
                    <tr>
                        <th>Laboratory Code</th>
                        <th>Laboratory Room Name</th>
                        <th class="text-center">Seat Capacity</th>
                        <th class="text-center">Total Reservations</th>
                        <th class="text-center">Completed Sessions</th>
                        <th class="text-center">Rejected Applications</th>
                        <th class="text-center">Cancelled Sessions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No laboratory booking data logged yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><span class="fw-bold text-white"><?= esc($row['code']) ?></span></td>
                                <td><span class="fw-semibold text-white"><?= esc($row['name']) ?></span></td>
                                <td class="text-center"><span class="badge bg-indigo"><?= $row['capacity'] ?> Workstations</span></td>
                                <td class="text-center"><span class="badge bg-secondary"><?= $row['total_reservations'] ?></span></td>
                                <td class="text-center text-success"><span class="badge bg-success-subtle text-success"><?= $row['completed_reservations'] ?></span></td>
                                <td class="text-center text-danger"><span class="badge bg-danger-subtle text-danger"><?= $row['rejected_reservations'] ?></span></td>
                                <td class="text-center text-warning"><span class="badge bg-warning-subtle text-warning"><?= $row['cancelled_reservations'] ?></span></td>
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
