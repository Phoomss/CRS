<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class ReportService {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get reservation statistics breakdown.
     */
    public function getReservationStatusStats(): array {
        return $this->db->query("
            SELECT rs.name as label, COUNT(r.id) as value
            FROM reservation_status rs
            LEFT JOIN reservations r ON rs.id = r.status_id
            GROUP BY rs.id, rs.name
            ORDER BY rs.id ASC
        ")->fetchAll();
    }

    /**
     * Get monthly reservation count trends (past 6 months).
     */
    public function getMonthlyReservationTrends(): array {
        return $this->db->query("
            SELECT DATE_FORMAT(start_time, '%b %Y') as label, COUNT(*) as value
            FROM reservations
            WHERE start_time >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY YEAR(start_time), MONTH(start_time), DATE_FORMAT(start_time, '%b %Y')
            ORDER BY YEAR(start_time) ASC, MONTH(start_time) ASC
        ")->fetchAll();
    }

    /**
     * Get peak reservation hours (which hours of the day are most booked).
     */
    public function getPeakUsageHours(): array {
        return $this->db->query("
            SELECT HOUR(start_time) as hour, COUNT(*) as count
            FROM reservations
            GROUP BY HOUR(start_time)
            ORDER BY count DESC
            LIMIT 24
        ")->fetchAll();
    }

    /**
     * Get most reserved computers.
     */
    public function getMostReservedComputers(int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT c.code, c.name, l.name as laboratory_name, COUNT(cd.id) as reservation_count
            FROM computers c
            JOIN laboratories l ON c.laboratory_id = l.id
            JOIN reservation_details cd ON c.id = cd.computer_id
            GROUP BY c.id, c.code, c.name, l.name
            ORDER BY reservation_count DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get computer usage report details.
     */
    public function getComputerUsageReport(): array {
        return $this->db->query("
            SELECT c.code, c.name, l.name as laboratory_name, 
                   COUNT(cd.id) as total_bookings,
                   SUM(CASE WHEN r.status_id = 5 THEN 1 ELSE 0 END) as completed_bookings,
                   SUM(CASE WHEN r.status_id = 6 THEN 1 ELSE 0 END) as expired_bookings,
                   SUM(TIMESTAMPDIFF(MINUTE, r.start_time, r.end_time)) as total_minutes
            FROM computers c
            JOIN laboratories l ON c.laboratory_id = l.id
            LEFT JOIN reservation_details cd ON c.id = cd.computer_id
            LEFT JOIN reservations r ON cd.reservation_id = r.id
            GROUP BY c.id, c.code, c.name, l.name
            ORDER BY total_bookings DESC
        ")->fetchAll();
    }

    /**
     * Get laboratory usage report details.
     */
    public function getLaboratoryUsageReport(): array {
        return $this->db->query("
            SELECT l.code, l.name, l.capacity,
                   COUNT(r.id) as total_reservations,
                   SUM(CASE WHEN r.status_id = 5 THEN 1 ELSE 0 END) as completed_reservations,
                   SUM(CASE WHEN r.status_id = 3 THEN 1 ELSE 0 END) as rejected_reservations,
                   SUM(CASE WHEN r.status_id = 4 THEN 1 ELSE 0 END) as cancelled_reservations
            FROM laboratories l
            LEFT JOIN reservations r ON l.id = r.laboratory_id
            GROUP BY l.id, l.code, l.name, l.capacity
            ORDER BY total_reservations DESC
        ")->fetchAll();
    }

    /**
     * Get user-specific reservation counts.
     */
    public function getUserReservationReport(): array {
        return $this->db->query("
            SELECT u.student_id, u.employee_id, u.first_name, u.last_name, u.email, r.name as role_name,
                   COUNT(res.id) as total_bookings,
                   SUM(CASE WHEN res.status_id = 5 THEN 1 ELSE 0 END) as completed_bookings,
                   SUM(CASE WHEN res.status_id = 6 THEN 1 ELSE 0 END) as expired_bookings
            FROM users u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN reservations res ON u.id = res.user_id
            GROUP BY u.id, u.student_id, u.employee_id, u.first_name, u.last_name, u.email, r.name
            ORDER BY total_bookings DESC
        ")->fetchAll();
    }
}
