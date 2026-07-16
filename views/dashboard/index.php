<style>
    .stat-card {
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
    }
    .dashboard-card {
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.04);
        border: 1px solid rgba(99, 102, 241, 0.06);
        background-color: #ffffff;
        overflow: hidden;
    }
    .dashboard-card-header {
        background-color: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dashboard-card-header h5, .dashboard-card-header h6 {
        color: #1e293b;
        font-weight: 700;
        margin: 0;
    }
    .dashboard-card-body {
        padding: 24px;
    }
    .modern-table-container {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .modern-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.8px;
        background-color: #f8fafc !important;
        color: #64748b;
        padding: 14px 18px;
        border-bottom: 2px solid #e2e8f0;
    }
    .modern-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.88rem;
        color: #334155;
    }
    .modern-table tbody tr {
        transition: all 0.2s ease;
    }
    .modern-table tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.015) !important;
    }
    .activity-item {
        border-left: 2px solid #e2e8f0;
        padding-left: 20px;
        position: relative;
    }
    .activity-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 6px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #6366f1;
        border: 2px solid #ffffff;
    }
</style>

<div class="row g-4 mb-4">
    <!-- Stat 1: Available computers -->
    <div class="col-md-3">
        <div class="card stat-card p-3 border-0 h-100" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid rgba(34, 197, 94, 0.2) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-success small fw-semibold text-uppercase" style="color: #166534 !important; font-size: 0.75rem; letter-spacing: 0.5px;">เครื่องว่างพร้อมใช้งาน</span>
                    <h3 class="mt-1 mb-0 fw-bold" style="color: #14532d !important;"><?= $availableCompCount ?> <span class="small" style="color: #166534; opacity: 0.7; font-size: 0.9rem;">/ <?= $computersCount ?></span></h3>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(34, 197, 94, 0.12); color: #15803d;">
                    <i class="fa-solid fa-circle-check fa-xl"></i>
                </div>
            </div>
            <div class="mt-3 small" style="color: #166534; opacity: 0.85; font-size: 0.82rem;">เปิดใช้งานและว่างสำหรับการจองเครื่อง</div>
        </div>
    </div>

    <!-- Stat 2: Active / Approved Bookings -->
    <div class="col-md-3">
        <div class="card stat-card p-3 border-0 h-100" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1px solid rgba(99, 102, 241, 0.2) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-indigo small fw-semibold text-uppercase" style="color: #3730a3 !important; font-size: 0.75rem; letter-spacing: 0.5px;">การจองที่อนุมัติแล้ว</span>
                    <h3 class="mt-1 mb-0 fw-bold" style="color: #1e1b4b !important;"><?= $approvedResCount ?></h3>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(99, 102, 241, 0.12); color: #4f46e5;">
                    <i class="fa-regular fa-calendar-check fa-xl"></i>
                </div>
            </div>
            <div class="mt-3 small" style="color: #3730a3; opacity: 0.85; font-size: 0.82rem;">ยืนยันคำขอจองแล้ว รอเข้าใช้เช็คอิน</div>
        </div>
    </div>

    <!-- Stat 3: Pending Approvals -->
    <div class="col-md-3">
        <div class="card stat-card p-3 border-0 h-100" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid rgba(245, 158, 11, 0.2) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-warning small fw-semibold text-uppercase" style="color: #b45309 !important; font-size: 0.75rem; letter-spacing: 0.5px;">รอการตรวจสอบ</span>
                    <h3 class="mt-1 mb-0 fw-bold" style="color: #78350f !important;"><?= $pendingResCount ?></h3>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                    <i class="fa-solid fa-hourglass-half fa-xl"></i>
                </div>
            </div>
            <div class="mt-3 small" style="color: #b45309; opacity: 0.85; font-size: 0.82rem;">คำขอจองใหม่ที่ต้องรอการอนุมัติ</div>
        </div>
    </div>

    <!-- Stat 4: Workstations under maintenance -->
    <div class="col-md-3">
        <div class="card stat-card p-3 border-0 h-100" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid rgba(239, 68, 68, 0.2) !important;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-danger small fw-semibold text-uppercase" style="color: #c2410c !important; font-size: 0.75rem; letter-spacing: 0.5px;">อยู่ระหว่างบำรุงรักษา</span>
                    <h3 class="mt-1 mb-0 fw-bold" style="color: #7f1d1d !important;"><?= $maintenanceCompCount ?> <span class="small" style="color: #7f1d1d; opacity: 0.7; font-size: 0.9rem;">/ <?= $offlineCompCount ?> ออฟไลน์</span></h3>
                </div>
                <div class="rounded-circle p-3" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                    <i class="fa-solid fa-screwdriver-wrench fa-xl"></i>
                </div>
            </div>
            <div class="mt-3 small" style="color: #c2410c; opacity: 0.85; font-size: 0.82rem;">ปิดใช้งานคอมพิวเตอร์เพื่อตรวจซ่อมบำรุง</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Full Calendar Schedule Widget -->
    <div class="col-lg-8">
        <div class="card dashboard-card h-100">
            <div class="dashboard-card-header">
                <h5><i class="fa-regular fa-calendar me-2 text-primary"></i>ตารางเวลาการจองเครื่องคอมพิวเตอร์</h5>
                <?php if (has_permission('create_reservations')): ?>
                    <a href="/reservations/create" class="btn btn-sm btn-primary px-3 py-1.5" style="border-radius: 8px; font-weight: 600;"><i class="fa-solid fa-plus me-1"></i> ทำการจองใหม่</a>
                <?php endif; ?>
            </div>
            <div class="dashboard-card-body">
                <div id="calendar" style="min-height: 480px;"></div>
            </div>
        </div>
    </div>

    <!-- Right Column: Charts & Statistics -->
    <div class="col-lg-4 d-flex flex-column gap-4">
        <?php if (!empty($monthlyTrends)): ?>
            <!-- Chart 1: Monthly Trends -->
            <div class="card dashboard-card flex-grow-1">
                <div class="dashboard-card-header">
                    <h6><i class="fa-solid fa-chart-line me-2 text-indigo"></i>แนวโน้มยอดการจองเครื่องสะสม</h6>
                </div>
                <div class="dashboard-card-body py-2">
                    <canvas id="monthlyTrendChart" style="max-height: 180px;"></canvas>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($statusStats)): ?>
            <!-- Chart 2: Status breakdown -->
            <div class="card dashboard-card flex-grow-1">
                <div class="dashboard-card-header">
                    <h6><i class="fa-solid fa-chart-pie me-2 text-indigo"></i>สัดส่วนตามสถานะการจอง</h6>
                </div>
                <div class="dashboard-card-body py-2">
                    <canvas id="statusBreakdownChart" style="max-height: 180px;"></canvas>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($monthlyTrends) && empty($statusStats)): ?>
            <!-- Student Helpful Links Widget -->
            <div class="card dashboard-card flex-grow-1">
                <div class="dashboard-card-header">
                    <h6><i class="fa-solid fa-circle-info me-2 text-primary"></i>คำแนะนำและขั้นตอนการใช้งาน</h6>
                </div>
                <div class="dashboard-card-body text-secondary small">
                    <p class="mb-2 fw-medium text-dark">ยินดีต้อนรับสู่ระบบจองเครื่องคอมพิวเตอร์ห้องปฏิบัติการภาควิชา</p>
                    <ul class="ps-3 mb-3 text-secondary" style="line-height: 1.5;">
                        <li class="mb-1">สามารถส่งคำขอจองเครื่องคอมพิวเตอร์ล่วงหน้าได้สูงสุด 1 สัปดาห์</li>
                        <li class="mb-1">การจองที่ได้รับอนุมัติแล้ว จะต้องเช็คอินเข้าใช้งานเครื่องภายใน 15 นาทีของช่วงเริ่มต้นเวลาจอง</li>
                        <li class="mb-1">โปรดเช็คเอาท์ออกทุกครั้งหลังใช้งานเสร็จสิ้น เพื่อปล่อยสิทธิ์เครื่องว่างให้เพื่อน ๆ คนอื่นใช้งาน</li>
                    </ul>
                    <a href="/reservations/create" class="btn btn-sm btn-outline-indigo w-100 py-2 fw-semibold" style="border-radius: 8px;">ทำการจองเครื่องทันที</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (has_role(['Super Administrator', 'Department Administrator', 'Staff'])): ?>
