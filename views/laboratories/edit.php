<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i>Modify Laboratory Room Details</h5>
                <a href="/laboratories" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
            </div>
            <div class="card-body">
                
                <?php $errors = session()->getFlash('errors', []); ?>

                <form action="/laboratories/update/<?= $lab['id'] ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="code" class="form-label">Laboratory Code</label>
                            <input type="text" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" id="code" name="code" value="<?= esc($lab['code']) ?>" required>
                            <?php if (isset($errors['code'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['code'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-8">
                            <label for="name" class="form-label">Laboratory Name</label>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= esc($lab['name']) ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-5">
                            <label for="building" class="form-label">Building Location</label>
                            <input type="text" class="form-control <?= isset($errors['building']) ? 'is-invalid' : '' ?>" id="building" name="building" value="<?= esc($lab['building']) ?>" required>
                            <?php if (isset($errors['building'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['building'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="floor" class="form-label">Floor Location</label>
                            <input type="text" class="form-control <?= isset($errors['floor']) ? 'is-invalid' : '' ?>" id="floor" name="floor" value="<?= esc($lab['floor']) ?>" required>
                            <?php if (isset($errors['floor'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['floor'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="capacity" class="form-label">Workstation Capacity</label>
                            <input type="number" class="form-control <?= isset($errors['capacity']) ? 'is-invalid' : '' ?>" id="capacity" name="capacity" value="<?= esc($lab['capacity']) ?>" required>
                            <?php if (isset($errors['capacity'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['capacity'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-9">
                            <label for="description" class="form-label">Laboratory Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= esc($lab['description']) ?></textarea>
                        </div>

                        <div class="col-md-3">
                            <label for="status" class="form-label">Laboratory Room Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= $lab['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $lab['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top border-secondary border-opacity-10 pt-4">
                        <a href="/laboratories" class="btn btn-outline-secondary px-4 py-2">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 py-2"><i class="fa-solid fa-save me-1"></i> Save Changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
