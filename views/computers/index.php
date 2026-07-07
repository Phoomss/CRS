<div class="card mb-4">
    <div class="card-header border-0 pb-0">
        <h5 class="fw-bold m-0"><i class="fa-solid fa-display text-indigo me-2"></i>Manage Workstations (Computers)</h5>
        <div class="d-flex gap-2">
            <a href="/computers/print" target="_blank" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-print me-1"></i> Print QR/Barcodes</a>
            <a href="/computers/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Register Workstation</a>
        </div>
    </div>
    <div class="card-body">
        
        <!-- Filter Form -->
        <form action="/computers" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-secondary small">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Code, name, brand, IP address..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Laboratory Room</label>
                <select name="laboratory_id" class="form-select">
                    <option value="">All Rooms</option>
                    <?php foreach ($labs as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= ($filters['laboratory_id'] ?? '') == $l['id'] ? 'selected' : '' ?>><?= esc($l['code']) ?> - <?= esc($l['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="reserved" <?= ($filters['status'] ?? '') === 'reserved' ? 'selected' : '' ?>>Reserved</option>
                    <option value="maintenance" <?= ($filters['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    <option value="offline" <?= ($filters['status'] ?? '') === 'offline' ? 'selected' : '' ?>>Offline</option>
                    <option value="disabled" <?= ($filters['status'] ?? '') === 'disabled' ? 'selected' : '' ?>>Disabled</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-indigo flex-grow-1"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="/computers" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle m-0" id="computers-table">
                <thead>
                    <tr>
                        <th>Workstation Code</th>
                        <th>Device Info</th>
                        <th>Asset Number</th>
                        <th>Room Location</th>
                        <th>IP Address</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($computers)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No computer workstations registered in the system yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($computers as $c): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= $c['image_path'] ? esc($c['image_path']) : 'https://images.unsplash.com/photo-1547082299-de196ea013d6?auto=format&fit=crop&w=80&q=80' ?>" alt="PC Image" class="rounded" style="width: 45px; height: 45px; object-fit: cover; border: 1px solid rgba(255,255,255,0.05);">
                                        <div>
                                            <span class="fw-bold text-white"><?= esc($c['code']) ?></span>
                                            <div class="text-secondary small"><?= esc($c['name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold small text-white"><?= esc($c['brand']) ?> <?= esc($c['model']) ?></div>
                                    <div class="small text-secondary" style="font-size: 0.75rem;"><?= esc($c['cpu']) ?> / <?= esc($c['ram']) ?> / <?= esc($c['storage']) ?></div>
                                </td>
                                <td><span class="badge bg-indigo-subtle text-indigo" style="font-size: 0.8rem;"><?= esc($c['asset_number']) ?></span></td>
                                <td>
                                    <div class="fw-semibold text-white"><?= esc($c['laboratory_code']) ?></div>
                                    <div class="small text-secondary text-truncate" style="max-width: 140px;"><?= esc($c['laboratory_name']) ?></div>
                                </td>
                                <td><code><?= esc($c['ip_address']) ?></code></td>
                                <td class="text-center">
                                    <?php
                                        // Available, Reserved, Maintenance, Offline, Disabled
                                        $class = 'bg-success';
                                        if ($c['status'] === 'reserved') $class = 'bg-info text-dark';
                                        if ($c['status'] === 'maintenance') $class = 'bg-warning text-dark';
                                        if ($c['status'] === 'offline') $class = 'bg-secondary';
                                        if ($c['status'] === 'disabled') $class = 'bg-danger';
                                    ?>
                                    <span class="badge badge-status <?= $class ?>"><?= esc(ucfirst($c['status'])) ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/computers/edit/<?= $c['id'] ?>" class="btn btn-sm btn-outline-light" title="Edit workstation"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-computer" data-id="<?= $c['id'] ?>" title="Delete workstation"><i class="fa-regular fa-trash-can"></i></button>
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
        $('.btn-delete-computer').on('click', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Delete Workstation?',
                text: "Are you sure you want to delete this computer? This cannot be undone.",
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
                        url: '/computers/delete/' + id,
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
                                title: 'Failed to Delete',
                                text: xhr.responseJSON?.error || 'Failed to delete workstation.',
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