<div class="row g-4">
    <!-- Popular Workstations -->
    <div class="col-lg-6">
        <div class="card dashboard-card h-100">
            <div class="dashboard-card-header">
                <h5><i class="fa-solid fa-fire text-warning me-2"></i>คอมพิวเตอร์ที่มียอดการจองสูงสุด</h5>
            </div>
            <div class="dashboard-card-body">
                <?php if (empty($mostUsedComputers)): ?>
                    <div class="text-center text-muted py-4">ยังไม่มีการบันทึกประวัติการจองเครื่องในระบบ</div>
                <?php else: ?>
                    <div class="table-responsive modern-table-container">
                        <table class="table modern-table m-0">
                            <thead>
                                <tr>
                                    <th>รหัสเครื่องคอมฯ</th>
                                    <th>ห้องปฏิบัติการ</th>
                                    <th class="text-center">จำนวนครั้งที่จอง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mostUsedComputers as $c): ?>
                                    <tr>
                                        <td><span class="fw-semibold text-dark"><?= esc($c['code']) ?></span></td>
                                        <td><?= esc($c['laboratory_name']) ?></td>
                                        <td class="text-center"><span class="badge bg-indigo-subtle text-indigo px-2.5 py-1.5 fw-semibold" style="font-size: 0.82rem;"><?= $c['reservation_count'] ?> ครั้ง</span></td>
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
        <div class="card dashboard-card h-100">
            <div class="dashboard-card-header">
                <h5><i class="fa-solid fa-list-check me-2 text-primary"></i>บันทึกกิจกรรมล่าสุดในระบบ</h5>
            </div>
            <div class="dashboard-card-body">
                <?php if (empty($recentLogs)): ?>
                    <div class="text-center text-muted py-4">ยังไม่มีบันทึกประวัติกิจกรรมในระบบ</div>
                <?php else: ?>
                    <div class="activity-timeline" style="max-height: 290px; overflow-y: auto; padding-right: 5px;">
                        <?php foreach ($recentLogs as $log): ?>
                            <div class="d-flex mb-3 pb-2 border-bottom border-light" style="font-size: 0.85rem;">
                                <div class="me-3 text-secondary pt-1" style="min-width: 65px; font-size: 0.75rem;">
                                    <?= date('H:i A', strtotime($log['created_at'])) ?>
                                </div>
                                <div class="flex-grow-1 activity-item">
                                    <span class="text-dark fw-semibold"><?= esc($log['first_name'] . ' ' . $log['last_name'] ?? 'ระบบ') ?></span>
                                    <span class="badge bg-secondary-subtle text-secondary ms-1" style="font-size: 0.7rem; font-weight: 600;"><?= esc($log['action']) ?></span>
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
            locale: 'th',
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
                        label: 'การจอง',
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
