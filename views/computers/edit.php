<style>
    .form-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.05);
        border: 1px solid rgba(99, 102, 241, 0.08);
        background-color: #ffffff;
        overflow: hidden;
    }
    .form-card-header {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        padding: 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .form-card-header h5 {
        color: #ffffff;
        font-weight: 700;
    }
    .form-card-header .btn-outline-light {
        border: 1px solid rgba(255, 255, 255, 0.4) !important;
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        font-weight: 500;
        border-radius: 10px;
        padding: 6px 14px;
        transition: all 0.2s ease;
    }
    .form-card-header .btn-outline-light:hover {
        background-color: #ffffff !important;
        color: #4f46e5 !important;
        transform: translateY(-1px);
    }
    .section-title {
        position: relative;
        padding-left: 12px;
        color: #4f46e5;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 2px;
        bottom: 2px;
        width: 4px;
        background-color: #6366f1;
        border-radius: 4px;
    }
    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        margin-bottom: 6px;
    }
    .form-control, .form-select {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.92rem;
        background-color: #f9fafb;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        background-color: #ffffff;
    }
    .input-group-icon {
        position: relative;
    }
    .input-group-icon i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .input-group-icon .form-control {
        padding-left: 40px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card form-card mb-4">
            <div class="form-card-header">
                <h5 class="m-0"><i class="fa-solid fa-pen-to-square me-2"></i>แก้ไขรายละเอียดเครื่องคอมพิวเตอร์</h5>
                <a href="/computers" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> กลับไปยังหน้ารายการ</a>
            </div>
            <div class="card-body p-4">
                
                <?php $errors = session()->getFlash('errors', []); ?>

                <form action="/computers/update/<?= $comp['id'] ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <!-- Section: Basic Info -->
                    <div class="row g-3 mb-5">
                        <div class="col-12">
                            <h6 class="section-title">ข้อมูลพื้นฐานตัวเครื่อง</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="code" class="form-label">รหัสเครื่องคอมฯ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" id="code" name="code" value="<?= esc($comp['code']) ?>" required>
                            <?php if (isset($errors['code'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['code'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="name" class="form-label">ชื่อเครื่องคอมฯ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= esc($comp['name']) ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="asset_number" class="form-label">รหัสครุภัณฑ์ / ป้ายคุมครุภัณฑ์ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['asset_number']) ? 'is-invalid' : '' ?>" id="asset_number" name="asset_number" value="<?= esc($comp['asset_number']) ?>" required>
                            <?php if (isset($errors['asset_number'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['asset_number'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="laboratory_id" class="form-label">ห้องปฏิบัติการที่ตั้งเครื่อง <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['laboratory_id']) ? 'is-invalid' : '' ?>" id="laboratory_id" name="laboratory_id" required>
                                <?php foreach ($labs as $l): ?>
                                    <option value="<?= $l['id'] ?>" <?= $comp['laboratory_id'] == $l['id'] ? 'selected' : '' ?>><?= esc($l['code']) ?> - <?= esc($l['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['laboratory_id'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['laboratory_id'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="ip_address" class="form-label">ที่อยู่ IP Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['ip_address']) ? 'is-invalid' : '' ?>" id="ip_address" name="ip_address" value="<?= esc($comp['ip_address']) ?>" required>
                            <?php if (isset($errors['ip_address'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['ip_address'][0]) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Section: Hardware Specs -->
                    <div class="row g-3 mb-5">
                        <div class="col-12">
                            <h6 class="section-title">ข้อมูลสเปกและฮาร์ดแวร์</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="brand" class="form-label">ยี่ห้อ (Brand) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['brand']) ? 'is-invalid' : '' ?>" id="brand" name="brand" value="<?= esc($comp['brand']) ?>" required>
                            <?php if (isset($errors['brand'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['brand'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="model" class="form-label">รุ่น (Model) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['model']) ? 'is-invalid' : '' ?>" id="model" name="model" value="<?= esc($comp['model']) ?>" required>
                            <?php if (isset($errors['model'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['model'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="cpu" class="form-label">หน่วยประมวลผล (CPU) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['cpu']) ? 'is-invalid' : '' ?>" id="cpu" name="cpu" value="<?= esc($comp['cpu']) ?>" required>
                            <?php if (isset($errors['cpu'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['cpu'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="ram" class="form-label">หน่วยความจำ (RAM) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['ram']) ? 'is-invalid' : '' ?>" id="ram" name="ram" value="<?= esc($comp['ram']) ?>" required>
                            <?php if (isset($errors['ram'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['ram'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="storage" class="form-label">ความจุพื้นที่เก็บข้อมูล (Storage) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['storage']) ? 'is-invalid' : '' ?>" id="storage" name="storage" value="<?= esc($comp['storage']) ?>" required>
                            <?php if (isset($errors['storage'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['storage'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="operating_system" class="form-label">ระบบปฏิบัติการ (OS) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['operating_system']) ? 'is-invalid' : '' ?>" id="operating_system" name="operating_system" value="<?= esc($comp['operating_system']) ?>" required>
                            <?php if (isset($errors['operating_system'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['operating_system'][0]) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Section: Image & Status -->
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="section-title">รูปภาพและสถานะการใช้งาน</h6>
                        </div>
                        <div class="col-md-6">
                            <label for="image" class="form-label">อัปโหลดภาพเครื่องคอมพิวเตอร์ใหม่ (ไม่จำเป็น)</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <?php if (!empty($comp['image_path'])): ?>
                                <div class="mt-2 small text-secondary">
                                    ไฟล์รูปภาพปัจจุบัน: <a href="<?= esc($comp['image_path']) ?>" target="_blank" class="fw-medium"><?= basename($comp['image_path']) ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">สถานะของเครื่อง</label>
                            <select class="form-select" id="status" name="status">
                                <option value="available" <?= $comp['status'] === 'available' ? 'selected' : '' ?>>ว่างพร้อมใช้งาน</option>
                                <option value="reserved" <?= $comp['status'] === 'reserved' ? 'selected' : '' ?>>ถูกจองแล้ว</option>
                                <option value="maintenance" <?= $comp['status'] === 'maintenance' ? 'selected' : '' ?>>อยู่ระหว่างบำรุงรักษา</option>
                                <option value="offline" <?= $comp['status'] === 'offline' ? 'selected' : '' ?>>ปิดใช้งานชั่วคราว</option>
                                <option value="disabled" <?= $comp['status'] === 'disabled' ? 'selected' : '' ?>>งดใช้งาน</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top border-secondary border-opacity-10 pt-4 mt-5">
                        <a href="/computers" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 10px;">ยกเลิก</a>
                        <button type="submit" class="btn btn-indigo px-4 py-2" style="border-radius: 10px;"><i class="fa-solid fa-save me-1"></i> บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
