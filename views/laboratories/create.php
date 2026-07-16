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
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card form-card mb-4">
            <div class="form-card-header">
                <h5 class="m-0"><i class="fa-solid fa-square-plus me-2"></i>ลงทะเบียนห้องปฏิบัติการคอมพิวเตอร์ใหม่</h5>
                <a href="/laboratories" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> กลับไปยังหน้ารายการ</a>
            </div>
            <div class="card-body p-4">
                
                <?php 
                    $errors = session()->getFlash('errors', []); 
                    $old = session()->getFlash('old_inputs', []);
                ?>

                <form action="/laboratories/store" method="POST">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <h6 class="section-title">ข้อมูลทั่วไปของห้องปฏิบัติการ</h6>
                        </div>

                        <div class="col-md-4">
                            <label for="code" class="form-label">รหัสห้องปฏิบัติการ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" id="code" name="code" placeholder="เช่น LAB101" value="<?= esc($old['code'] ?? '') ?>" required>
                            <?php if (isset($errors['code'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['code'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-8">
                            <label for="name" class="form-label">ชื่อห้องปฏิบัติการ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" placeholder="เช่น ห้องปฏิบัติการวิศวกรรมซอฟต์แวร์" value="<?= esc($old['name'] ?? '') ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['name'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-5">
                            <label for="building" class="form-label">อาคารที่ตั้ง <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['building']) ? 'is-invalid' : '' ?>" id="building" name="building" placeholder="เช่น อาคารเรียนรวม 1" value="<?= esc($old['building'] ?? '') ?>" required>
                            <?php if (isset($errors['building'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['building'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="floor" class="form-label">ชั้นที่ตั้ง <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['floor']) ? 'is-invalid' : '' ?>" id="floor" name="floor" placeholder="เช่น ชั้น 4" value="<?= esc($old['floor'] ?? '') ?>" required>
                            <?php if (isset($errors['floor'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['floor'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-3">
                            <label for="capacity" class="form-label">จำนวนเครื่องคอมพิวเตอร์ที่ติดตั้ง <span class="text-danger">*</span></label>
                            <input type="number" class="form-control <?= isset($errors['capacity']) ? 'is-invalid' : '' ?>" id="capacity" name="capacity" placeholder="เช่น 30" value="<?= esc($old['capacity'] ?? '') ?>" required>
                            <?php if (isset($errors['capacity'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['capacity'][0]) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-12">
                            <label for="description" class="form-label">คำอธิบาย/หมายเหตุเพิ่มเติมเกี่ยวกับห้องแล็บ (ไม่จำเป็น)</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="อธิบายรายวิชาที่สอนในห้องนี้ หรืออุปกรณ์พิเศษและซอฟต์แวร์ที่ติดตั้งในแล็บนี้..."><?= esc($old['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top border-secondary border-opacity-10 pt-4 mt-5">
                        <a href="/laboratories" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 10px;">ยกเลิก</a>
                        <button type="submit" class="btn btn-indigo px-4 py-2" style="border-radius: 10px;"><i class="fa-solid fa-save me-1"></i> บันทึกข้อมูลห้องปฏิบัติการ</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
