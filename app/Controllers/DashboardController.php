<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\ComputerService;
use App\Services\ReservationService;
use App\Services\ActivityLogService;
use App\Services\ReportService;
use App\Core\Session;

class DashboardController extends Controller {
    private ComputerService $computerService;
    private ReservationService $reservationService;
    private ActivityLogService $logService;
    private ReportService $reportService;

    public function __construct() {
        $this->computerService = new ComputerService();
        $this->reservationService = new ReservationService();
        $this->logService = new ActivityLogService();
        $this->reportService = new ReportService();
    }

    /**
     * Display Dashboard page.
     */
    public function index(): void {
        $user = Session::get('user');

        // Trigger reservation expiry checks automatically on dashboard load
        $this->reservationService->releaseExpiredReservations();

        // Gather metrics
        $computersCount = $this->computerService->countComputers();
        $availableCompCount = $this->computerService->countComputers(['status' => 'available']);
        $maintenanceCompCount = $this->computerService->countComputers(['status' => 'maintenance']);
        $offlineCompCount = $this->computerService->countComputers(['status' => 'offline']);

        // Reservations counts
        $pendingResCount = $this->reservationService->countReservations(['status_id' => 1]); // Pending
        $approvedResCount = $this->reservationService->countReservations(['status_id' => 2]); // Approved

        $recentLogs = [];
        $mostUsedComputers = [];
        $monthlyTrends = [];
        $statusStats = [];

        // Admin-only stats
        $role = $user['role_name'] ?? '';
        if ($role === 'Super Administrator' || $role === 'Department Administrator' || $role === 'Staff') {
            $recentLogs = $this->logService->getLogs(1, 8);
            $mostUsedComputers = $this->reportService->getMostReservedComputers(5);
            $monthlyTrends = $this->reportService->getMonthlyReservationTrends();
            $statusStats = $this->reportService->getReservationStatusStats();
        } else {
            // For students and lecturers, load their own bookings count
            $pendingResCount = $this->reservationService->countReservations(['user_id' => $user['id'], 'status_id' => 1]);
            $approvedResCount = $this->reservationService->countReservations(['user_id' => $user['id'], 'status_id' => 2]);
        }

        $this->render('dashboard.index', [
            'computersCount'       => $computersCount,
            'availableCompCount'   => $availableCompCount,
            'maintenanceCompCount' => $maintenanceCompCount,
            'offlineCompCount'     => $offlineCompCount,
            'pendingResCount'      => $pendingResCount,
            'approvedResCount'     => $approvedResCount,
            'recentLogs'           => $recentLogs,
            'mostUsedComputers'    => $mostUsedComputers,
            'monthlyTrends'        => $monthlyTrends,
            'statusStats'          => $statusStats
        ]);
    }
}
