<div class="row g-4 mb-4">
    <!-- Stat 1: Available computers -->
    <div class="col-md-3">
        <div class="card p-3 border-0 h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid rgba(34, 197, 94, 0.2) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-success small fw-semibold text-uppercase" style="color: #166534 !important;">Workstations Free</span>
                    <h3 class="mt-1 mb-0 fw-bold" style="color: #14532d !important;"><?= $availableCompCount ?> <span class="small" style="color: #166534; opacity: 0.7; font-size: 0.9rem;">/ <?= $computersCount ?></span></h3>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(34, 197, 94, 0.12); color: #15803d;">
                    <i class="fa-solid fa-circle-check fa-xl"></i>
                </div>
            </div>
            <div class="mt-3 small" style="color: #166534; opacity: 0.85;">Active and available for booking</div>
        </div>
    </div>

    <!-- Stat 2: Active / Approved Bookings -->
    <div class="col-md-3">
        <div class="card p-3 border-0 h-100" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1px solid rgba(99, 102, 241, 0.2) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-indigo small fw-semibold text-uppercase" style="color: #3730a3 !important;">Approved Seats</span>
                    <h3 class="mt-1 mb-0 fw-bold" style="color: #1e1b4b !important;"><?= $approvedResCount ?></h3>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5;">
                    <i class="fa-regular fa-calendar-check fa-xl"></i>
                </div>
            </div>
            <div class="mt-3 small" style="color: #3730a3; opacity: 0.85;">Confirmed schedules awaiting check-in</div>
        </div>
    </div>

    <!-- Stat 3: Pending Approvals -->
    <div class="col-md-3">
        <div class="card p-3 border-0 h-100" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid rgba(245, 158, 11, 0.2) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-warning small fw-semibold text-uppercase" style="color: #b45309 !important;">Pending Review</span>
                    <h3 class="mt-1 mb-0 fw-bold" style="color: #78350f !important;"><?= $pendingResCount ?></h3>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                    <i class="fa-solid fa-hourglass-half fa-xl"></i>
                </div>
            </div>
            <div class="mt-3 small" style="color: #b45309; opacity: 0.85;">Requires administrator approval</div>
        </div>
    </div>

    <!-- Stat 4: Workstations under maintenance -->
    <div class="col-md-3">
        <div class="card p-3 border-0 h-100" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid rgba(239, 68, 68, 0.2) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-danger small fw-semibold text-uppercase" style="color: #c2410c !important;">Under Maintenance</span>
                    <h3 class="mt-1 mb-0 fw-bold" style="color: #7f1d1d !important;"><?= $maintenanceCompCount ?> <span class="small" style="color: #7f1d1d; opacity: 0.7; font-size: 0.9rem;">/ <?= $offlineCompCount ?> off</span></h3>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                    <i class="fa-solid fa-screwdriver-wrench fa-xl"></i>
                </div>
            </div>
            <div class="mt-3 small" style="color: #c2410c; opacity: 0.85;">Offline workstations being serviced</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Full Calendar Schedule Widget -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h5 class="m-0 fw-bold"><i class="fa-regular fa-calendar me-2 text-primary"></i>Reservation Schedule</h5>
                <?php if (has_permission('create_reservations')): ?>
                    <a href="/reservations/create" class="btn btn-sm btn-primary" style="border-radius: 8px;"><i class="fa-solid fa-plus me-1"></i> New Booking</a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div id="calendar" style="min-height: 480px;"></div>
            </div>
        </div>
    </div>

    <!-- Right Column: Charts & Statistics -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <?php if (!empty($monthlyTrends)): ?>
            <!-- Chart 1: Monthly Trends -->
            <div class="card flex-grow-1">
                <div class="card-header border-0 pb-0">
                    <h6 class="m-0 fw-bold"><i class="fa-solid fa-chart-line me-2 text-indigo"></i>Reservation Trends</h6>
                </div>
                <div class="card-body py-2">
                    <canvas id="monthlyTrendChart" style="max-height: 180px;"></canvas>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($statusStats)): ?>
            <!-- Chart 2: Status breakdown -->
            <div class="card flex-grow-1">
                <div class="card-header border-0 pb-0">
                    <h6 class="m-0 fw-bold"><i class="fa-solid fa-chart-pie me-2 text-indigo"></i>Status Breakdown</h6>
                </div>
                <div class="card-body py-2">
                    <canvas id="statusBreakdownChart" style="max-height: 180px;"></canvas>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($monthlyTrends) && empty($statusStats)): ?>
            <!-- Student Helpful Links Widget -->
            <div class="card flex-grow-1">
                <div class="card-header border-0 pb-0">
                    <h6 class="m-0 fw-bold"><i class="fa-solid fa-circle-info me-2 text-primary"></i>Helpful Instructions</h6>
                </div>
                <div class="card-body text-secondary small">
                    <p class="mb-2">Welcome to the Department Computer Lab Booking System.</p>
                    <ul class="ps-3 mb-3">
                        <li class="mb-1">Submit your reservations up to 1 week in advance.</li>
                        <li class="mb-1">Approved bookings must check in within 15 minutes of the start time.</li>
                        <li class="mb-1">Always remember to check out when you finish to release the computer for other students.</li>
                    </ul>
                    <a href="/reservations/create" class="btn btn-sm btn-outline-indigo w-100">Make a Booking Now</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (has_role(['Super Administrator', 'Department Administrator', 'Staff'])): ?>
