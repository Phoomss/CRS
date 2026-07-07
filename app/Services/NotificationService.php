<?php

namespace App\Services;

use App\Repositories\NotificationRepository;
use App\Repositories\UserRepository;

class NotificationService {
    private NotificationRepository $repository;

    public function __construct() {
        $this->repository = new NotificationRepository();
    }

    /**
     * Send a notification to a specific user.
     * Types: 'dashboard', 'email', 'both'
     */
    public function send(int $userId, string $title, string $message, string $type = 'dashboard'): bool {
        // 1. Log in database if type is dashboard or both
        if ($type === 'dashboard' || $type === 'both') {
            $this->repository->create([
                'user_id' => $userId,
                'title'   => $title,
                'message' => $message,
                'type'    => $type
            ]);
        }

        // 2. Send email if type is email or both
        if ($type === 'email' || $type === 'both') {
            $this->sendEmailNotification($userId, $title, $message);
        }

        return true;
    }

    /**
     * Simulates or executes email dispatch via SMTP or local file logs.
     */
    private function sendEmailNotification(int $userId, string $title, string $message): void {
        $user = (new UserRepository())->findById($userId);
        if (!$user) {
            return;
        }

        $toEmail = $user['email'];
        $toName = $user['first_name'] . ' ' . $user['last_name'];

        $settingService = new SettingService();
        $emailEnabled = $settingService->getBool('notification_email_enabled', false);

        $logPath = __DIR__ . '/../../storage/logs/mail.log';
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $emailLog = "==================================================\n";
        $emailLog .= "TIMESTAMP: {$timestamp}\n";
        $emailLog .= "TO: {$toName} <{$toEmail}>\n";
        $emailLog .= "SUBJECT: {$title}\n";
        $emailLog .= "MESSAGE:\n{$message}\n";
        $emailLog .= "==================================================\n\n";

        // Append to local mail simulation log
        file_put_contents($logPath, $emailLog, FILE_APPEND);

        // If email is actually enabled, we can use PHP's mail() function as a fallback:
        if ($emailEnabled) {
            $fromEmail = $settingService->get('smtp_from_email', 'no-reply@lab.edu');
            $fromName = $settingService->get('smtp_from_name', 'Lab Booking System');
            
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=utf-8',
                "From: {$fromName} <{$fromEmail}>",
                "Reply-To: {$fromEmail}"
            ];

            // Send actual mail using mail() - which uses server sendmail
            @mail($toEmail, $title, nl2br(htmlspecialchars($message)), implode("\r\n", $headers));
        }
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(int $id): bool {
        return $this->repository->markAsRead($id);
    }

    /**
     * Mark all user notifications as read.
     */
    public function markAllAsRead(int $userId): bool {
        return $this->repository->markAllAsRead($userId);
    }

    /**
     * Get user's notifications.
     */
    public function getUserNotifications(int $userId, int $page = 1, int $perPage = 50): array {
        $offset = ($page - 1) * $perPage;
        return $this->repository->getNotifications($userId, $perPage, $offset);
    }

    /**
     * Count user unread notifications.
     */
    public function countUnread(int $userId): int {
        return $this->repository->countUnread($userId);
    }
}
