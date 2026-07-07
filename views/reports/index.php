<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-file-invoice-dollar text-indigo me-2"></i>Usage & Reservations Analytics Reports</h5>
            </div>
            
            <div class="card-body py-4">
                <p class="text-secondary mb-4">Select a report module below to view detailed analytics logs, print physical report sheets, or export to CSV Excel tables.</p>
                
                <div class="row g-4">
                    <!-- Report Card 1: Computers -->
                    <div class="col-md-4">
                        <div class="card p-3 h-100 border-0" style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.03);">
                            <div class="text-indigo mb-3"><i class="fa-solid fa-display fa-2x"></i></div>
                            <h5 class="fw-bold text-white mb-2">Computer Workstation Usage</h5>
                            <p class="text-secondary small mb-4">Tracks total bookings count, completed/expired count, and cumulative active duration (minutes) for individual workstations.</p>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="/reports/view/computers" class="btn btn-sm btn-indigo flex-grow-1"><i class="fa-solid fa-eye me-1"></i> View</a>
                                <a href="/reports/export/computers" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-download"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Report Card 2: Laboratories -->
                    <div class="col-md-4">
                        <div class="card p-3 h-100 border-0" style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.03);">
                            <div class="text-indigo mb-3"><i class="fa-solid fa-door-open fa-2x"></i></div>
                            <h5 class="fw-bold text-white mb-2">Laboratory Capacity Density</h5>
                            <p class="text-secondary small mb-4">Analyzes reservation counts per lab room, detailing approved, completed, rejected, and cancelled metrics.</p>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="/reports/view/laboratories" class="btn btn-sm btn-indigo flex-grow-1"><i class="fa-solid fa-eye me-1"></i> View</a>
                                <a href="/reports/export/laboratories" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-download"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Report Card 3: Users -->
                    <div class="col-md-4">
                        <div class="card p-3 h-100 border-0" style="background: rgba(15, 23, 42, 0.4); border: 1px solid rgba(255,255,255,0.03);">
                            <div class="text-indigo mb-3"><i class="fa-solid fa-users-viewfinder fa-2x"></i></div>
                            <h5 class="fw-bold text-white mb-2">User Reservation Logs</h5>
                            <p class="text-secondary small mb-4">Displays aggregate booking counts grouped by users (Students, Lecturers), tracking completion vs expiration performance.</p>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="/reports/view/users" class="btn btn-sm btn-indigo flex-grow-1"><i class="fa-solid fa-eye me-1"></i> View</a>
                                <a href="/reports/export/users" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-download"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