<div class="row g-4">
    <!-- Popular Workstations -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-fire text-warning me-2"></i>Most Booked Computers</h5>
            </div>
            <div class="card-body">
                <?php if (empty($mostUsedComputers)): ?>
                    <div class="text-center text-muted py-4">No computer bookings recorded yet.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover m-0">
                            <thead>
                                <tr>
                                    <th>Workstation Code</th>
                                    <th>Lab Room</th>
                                    <th class="text-center">Bookings Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mostUsedComputers as $c): ?>
                                    <tr>
                                        <td><span class="fw-semibold text-white"><?= esc($c['code']) ?></span></td>
                                        <td><?= esc($c['laboratory_name']) ?></td>
                                        <td class="text-center"><span class="badge bg-indigo"><?= $c['reservation_count'] ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h5 class="m-0 fw-bold"><i class="fa-solid fa-list-check me-2 text-primary"></i>Recent Activities</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recentLogs)): ?>
                    <div class="text-center text-muted py-4">No logged actions recorded yet.</div>
                <?php else: ?>
                    <div class="activity-timeline" style="max-height: 290px; overflow-y: auto; padding-right: 5px;">
                        <?php foreach ($recentLogs as $log): ?>
                            <div class="d-flex mb-3 border-bottom border-secondary border-opacity-10 pb-2" style="font-size: 0.85rem;">
                                <div class="me-3 text-secondary pt-1" style="min-width: 65px; font-size: 0.75rem;">
                                    <?= date('H:i A', strtotime($log['created_at'])) ?>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="text-white fw-semibold"><?= esc($log['first_name'] . ' ' . $log['last_name'] ?? 'System') ?></span>
                                    <span class="text-muted">performed</span> 
                                    <span class="badge bg-secondary" style="font-size: 0.7rem;"><?= esc($log['action']) ?></span>
                                    <div class="text-secondary mt-1 text-truncate" style="max-width: 320px; font-size: 0.8rem;"><?= esc($log['details']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Dashboard Page Initializations -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Setup FullCalendar
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'bootstrap5',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '/reservations/calendar-data',
            eventClick: function(info) {
                if (info.event.url) {
                    info.jsEvent.preventDefault();
                    window.location.href = info.event.url;
                }
            },
            height: 'auto'
        });
        calendar.render();

        // 2. Setup Chart.js (Trends)
        <?php if (!empty($monthlyTrends)): ?>
            var trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
            var trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_column($monthlyTrends, 'label')) ?>,
                    datasets: [{
                        label: 'Bookings',
                        data: <?= json_encode(array_column($monthlyTrends, 'value')) ?>,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.15)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#9ca3af' } },
                        y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#9ca3af', precision: 0 } }
                    }
                }
            });
        <?php endif; ?>

        // 3. Setup Chart.js (Status Breakdown)
        <?php if (!empty($statusStats)): ?>
            var statusCtx = document.getElementById('statusBreakdownChart').getContext('2d');
            var statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($statusStats, 'label')) ?>,
                    datasets: [{
                        data: <?= json_encode(array_column($statusStats, 'value')) ?>,
                        backgroundColor: ['#ffc107', '#17a2b8', '#dc3545', '#6c757d', '#28a745', '#343a40'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { color: '#9ca3af', boxWidth: 12 } }
                    }
                }
            });
        <?php endif; ?>
    });
</script>
