<div class="card mb-4">
    <div class="card-header border-0 pb-0">
        <h5 class="fw-bold m-0"><i class="fa-solid fa-door-open text-indigo me-2"></i>Manage Laboratories</h5>
        <a href="/laboratories/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Register Laboratory</a>
    </div>
    <div class="card-body">
        
        <!-- Filter Form -->
        <form action="/laboratories" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-5">
                <label class="form-label text-secondary small">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Room code, name, building location..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label text-secondary small">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-indigo flex-grow-1"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="/laboratories" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle m-0" id="laboratories-table">
                <thead>
                    <tr>
                        <th>Laboratory Code</th>
                        <th>Laboratory Name</th>
                        <th>Location</th>
                        <th class="text-center">Seat Capacity</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($labs)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No computer laboratories registered in the system yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($labs as $lab): ?>
                            <tr>
                                <td><span class="fw-bold text-white"><?= esc($lab['code']) ?></span></td>
                                <td><span class="fw-semibold text-white"><?= esc($lab['name']) ?></span></td>
                                <td>
                                    <div><?= esc($lab['building']) ?></div>
                                    <div class="small text-secondary"><?= esc($lab['floor']) ?></div>
                                </td>
                                <td class="text-center"><span class="badge bg-indigo"><?= $lab['capacity'] ?> Workstations</span></td>
                                <td class="text-center">
                                    <?php
                                        $class = $lab['status'] === 'active' ? 'bg-success' : 'bg-danger';
                                    ?>
                                    <span class="badge badge-status <?= $class ?>"><?= esc(ucfirst($lab['status'])) ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/laboratories/edit/<?= $lab['id'] ?>" class="btn btn-sm btn-outline-light" title="Edit Lab Details"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-lab" data-id="<?= $lab['id'] ?>" title="Delete Lab Room"><i class="fa-regular fa-trash-can"></i></button>
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
        $('.btn-delete-lab').on('click', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Delete Laboratory?',
                text: "Are you sure you want to delete this laboratory? This cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete it!',
                background: '#111827',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/laboratories/delete/' + id,
                        method: 'POST',
                        data: { csrf_token: '<?= csrf_token() ?>' },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: response.message,
                                confirmButtonColor: '#6366f1',
                                background: '#111827',
                                color: '#fff'
                            }).then(() => { window.location.reload(); });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Deletion Blocked',
                                text: xhr.responseJSON?.error || 'Failed to delete laboratory.',
                                confirmButtonColor: '#6366f1',
                                background: '#111827',
                                color: '#fff'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
