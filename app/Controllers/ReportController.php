<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\ReportService;

class ReportController extends Controller {
    private ReportService $reportService;

    public function __construct() {
        $this->reportService = new ReportService();
    }

    /**
     * Show Report Landing panel.
     */
    public function index(): void {
        $this->render('reports.index');
    }

    /**
     * View specific report.
     */
    public function view(Request $request, string $type): void {
        $data = $this->getReportData($type);
        
        $this->render("reports.view_{$type}", [
            'data' => $data,
            'type' => $type
        ]);
    }

    /**
     * Export report to CSV/Excel.
     */
    public function export(Request $request, string $type): void {
        $data = $this->getReportData($type);
        $filename = "report_{$type}_" . date('Ymd_His') . ".csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for proper Excel encoding
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if (empty($data)) {
            fputcsv($output, ['No records found.']);
            fclose($output);
            exit;
        }

        // Output Headers based on type
        if ($type === 'computers') {
            fputcsv($output, ['Computer Code', 'Workstation Name', 'Laboratory Name', 'Total Bookings', 'Completed Bookings', 'Expired Bookings', 'Total Usage (Minutes)']);
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['code'],
                    $row['name'],
                    $row['laboratory_name'],
                    $row['total_bookings'],
                    $row['completed_bookings'],
                    $row['expired_bookings'],
                    $row['total_minutes'] ?? 0
                ]);
            }
        } elseif ($type === 'laboratories') {
            fputcsv($output, ['Laboratory Code', 'Laboratory Name', 'Capacity', 'Total Reservations', 'Completed Reservations', 'Rejected Reservations', 'Cancelled Reservations']);
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['code'],
                    $row['name'],
                    $row['capacity'],
                    $row['total_reservations'],
                    $row['completed_reservations'],
                    $row['rejected_reservations'],
                    $row['cancelled_reservations']
                ]);
            }
        } elseif ($type === 'users') {
            fputcsv($output, ['Student ID', 'Employee ID', 'Full Name', 'Email', 'Role', 'Total Bookings', 'Completed Bookings', 'Expired Bookings']);
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['student_id'] ?? '-',
                    $row['employee_id'] ?? '-',
                    $row['first_name'] . ' ' . $row['last_name'],
                    $row['email'],
                    $row['role_name'],
                    $row['total_bookings'],
                    $row['completed_bookings'],
                    $row['expired_bookings']
                ]);
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Helper to gather raw report array values.
     */
    private function getReportData(string $type): array {
        switch ($type) {
            case 'computers':
                return $this->reportService->getComputerUsageReport();
            case 'laboratories':
                return $this->reportService->getLaboratoryUsageReport();
            case 'users':
                return $this->reportService->getUserReservationReport();
            default:
                return [];
        }
    }
}
