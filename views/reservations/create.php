<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-square-plus text-primary me-2"></i>New Computer Reservation</h5>
                <a href="/reservations" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
            </div>
            <div class="card-body">
                
                <form id="booking-form">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <!-- Step 1: Lab Selection -->
                        <div class="col-md-4">
                            <label for="laboratory_id" class="form-label text-secondary small">1. Select Laboratory Room</label>
                            <select id="laboratory_id" name="laboratory_id" class="form-select" required>
                                <option value="">-- Select Lab Room --</option>
                                <?php foreach ($labs as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= esc($l['code']) ?> - <?= esc($l['name']) ?> (Cap: <?= $l['capacity'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Step 2: Date Selection -->
                        <div class="col-md-3">
                            <label for="date" class="form-label text-secondary small">2. Reservation Date</label>
                            <input type="date" id="date" name="date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                        </div>

                        <!-- Step 3: Time Selection -->
                        <div class="col-md-2">
                            <label for="start_time" class="form-label text-secondary small">Start Time</label>
                            <input type="time" id="start_time" name="start_time" class="form-control" required>
                        </div>

                        <div class="col-md-2">
                            <label for="end_time" class="form-label text-secondary small">End Time</label>
                            <input type="time" id="end_time" name="end_time" class="form-control" required>
                        </div>

                        <!-- Search Button -->
                        <div class="col-md-1 d-grid">
                            <label class="form-label text-transparent small">Find</label>
                            <button type="button" id="btn-search-pcs" class="btn btn-indigo"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </div>

                    <!-- Step 4: Available Computers Grid -->
                    <div id="pc-selection-area" class="mb-4" style="display: none;">
                        <h6 class="text-secondary small mb-3 text-uppercase fw-semibold tracking-wider">3. Choose Available Workstation(s)</h6>
                        
                        <!-- PCs Grid -->
                        <div class="row g-3 row-cols-1 row-cols-md-3 row-cols-lg-4" id="pcs-grid"></div>
                        
                        <div id="no-pcs-message" class="text-center text-muted py-5" style="display: none;">
                            <i class="fa-solid fa-triangle-exclamation fa-2x mb-3 text-warning"></i>
                            <p class="m-0">No available workstations found for the selected room and timing parameters.</p>
                        </div>
                    </div>

                    <!-- Step 5: Purpose and Submit -->
                    <div id="submission-area" style="display: none;">
                        <div class="mb-4">
                            <label for="purpose" class="form-label text-secondary small">4. Purpose of Reservation</label>
                            <textarea id="purpose" name="purpose" rows="3" class="form-control" placeholder="e.g. Programming assignment, Database laboratory class project, network configuration exercise..." required></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold" style="border-radius: 10px;"><i class="fa-solid fa-circle-check me-2"></i> Submit Reservation</button>
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
                    title: 'Missing Fields',
                    text: 'Please select a laboratory room, date, start time, and end time first.',
                    confirmButtonColor: '#6366f1',
                    background: '#111827',
                    color: '#fff'
                });
                return;
            }

            // Show loading animation inside grid
            $('#pc-selection-area').show();
            $('#pcs-grid').html('<div class="col-12 text-center text-muted py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 small">Searching workstations...</p></div>');
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
                                <div class="card h-100 pc-card" style="cursor: pointer; border-color: rgba(255,255,255,0.06); background: rgba(15, 23, 42, 0.4);">
                                    <img src="${imgUrl}" class="card-img-top" alt="Workstation Image" style="height: 120px; object-fit: cover; border-top-left-radius: 15px; border-top-right-radius: 15px; opacity: 0.85;">
                                    <div class="card-body p-3">
                                        <div class="form-check m-0">
                                            <input class="form-check-input pc-checkbox" type="checkbox" name="computers[]" value="${pc.id}" id="pc-${pc.id}" style="float: right;">
                                            <label class="form-check-label d-block text-white fw-bold" for="pc-${pc.id}" style="cursor: pointer;">
                                                ${pc.code}
                                            </label>
                                        </div>
                                        <div class="text-secondary small mt-1">${pc.brand} ${pc.model}</div>
                                        <hr class="my-2 border-secondary opacity-25">
                                        <div style="font-size: 0.75rem; color: #94a3b8;">
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
                            card.css('background', 'rgba(99, 102, 241, 0.08)');
                        } else {
                            card.css('border-color', 'rgba(255,255,255,0.06)');
                            card.css('background', 'rgba(15, 23, 42, 0.4)');
                        }
                    });

                    $('#submission-area').show();
                },
                error: function(xhr) {
                    $('#pcs-grid').empty();
                    Swal.fire({
                        icon: 'error',
                        title: 'Search Failed',
                        text: xhr.responseJSON?.error || 'Could not fetch computers.',
                        confirmButtonColor: '#6366f1',
                        background: '#111827',
                        color: '#fff'
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
                    title: 'Workstation Required',
                    text: 'Please select at least one computer workstation card from the list.',
                    confirmButtonColor: '#6366f1',
                    background: '#111827',
                    color: '#fff'
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
                        title: 'Booking Submitted!',
                        text: response.message,
                        confirmButtonColor: '#6366f1',
                        background: '#111827',
                        color: '#fff'
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
                            title: 'Validation Error',
                            html: errMsgs,
                            confirmButtonColor: '#6366f1',
                            background: '#111827',
                            color: '#fff'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Failed',
                            text: xhr.responseJSON?.error || 'A double-booking conflict occurred. Please refresh availability.',
                            confirmButtonColor: '#6366f1',
                            background: '#111827',
                            color: '#fff'
                        });
                    }
                }
            });
        });
    });
</script>
