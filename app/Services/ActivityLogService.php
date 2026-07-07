<?php

namespace App\Services;

use App\Repositories\ActivityLogRepository;
use App\Core\Session;

class ActivityLogService {
    private ActivityLogRepository $logRepository;

    public function __construct() {
        $this->logRepository = new ActivityLogRepository();
    }

    /**
     * Log the current action.
     */
    public function log(string $action, string|array $details): void {
        $user = Session::get('user');
        $userId = $user ? (int)$user['id'] : null;

        $detailStr = is_array($details) ? json_encode($details, JSON_UNESCAPED_UNICODE) : $details;
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        $this->logRepository->log($userId, $action, $detailStr, $ip, $userAgent);
    }

    /**
     * Get paginated logs.
     */
    public function getLogs(int $page = 1, int $perPage = 50): array {
        $offset = ($page - 1) * $perPage;
        return $this->logRepository->getLogs($perPage, $offset);
    }

    /**
     * Get count of all logs.
     */
    public function countLogs(): int {
        return $this->logRepository->countLogs();
    }
}
