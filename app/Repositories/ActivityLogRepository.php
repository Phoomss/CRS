<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ActivityLogRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Log a user activity.
     */
    public function log(?int $userId, string $action, string $details, ?string $ip, ?string $userAgent): bool {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent, created_at)
            VALUES (:user_id, :action, :details, :ip_address, :user_agent, NOW())
        ");
        return $stmt->execute([
            'user_id'    => $userId,
            'action'     => $action,
            'details'    => $details,
            'ip_address' => $ip,
            'user_agent' => $userAgent
        ]);
    }

    /**
     * Get paginated logs with user details.
     */
    public function getLogs(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT al.*, u.first_name, u.last_name, u.email, r.name as role_name
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            LEFT JOIN roles r ON u.role_id = r.id
            ORDER BY al.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get count of all logs.
     */
    public function countLogs(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();
    }
}
