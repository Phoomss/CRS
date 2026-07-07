<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Modify Workstation Details</h5>
                <a href="/computers" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
            </div>
            <div class="card-body">
                
                <?php $errors = session()->getFlash('errors', []); ?>

                <form action="/computers/update/<?= $comp['id'] ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">Basic Information</h6>
                        <div class="col-md-4">
                            <label for="code" class="form-label">Workstation Code</label>
                            <input type="text" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" id="code" name="code" value="<?= esc($comp['code']) ?>" required>
                            <?php if (isset($errors['code'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['code'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="name" class="form-label">Workstation Name</label>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= esc($comp['name']) ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="asset_number" class="form-label">Asset Number / Tag</label>
                            <input type="text" class="form-control <?= isset($errors['asset_number']) ? 'is-invalid' : '' ?>" id="asset_number" name="asset_number" value="<?= esc($comp['asset_number']) ?>" required>
                            <?php if (isset($errors['asset_number'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['asset_number'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="laboratory_id" class="form-label">Laboratory Room Assignment</label>
                            <select class="form-select <?= isset($errors['laboratory_id']) ? 'is-invalid' : '' ?>" id="laboratory_id" name="laboratory_id" required>
                                <?php foreach ($labs as $l): ?>
                                    <option value="<?= $l['id'] ?>" <?= $comp['laboratory_id'] == $l['id'] ? 'selected' : '' ?>><?= esc($l['code']) ?> - <?= esc($l['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['laboratory_id'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['laboratory_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="ip_address" class="form-label">IP Address</label>
                            <input type="text" class="form-control <?= isset($errors['ip_address']) ? 'is-invalid' : '' ?>" id="ip_address" name="ip_address" value="<?= esc($comp['ip_address']) ?>" required>
                            <?php if (isset($errors['ip_address'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['ip_address'][0]) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">Hardware Specifications</h6>
                        <div class="col-md-6">
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" class="form-control <?= isset($errors['brand']) ? 'is-invalid' : '' ?>" id="brand" name="brand" value="<?= esc($comp['brand']) ?>" required>
                            <?php if (isset($errors['brand'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['brand'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="model" class="form-label">Model Name / Number</label>
                            <input type="text" class="form-control <?= isset($errors['model']) ? 'is-invalid' : '' ?>" id="model" name="model" value="<?= esc($comp['model']) ?>" required>
                            <?php if (isset($errors['model'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['model'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="cpu" class="form-label">CPU / Processor</label>
                            <input type="text" class="form-control <?= isset($errors['cpu']) ? 'is-invalid' : '' ?>" id="cpu" name="cpu" value="<?= esc($comp['cpu']) ?>" required>
                            <?php if (isset($errors['cpu'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['cpu'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="ram" class="form-label">RAM / Memory</label>
                            <input type="text" class="form-control <?= isset($errors['ram']) ? 'is-invalid' : '' ?>" id="ram" name="ram" value="<?= esc($comp['ram']) ?>" required>
                            <?php if (isset($errors['ram'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['ram'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="storage" class="form-label">Storage Capacity</label>
                            <input type="text" class="form-control <?= isset($errors['storage']) ? 'is-invalid' : '' ?>" id="storage" name="storage" value="<?= esc($comp['storage']) ?>" required>
                            <?php if (isset($errors['storage'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['storage'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="operating_system" class="form-label">Operating System</label>
                            <input type="text" class="form-control <?= isset($errors['operating_system']) ? 'is-invalid' : '' ?>" id="operating_system" name="operating_system" value="<?= esc($comp['operating_system']) ?>" required>
                            <?php if (isset($errors['operating_system'])): ?>
                                <div class="invalid-feedback"><?= esc($comp['operating_system'][0]) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <h6 class="text-secondary small text-uppercase fw-semibold m-0">Image & Status</h6>
                        <div class="col-md-6">
                            <label for="image" class="form-label">Replace Workstation Photo (Optional)</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <?php if (!empty($comp['image_path'])): ?>
                                <div class="mt-2 small text-secondary">
                                    Current file: <a href="<?= esc($comp['image_path']) ?>" target="_blank"><?= basename($comp['image_path']) ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Workstation Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="available" <?= $comp['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="reserved" <?= $comp['status'] === 'reserved' ? 'selected' : '' ?>>Reserved</option>
                                <option value="maintenance" <?= $comp['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                <option value="offline" <?= $comp['status'] === 'offline' ? 'selected' : '' ?>>Offline</option>
                                <option value="disabled" <?= $comp['status'] === 'disabled' ? 'selected' : '' ?>>Disabled</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top border-secondary border-opacity-10 pt-4">
                        <a href="/computers" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-save me-1"></i> Save Changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
