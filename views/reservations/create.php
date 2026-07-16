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
    
    /* PC Card Design Customization */
    .pc-card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .pc-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.08);
        border-color: rgba(99, 102, 241, 0.2);
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
</style>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card form-card mb-4">
            <div class="form-card-header">
                <h5 class="m-0"><i class="fa-solid fa-square-plus me-2"></i>ส่งคำขอจองเครื่องคอมพิวเตอร์ใหม่</h5>
                <a href="/reservations" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> กลับไปยังหน้ารายการ</a>
            </div>
            <div class="card-body p-4">
                
                <form id="booking-form">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-5">
                        <!-- Step 1: Lab Selection -->
                        <div class="col-md-4">
                            <label for="laboratory_id" class="form-label">1. เลือกห้องปฏิบัติการ <span class="text-danger">*</span></label>
                            <select id="laboratory_id" name="laboratory_id" class="form-select" required>
                                <option value="">-- เลือกห้องแล็บ --</option>
                                <?php foreach ($labs as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= esc($l['code']) ?> - <?= esc($l['name']) ?> (จำนวนที่นั่ง: <?= $l['capacity'] ?> เครื่อง)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Step 2: Date Selection -->
                        <div class="col-md-3">
                            <label for="date" class="form-label">2. เลือกวันที่ต้องการจอง <span class="text-danger">*</span></label>
                            <input type="date" id="date" name="date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- Step 3: Time Selection -->
                        <div class="col-md-2">
                            <label for="start_time" class="form-label">เวลาเริ่มต้น <span class="text-danger">*</span></label>
                            <input type="time" id="start_time" name="start_time" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label for="end_time" class="form-label">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                            <input type="time" id="end_time" name="end_time" class="form-control" required>
                        </div>

                        <!-- Search Button -->
                        <div class="col-md-1 d-grid">
                            <label class="form-label text-transparent small">ค้นหา</label>
                            <button type="button" id="btn-search-pcs" class="btn btn-indigo" title="ค้นหาเครื่องที่ว่าง"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </div>

                    <!-- Step 4: Available Computers Grid -->
                    <div id="pc-selection-area" class="mb-5" style="display: none;">
                        <h6 class="section-title">3. เลือกเครื่องคอมพิวเตอร์ที่ว่าง</h6>
                        
                        <!-- PCs Grid -->
                        <div class="row g-3 row-cols-1 row-cols-md-3 row-cols-lg-4" id="pcs-grid"></div>
                        
                        <div id="no-pcs-message" class="text-center text-muted py-5" style="display: none;">
                            <i class="fa-solid fa-triangle-exclamation fa-2x mb-3 text-warning"></i>
                            <p class="m-0 fw-semibold">ไม่พบเครื่องคอมพิวเตอร์ที่ว่างในเงื่อนไขและห้องแล็บที่ท่านเลือก</p>
                            <span class="small text-secondary">กรุณาลองเปลี่ยนวันที่หรือช่วงเวลาเข้าใช้งานใหม่อีกครั้ง</span>
                        </div>
                    </div>

                    <!-- Step 5: Purpose and Submit -->
                    <div id="submission-area" style="display: none;">
                        <div class="mb-4">
                            <label for="purpose" class="form-label">4. วัตถุประสงค์ในการขอเข้าใช้งานเครื่อง <span class="text-danger">*</span></label>
                            <textarea id="purpose" name="purpose" rows="3" class="form-control" placeholder="เช่น ทำงานการบ้านเขียนโปรแกรมวิชาคอมพิวเตอร์, ฝึกหัดการตั้งค่าระบบเน็ตเวิร์ก, ศึกษาค้นคว้าด้วยตนเอง..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-end border-top border-secondary border-opacity-10 pt-4 mt-5">
                            <button type="submit" class="btn btn-indigo px-5 py-2 fw-semibold" style="border-radius: 10px;"><i class="fa-solid fa-circle-check me-2"></i> ส่งคำขอจองเครื่องคอมพิวเตอร์</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<!-- AJAX Search and Submit Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search available computers
        $('#btn-search-pcs').on('click', function() {
            var labId = $('#laboratory_id').val();
            var date = $('#date').val();
            var startTime = $('#start_time').val();
            var endTime = $('#end_time').val();

            if (!labId || !date || !startTime || !endTime) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรอกข้อมูลไม่ครบถ้วน',
                    text: 'กรุณาเลือกห้องปฏิบัติการ, วันที่, เวลาเริ่มต้น และเวลาสิ้นสุดก่อนค้นหาเครื่องคอมฯ ว่าง',
                    confirmButtonColor: '#6366f1',
                    background: '#ffffff',
                    color: '#1e293b'
                });
                return;
            }

            // Show loading animation inside grid
            $('#pc-selection-area').show();
            $('#pcs-grid').html('<div class="col-12 text-center text-muted py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 small">กำลังค้นหาเครื่องคอมพิวเตอร์ที่ว่างในระบบ...</p></div>');
            $('#no-pcs-message').hide();
            $('#submission-area').hide();

            $.ajax({
                url: '/reservations/available-computers',
                method: 'GET',
                data: {
                    laboratory_id: labId,
                    date: date,
                    start_time: startTime,
                    end_time: endTime
                },
                success: function(response) {
                    var computers = response.computers || [];
                    $('#pcs-grid').empty();

                    if (computers.length === 0) {
                        $('#no-pcs-message').show();
                        return;
                    }

                    // Render computers as checkbox cards
                    computers.forEach(function(pc) {
                        var imgUrl = pc.image_path ? pc.image_path : 'https://images.unsplash.com/photo-1547082299-de196ea013d6?auto=format&fit=crop&w=300&q=80';
                        
                        var cardHtml = `
                            <div class="col">
                                <div class="card h-100 pc-card" style="cursor: pointer;" id="card-pc-${pc.id}">
                                    <img src="${imgUrl}" class="card-img-top" alt="Workstation Image" style="height: 120px; object-fit: cover; opacity: 0.85;">
                                    <div class="card-body p-3">
                                        <div class="form-check m-0">
                                            <input class="form-check-input pc-checkbox" type="checkbox" name="computers[]" value="${pc.id}" id="pc-${pc.id}" style="float: right;">
                                            <label class="form-check-label d-block text-dark fw-bold" for="pc-${pc.id}" style="cursor: pointer;">
                                                ${pc.code}
                                            </label>
                                        </div>
                                        <div class="text-secondary small mt-1">${pc.brand} ${pc.model}</div>
                                        <hr class="my-2 border-secondary opacity-10">
                                        <div style="font-size: 0.75rem; color: #475569;">
                                            <div><strong>CPU:</strong> ${pc.cpu}</div>
                                            <div><strong>RAM:</strong> ${pc.ram}</div>
                                            <div><strong>OS:</strong> ${pc.operating_system}</div>
                                            <div><strong>IP:</strong> ${pc.ip_address}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#pcs-grid').append(cardHtml);
                    });

                    // Add card click toggle behaviour
                    $('.pc-card').on('click', function(e) {
                        if ($(e.target).hasClass('pc-checkbox')) return;
                        var checkbox = $(this).find('.pc-checkbox');
                        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                    });

                    $('.pc-checkbox').on('change', function() {
                        var card = $(this).closest('.pc-card');
                        if (this.checked) {
                            card.css('border-color', '#6366f1');
                            card.css('background-color', 'rgba(99, 102, 241, 0.06)');
                            card.css('box-shadow', '0 8px 24px rgba(99, 102, 241, 0.12)');
                        } else {
                            card.css('border-color', '#e2e8f0');
                            card.css('background-color', '#ffffff');
                            card.css('box-shadow', 'none');
                        }
                    });

                    $('#submission-area').show();
                },
                error: function(xhr) {
                    $('#pcs-grid').empty();
                    Swal.fire({
                        icon: 'error',
                        title: 'การค้นหาล้มเหลว',
                        text: xhr.responseJSON?.error || 'ไม่สามารถเรียกข้อมูลเครื่องคอมพิวเตอร์ได้',
                        confirmButtonColor: '#6366f1',
                        background: '#ffffff',
                        color: '#1e293b'
                    });
                }
            });
        });

        // Submit Booking AJAX handler
        $('#booking-form').on('submit', function(e) {
            e.preventDefault();

            var selectedCompCount = $('.pc-checkbox:checked').length;
            if (selectedCompCount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกเครื่องคอมฯ',
                    text: 'กรุณาเลือกเครื่องคอมพิวเตอร์ที่คุณต้องการจองอย่างน้อยหนึ่งเครื่องจากในรายการด้านบน',
                    confirmButtonColor: '#6366f1',
                    background: '#ffffff',
                    color: '#1e293b'
                });
                return;
            }

            var formData = {
                laboratory_id: $('#laboratory_id').val(),
                date: $('#date').val(),
                start_time: $('#start_time').val(),
                end_time: $('#end_time').val(),
                purpose: $('#purpose').val(),
                csrf_token: '<?= csrf_token() ?>',
                computers: []
            };

            $('.pc-checkbox:checked').each(function() {
                formData.computers.push($(this).val());
            });

            $.ajax({
                url: '/reservations/store',
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ส่งคำขอจองสำเร็จ!',
                        text: response.message || 'ส่งข้อมูลรายละเอียดการจองเข้าระบบเรียบร้อยแล้ว',
                        confirmButtonColor: '#6366f1',
                        background: '#ffffff',
                        color: '#1e293b'
                    }).then(() => {
                        window.location.href = '/reservations/view/' + response.reservation_id;
                    });
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errMsgs = '';
                        var errors = xhr.responseJSON?.errors || {};
                        for (var field in errors) {
                            errMsgs += errors[field].join('<br>') + '<br>';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'ข้อมูลไม่ถูกต้อง',
                            html: errMsgs,
                            confirmButtonColor: '#6366f1',
                            background: '#ffffff',
                            color: '#1e293b'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'ส่งคำขอจองล้มเหลว',
                            text: xhr.responseJSON?.error || 'เกิดปัญหาจองชนกันซ้ำซ้อน กรุณารีเฟรชตรวจสอบสถานะว่างใหม่อีกครั้ง',
                            confirmButtonColor: '#6366f1',
                            background: '#ffffff',
                            color: '#1e293b'
                        });
                    }
                }
            });
        });
    });
</script>
