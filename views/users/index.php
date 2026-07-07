<div class="card mb-4">
    <div class="card-header border-0 pb-0">
        <h5 class="fw-bold m-0"><i class="fa-solid fa-users text-indigo me-2"></i>Manage User Accounts</h5>
        <a href="/users/create" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus me-1"></i> Add User Account</a>
    </div>
    <div class="card-body">
        
        <!-- Filter Form -->
        <form action="/users" method="GET" class="row g-3 mb-4 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-secondary small">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search name, email, student ID, employee ID..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">System Role</label>
                <select name="role_id" class="form-select">
                    <option value="">All Roles</option>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($filters['role_id'] ?? '') == $r['id'] ? 'selected' : '' ?>><?= esc($r['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-secondary small">Account Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="suspended" <?= ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-indigo flex-grow-1"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="/users" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle m-0" id="users-table">
                <thead>
                    <tr>
                        <th>User Account Details</th>
                        <th>Unique Identification</th>
                        <th>Phone Number</th>
                        <th>System Role</th>
                        <th class="text-center">Account Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No user accounts found matching current parameters.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($u['first_name'] . ' ' . $u['last_name']) ?>&background=6366f1&color=fff&bold=true" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px;">
                                        <div>
                                            <span class="fw-bold text-white"><?= esc($u['first_name']) ?> <?= esc($u['last_name']) ?></span>
                                            <div class="text-secondary small"><?= esc($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($u['student_id']): ?>
                                        <div class="small">Student ID:</div>
                                        <span class="badge bg-secondary"><?= esc($u['student_id']) ?></span>
                                    <?php elseif ($u['employee_id']): ?>
                                        <div class="small">Employee ID:</div>
                                        <span class="badge bg-secondary"><?= esc($u['employee_id']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">None</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($u['phone_number'] ?? '-') ?></td>
                                <td>
                                    <span class="fw-semibold text-white"><?= esc($u['role_name']) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $class = 'bg-success';
                                        if ($u['status'] === 'inactive') $class = 'bg-secondary';
                                        if ($u['status'] === 'suspended') $class = 'bg-danger';
                                    ?>
                                    <span class="badge badge-status <?= $class ?>"><?= esc(ucfirst($u['status'])) ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="/users/edit/<?= $u['id'] ?>" class="btn btn-sm btn-outline-light" title="Edit User"><i class="fa-regular fa-pen-to-square"></i></a>
                                        <button class="btn btn-sm btn-outline-danger btn-delete-user" data-id="<?= $u['id'] ?>" title="Delete User"><i class="fa-regular fa-trash-can"></i></button>
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
        $('.btn-delete-user').on('click', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Delete User Account?',
                text: "Are you sure you want to delete this user? This cannot be undone.",
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
                        url: '/users/delete/' + id,
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
                                text: xhr.responseJSON?.error || 'Failed to delete user.',
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
