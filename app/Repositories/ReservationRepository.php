<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;
use Exception;

class ReservationRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Find reservation by ID with all joined tables (user, lab, computer, status).
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT r.*, 
                   u.first_name, u.last_name, u.email, u.student_id, u.employee_id,
                   l.name as laboratory_name, l.code as laboratory_code,
                   rs.name as status_name,
                   cd.computer_id, cd.check_in_time, cd.check_out_time,
                   c.code as computer_code, c.name as computer_name, c.asset_number as computer_asset_number,
                   approver.first_name as approver_first_name, approver.last_name as approver_last_name
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            JOIN laboratories l ON r.laboratory_id = l.id
            JOIN reservation_status rs ON r.status_id = rs.id
            LEFT JOIN reservation_details cd ON r.id = cd.reservation_id
            LEFT JOIN computers c ON cd.computer_id = c.id
            LEFT JOIN users approver ON r.approved_by = approver.id
            WHERE r.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    /**
     * Create a reservation. Uses Transaction and strict row locking to prevent race conditions.
     */
    public function create(array $data, array $computerIds): int {
        $this->db->beginTransaction();

        try {
            // 1. Lock the selected computers for update to prevent concurrent reservation creation on them
            $placeholders = implode(',', array_fill(0, count($computerIds), '?'));
            $stmtLock = $this->db->prepare("SELECT id FROM computers WHERE id IN ($placeholders) FOR UPDATE");
            $stmtLock->execute($computerIds);

            // 2. Check for overlapping bookings one last time inside the transaction (double check)
            $stmtOverlap = $this->db->prepare("
                SELECT COUNT(*) 
                FROM reservation_details cd
                JOIN reservations r ON cd.reservation_id = r.id
                WHERE cd.computer_id IN ($placeholders)
                  AND r.status_id IN (1, 2) -- Pending, Approved
                  AND r.start_time < ? 
                  AND r.end_time > ?
            ");
            
            $overlapParams = array_merge($computerIds, [$data['end_time'], $data['start_time']]);
            $stmtOverlap->execute($overlapParams);
            $overlapCount = (int)$stmtOverlap->fetchColumn();

            if ($overlapCount > 0) {
                throw new Exception("One or more selected computers are already reserved during this time period.");
            }

            // 3. Insert reservation header
            $stmtHeader = $this->db->prepare("
                INSERT INTO reservations (user_id, laboratory_id, purpose, start_time, end_time, status_id, created_at)
                VALUES (:user_id, :laboratory_id, :purpose, :start_time, :end_time, :status_id, NOW())
            ");
            $stmtHeader->execute([
                'user_id'       => (int)$data['user_id'],
                'laboratory_id' => (int)$data['laboratory_id'],
                'purpose'       => $data['purpose'],
                'start_time'    => $data['start_time'],
                'end_time'      => $data['end_time'],
                'status_id'     => (int)$data['status_id']
            ]);

            $reservationId = (int)$this->db->lastInsertId();

            // 4. Insert reservation details (link computers)
            $stmtDetail = $this->db->prepare("
                INSERT INTO reservation_details (reservation_id, computer_id)
                VALUES (:reservation_id, :computer_id)
            ");

            foreach ($computerIds as $compId) {
                $stmtDetail->execute([
                    'reservation_id' => $reservationId,
                    'computer_id'    => (int)$compId
                ]);
            }

            $this->db->commit();
            return $reservationId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Update reservation status, approvals, and remarks.
     */
    public function updateStatus(int $id, int $statusId, ?string $remarks = null, ?int $approvedBy = null): bool {
        $sql = "UPDATE reservations SET status_id = :status_id, remarks = :remarks";
        $params = [
            'id'        => $id,
            'status_id' => $statusId,
            'remarks'   => $remarks
        ];

        if ($approvedBy !== null) {
            $sql .= ", approved_by = :approved_by, approved_at = NOW()";
            $params['approved_by'] = $approvedBy;
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Record check-in timestamp.
     */
    public function recordCheckIn(int $reservationId, int $computerId): bool {
        $stmt = $this->db->prepare("
            UPDATE reservation_details 
            SET check_in_time = NOW() 
            WHERE reservation_id = :reservation_id AND computer_id = :computer_id
        ");
        return $stmt->execute([
            'reservation_id' => $reservationId,
            'computer_id'    => $computerId
        ]);
    }

    /**
     * Record check-out timestamp.
     */
    public function recordCheckOut(int $reservationId, int $computerId): bool {
        $stmt = $this->db->prepare("
            UPDATE reservation_details 
            SET check_out_time = NOW() 
            WHERE reservation_id = :reservation_id AND computer_id = :computer_id
        ");
        return $stmt->execute([
            'reservation_id' => $reservationId,
            'computer_id'    => $computerId
        ]);
    }

    /**
     * Get reservations with filter rules and paging.
     */
    public function getReservations(array $filters = [], int $limit = 10, int $offset = 0): array {
        $sql = "
            SELECT r.*, 
                   u.first_name, u.last_name, u.email, u.student_id, u.employee_id,
                   l.name as laboratory_name, l.code as laboratory_code,
                   rs.name as status_name,
                   c.code as computer_code, c.name as computer_name
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            JOIN laboratories l ON r.laboratory_id = l.id
            JOIN reservation_status rs ON r.status_id = rs.id
            LEFT JOIN reservation_details cd ON r.id = cd.reservation_id
            LEFT JOIN computers c ON cd.computer_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND r.user_id = :user_id";
            $params['user_id'] = (int)$filters['user_id'];
        }

        if (!empty($filters['laboratory_id'])) {
            $sql .= " AND r.laboratory_id = :laboratory_id";
            $params['laboratory_id'] = (int)$filters['laboratory_id'];
        }

        if (!empty($filters['status_id'])) {
            $sql .= " AND r.status_id = :status_id";
            $params['status_id'] = (int)$filters['status_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR c.code LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['date'])) {
            $sql .= " AND DATE(r.start_time) = :date";
            $params['date'] = $filters['date'];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND DATE(r.start_time) BETWEEN :start_date AND :end_date";
            $params['start_date'] = $filters['start_date'];
            $params['end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Count reservations matching filters.
     */
    public function countReservations(array $filters = []): int {
        $sql = "
            SELECT COUNT(*) 
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            LEFT JOIN reservation_details cd ON r.id = cd.reservation_id
            LEFT JOIN computers c ON cd.computer_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= " AND r.user_id = :user_id";
            $params['user_id'] = (int)$filters['user_id'];
        }

        if (!empty($filters['laboratory_id'])) {
            $sql .= " AND r.laboratory_id = :laboratory_id";
            $params['laboratory_id'] = (int)$filters['laboratory_id'];
        }

        if (!empty($filters['status_id'])) {
            $sql .= " AND r.status_id = :status_id";
            $params['status_id'] = (int)$filters['status_id'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search OR c.code LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['date'])) {
            $sql .= " AND DATE(r.start_time) = :date";
            $params['date'] = $filters['date'];
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND DATE(r.start_time) BETWEEN :start_date AND :end_date";
            $params['start_date'] = $filters['start_date'];
            $params['end_date'] = $filters['end_date'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Find reservations that have passed their check-in time window and remain "Approved" (2) without check-in.
     */
    public function findExpiredReservations(int $expiryMinutes): array {
        $stmt = $this->db->prepare("
            SELECT r.id 
            FROM reservations r
            LEFT JOIN reservation_details cd ON r.id = cd.reservation_id
            WHERE r.status_id = 2 -- Approved
              AND DATE_ADD(r.start_time, INTERVAL :expiry MINUTE) < NOW()
              AND cd.check_in_time IS NULL
        ");
        $stmt->bindValue(':expiry', $expiryMinutes, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Find all reservations for Calendar (daily, weekly, monthly schedule).
     */
    public function getReservationsForCalendar(string $start, string $end): array {
        $stmt = $this->db->prepare("
            SELECT r.id, r.purpose as title, r.start_time as start, r.end_time as end,
                   u.first_name, u.last_name, l.name as lab_name, rs.name as status_name, r.status_id
            FROM reservations r
            JOIN users u ON r.user_id = u.id
            JOIN laboratories l ON r.laboratory_id = l.id
            JOIN reservation_status rs ON r.status_id = rs.id
            WHERE r.start_time >= :start AND r.end_time <= :end
              AND r.status_id NOT IN (4, 3) -- Exclude Cancelled, Rejected for calendar clarity (or display all)
        ");
        $stmt->execute(['start' => $start, 'end' => $end]);
        return $stmt->fetchAll();
    }

    /**
     * Check if a user has exceeded active bookings limit.
     */
    public function countActiveReservationsByUser(int $userId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM reservations 
            WHERE user_id = :user_id 
              AND status_id IN (1, 2) -- Pending, Approved
        ");
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }
}
