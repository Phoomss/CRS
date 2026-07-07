<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class NotificationRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Create a notification.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
            VALUES (:user_id, :title, :message, :type, 0, NOW())
        ");
        $stmt->execute([
            'user_id' => (int)$data['user_id'],
            'title'   => $data['title'],
            'message' => $data['message'],
            'type'    => $data['type'] ?? 'dashboard'
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(int $id): bool {
        $stmt = $this->db->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE id = :id
        ");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Mark all user notifications as read.
     */
    public function markAllAsRead(int $userId): bool {
        $stmt = $this->db->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = :user_id AND is_read = 0
        ");
        return $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Get user's notifications.
     */
    public function getNotifications(int $userId, int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count unread notifications.
     */
    public function countUnread(int $userId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications 
            WHERE user_id = :user_id AND is_read = 0
        ");
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }
}
