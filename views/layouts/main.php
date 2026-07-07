<?php
$currentUser = auth();
$roleName = $currentUser['role_name'] ?? '';
$notifService = new \App\Services\NotificationService();
$unreadCount = $currentUser ? $notifService->countUnread((int)$currentUser['id']) : 0;
$unreadNotifs = $currentUser ? $notifService->getUserNotifications((int)$currentUser['id'], 1, 5) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LabReserve - Computer Booking System</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!-- FullCalendar.js CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 60px;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --card-border: rgba(0, 0, 0, 0.06);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --accent-color: #6366f1;
            --accent-hover: #4f46e5;
            --sidebar-bg: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            margin: 0;
            overflow-x: hidden;
        }

        /* Layout Structure */
        #wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            border-right: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        #sidebar.collapsed {
            margin-left: calc(-1 * var(--sidebar-width));
        }

        .sidebar-brand {
            height: var(--navbar-height);
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            text-decoration: none;
        }

        .sidebar-brand i {
            color: var(--accent-color);
            margin-right: 10px;
        }

        .sidebar-user {
            padding: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
        }

        .sidebar-user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 12px;
            border: 2px solid var(--accent-color);
        }

        .sidebar-user .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #0f172a;
        }

        .sidebar-user .user-role {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .sidebar-menu {
            list-style: none;
            padding: 15px 10px;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-item {
            margin-bottom: 5px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.05rem;
        }

        .sidebar-link:hover, .sidebar-item.active .sidebar-link {
            background-color: rgba(99, 102, 241, 0.06);
            color: var(--accent-color);
        }

        .sidebar-item.active .sidebar-link i {
            color: var(--accent-color);
        }

        /* Top Navbar */
        #content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .top-navbar {
            height: var(--navbar-height);
            background-color: #ffffff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
        }

        .toggle-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: 1.25rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .toggle-btn:hover {
            color: #000;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-dropdown-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            position: relative;
            cursor: pointer;
        }

        .nav-dropdown-btn:hover {
            color: #000;
        }

        .nav-dropdown-btn .badge {
            position: absolute;
            top: -5px;
            right: -8px;
            font-size: 0.65rem;
            padding: 3px 6px;
        }

        /* Main Content Container */
        .main-content {
            padding: 30px;
            flex-grow: 1;
            overflow-y: auto;
        }

        /* Custom Card Styling */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            color: var(--text-primary);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            padding: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-body {
            padding: 25px;
        }

        /* Modal styling overrides for light mode */
        .modal-content {
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: var(--text-primary);
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .modal-header .btn-close {
            filter: none !important;
        }

        .modal-footer {
            border-top: 1px solid rgba(0, 0, 0, 0.06);
        }

        .form-control, .form-select {
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.12);
            color: #0f172a;
            border-radius: 10px;
            padding: 10px 15px;
        }

        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            color: #0f172a;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .table {
            color: var(--text-primary);
            border-color: rgba(0, 0, 0, 0.06);
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            border-bottom: 2px solid rgba(0, 0, 0, 0.08);
        }

        .table td {
            vertical-align: middle;
            font-size: 0.9rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Force dark tables/dropdowns in views to render as light mode */
        .table-dark {
            --bs-table-bg: #ffffff !important;
            --bs-table-color: #0f172a !important;
            --bs-table-border-color: rgba(0, 0, 0, 0.05) !important;
            --bs-table-hover-bg: rgba(0, 0, 0, 0.02) !important;
        }

        .dropdown-menu-dark {
            --bs-dropdown-bg: #ffffff !important;
            --bs-dropdown-color: #0f172a !important;
            --bs-dropdown-link-color: #475569 !important;
            --bs-dropdown-link-hover-bg: rgba(99, 102, 241, 0.06) !important;
            --bs-dropdown-link-hover-color: var(--accent-color) !important;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .dropdown-header {
            color: #94a3b8 !important;
            border-bottom-color: rgba(0, 0, 0, 0.05) !important;
        }

        .dropdown-divider {
            border-color: rgba(0, 0, 0, 0.06) !important;
        }

        .bg-dark.bg-opacity-20 {
            background-color: #f1f5f9 !important;
            border-color: rgba(0, 0, 0, 0.05) !important;
        }

        .bg-dark {
            background-color: #f8fafc !important;
        }

        .pc-card {
            background-color: #ffffff !important;
            border-color: rgba(0, 0, 0, 0.08) !important;
        }

        .pc-card label {
            color: #0f172a !important;
        }

        .activity-timeline .text-white {
            color: #0f172a !important;
        }

        .swal2-popup {
            background-color: #ffffff !important;
            color: #0f172a !important;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1) !important;
        }

        /* Custom Badges */
        .badge-status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 30px;
        }

        /* Loader Overlay animation */
        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #0b0f19;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.5s ease;
        }

        .spinner-glow {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(99, 102, 241, 0.1);
            border-radius: 50%;
            border-top-color: var(--accent-color);
            animation: spin 1s infinite linear;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.5);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Mobile Responsiveness styling rules */
        @media (max-width: 991.98px) {
            #sidebar {
                position: fixed;
                height: 100vh;
                margin-left: calc(-1 * var(--sidebar-width));
                box-shadow: 15px 0 45px rgba(0, 0, 0, 0.08);
            }
            #sidebar.show-mobile {
                margin-left: 0 !important;
            }
            #sidebar.collapsed {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #content-wrapper {
                width: 100%;
                min-width: 0;
            }
            .main-content {
                padding: 15px;
            }
        }
        
        @media (max-width: 575.98px) {
            .top-navbar {
                padding: 0 15px;
            }
            .navbar-actions {
                gap: 10px;
            }
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .card-header .d-flex, .card-header .gap-2 {
                width: 100%;
                justify-content: space-between;
            }
            .card-body {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Loader Overlay -->
    <div id="loader">
        <div class="spinner-glow"></div>
    </div>

    <div id="wrapper">
        <!-- Sidebar Navigation -->
        <aside id="sidebar">
            <a href="/dashboard" class="sidebar-brand">
                <i class="fa-solid fa-desktop"></i>
                <span>LabReserve</span>
            </a>
            
            <div class="sidebar-user">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?>&background=6366f1&color=fff&bold=true" alt="Avatar">
                <div>
                    <div class="user-name"><?= esc($currentUser['first_name'] . ' ' . $currentUser['last_name']) ?></div>
                    <div class="user-role"><?= esc($roleName) ?></div>
                </div>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-item" id="menu-dashboard">
                    <a href="/dashboard" class="sidebar-link">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="sidebar-item" id="menu-reservations">
                    <a href="/reservations" class="sidebar-link">
                        <i class="fa-regular fa-calendar-check"></i>
                        <span>Reservations</span>
                    </a>
                </li>

                <?php if ($roleName === 'Super Administrator' || $roleName === 'Department Administrator' || $roleName === 'Staff'): ?>
                    <li class="sidebar-item" id="menu-computers">
                        <a href="/computers" class="sidebar-link">
                            <i class="fa-solid fa-display"></i>
                            <span>Workstations</span>
                        </a>
                    </li>
                    
                    <li class="sidebar-item" id="menu-laboratories">
                        <a href="/laboratories" class="sidebar-link">
                            <i class="fa-solid fa-door-open"></i>
                            <span>Laboratories</span>
                        </a>
                    </li>

                    <li class="sidebar-item" id="menu-users">
                        <a href="/users" class="sidebar-link">
                            <i class="fa-solid fa-users"></i>
                            <span>User Accounts</span>
                        </a>
                    </li>

                    <li class="sidebar-item" id="menu-reports">
                        <a href="/reports" class="sidebar-link">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Usage Reports</span>
                        </a>
                    </li>

                    <li class="sidebar-item" id="menu-settings">
                        <a href="/settings" class="sidebar-link">
                            <i class="fa-solid fa-gears"></i>
                            <span>System Settings</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </aside>

        <!-- Page Content Wrapper -->
        <div id="content-wrapper">
            <!-- Top Navigation Bar -->
            <header class="top-navbar">
                <button class="toggle-btn" id="sidebar-toggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                
                <div class="navbar-actions">
                    <!-- Notifications Dropdown -->
                    <div class="dropdown">
                        <button class="nav-dropdown-btn dropdown-toggle no-caret" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-bell"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="badge rounded-pill bg-danger"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-dark p-2" style="width: 290px; border-radius: 12px;">
                            <h6 class="dropdown-header border-bottom border-secondary pb-2 mb-2 text-white">Notifications</h6>
                            <?php if (empty($unreadNotifs)): ?>
                                <div class="text-center text-muted py-3" style="font-size: 0.85rem;">No new notifications.</div>
                            <?php else: ?>
                                <?php foreach ($unreadNotifs as $notif): ?>
                                    <div class="dropdown-item py-2 border-bottom border-secondary-subtle" style="white-space: normal; font-size: 0.8rem;">
                                        <div class="fw-bold text-white"><?= esc($notif['title']) ?></div>
                                        <div class="text-secondary"><?= esc($notif['message']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <a href="/reservations" class="dropdown-item text-center text-primary fw-semibold pt-2 pb-0" style="font-size: 0.8rem;">View Reservations History</a>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="nav-dropdown-btn dropdown-toggle no-caret" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-user"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" style="border-radius: 12px;">
                            <li><a class="dropdown-item" href="/settings"><i class="fa-solid fa-key me-2 text-muted"></i> Security Profile</a></li>
                            <li><hr class="dropdown-divider border-secondary"></li>
                            <li>
                                <form action="/logout" method="POST">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-power-off me-2"></i> Log Out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Render Area -->
            <main class="main-content">
                <!-- Render inner page content -->
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Core Javascript Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Helper Scripts -->
    <script>
        $(window_load = function() {
            // Remove Loader Overlay
            $('#loader').css('opacity', 0);
            setTimeout(function() {
                $('#loader').hide();
            }, 500);

            // Toggle Sidebar (Responsive)
            $('#sidebar-toggle').on('click', function() {
                if ($(window).width() < 992) {
                    $('#sidebar').removeClass('collapsed');
                    $('#sidebar').toggleClass('show-mobile');
                } else {
                    $('#sidebar').removeClass('show-mobile');
                    $('#sidebar').toggleClass('collapsed');
                }
            });

            // Auto Active Sidebar Link based on URL path
            var path = window.location.pathname.substring(1);
            if (path === '' || path.startsWith('dashboard')) {
                $('#menu-dashboard').addClass('active');
            } else if (path.startsWith('reservations')) {
                $('#menu-reservations').addClass('active');
            } else if (path.startsWith('computers')) {
                $('#menu-computers').addClass('active');
            } else if (path.startsWith('laboratories')) {
                $('#menu-laboratories').addClass('active');
            } else if (path.startsWith('users')) {
                $('#menu-users').addClass('active');
            } else if (path.startsWith('reports')) {
                $('#menu-reports').addClass('active');
            } else if (path.startsWith('settings')) {
                $('#menu-settings').addClass('active');
            }

            // Display Toast alert if success flash is set
            <?php if (session()->getFlash('success')): ?>
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: <?= json_encode(session()->getFlash('success')) ?>,
                    showConfirmButton: false,
                    timer: 3500,
                    background: '#111827',
                    color: '#fff',
                    timerProgressBar: true
                });
            <?php endif; ?>

            <?php if (session()->getFlash('error')): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: <?= json_encode(session()->getFlash('error')) ?>,
                    confirmButtonColor: '#6366f1',
                    background: '#111827',
                    color: '#fff'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>
