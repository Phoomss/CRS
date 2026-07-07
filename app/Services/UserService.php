<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;

class UserService {
    private UserRepository $userRepository;
    private ActivityLogService $logService;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->logService = new ActivityLogService();
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): int {
        $this->validateUserUniqueness($data);

        // Hash the password
        $data['password_hash'] = password_hash($data['password'] ?? 'admin123', PASSWORD_BCRYPT);

        $userId = $this->userRepository->create($data);

        $this->logService->log('create_user', [
            'user_id' => $userId,
            'email' => $data['email'],
            'role_id' => $data['role_id']
        ]);

        return $userId;
    }

    /**
     * Update an existing user.
     */
    public function updateUser(int $id, array $data): bool {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new Exception("User not found.");
        }

        $this->validateUserUniqueness($data, $id);

        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            $data['password_hash'] = '';
        }

        $result = $this->userRepository->update($id, $data);

        if ($result) {
            $this->logService->log('update_user', [
                'target_user_id' => $id,
                'email' => $data['email'],
                'role_id' => $data['role_id']
            ]);
        }

        return $result;
    }

    /**
     * Delete a user.
     */
    public function deleteUser(int $id): bool {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            throw new Exception("User not found.");
        }

        // Check if user is Super Administrator (protect from self-deletion or locking out)
        if ($user['role_name'] === 'Super Administrator') {
            // Count super admins to prevent deleting the last one
            $filters = ['role_id' => 1];
            $superAdminCount = $this->userRepository->countUsers($filters);
            if ($superAdminCount <= 1) {
                throw new Exception("Cannot delete the last Super Administrator account.");
            }
        }

        try {
            $result = $this->userRepository->delete($id);
            if ($result) {
                $this->logService->log('delete_user', [
                    'deleted_user_id' => $id,
                    'email' => $user['email']
                ]);
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception("Cannot delete user. This user may have active reservations or activity logs in the system.");
        }
    }

    /**
     * Get user by ID.
     */
    public function getUserById(int $id): ?array {
        return $this->userRepository->findById($id);
    }

    /**
     * Get users.
     */
    public function getUsers(array $filters = [], int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        return $this->userRepository->getUsers($filters, $perPage, $offset);
    }

    /**
     * Count users.
     */
    public function countUsers(array $filters = []): int {
        return $this->userRepository->countUsers($filters);
    }

    /**
     * Get all roles.
     */
    public function getRoles(): array {
        return $this->userRepository->getRoles();
    }

    /**
     * Validate user uniqueness constraints.
     */
    private function validateUserUniqueness(array $data, ?int $excludeId = null): void {
        // 1. Check Email
        $existing = $this->userRepository->findByEmail($data['email']);
        if ($existing && ($excludeId === null || (int)$existing['id'] !== $excludeId)) {
            throw new Exception("The email address '{$data['email']}' is already in use.");
        }

        // 2. Check Student ID
        if (!empty($data['student_id'])) {
            $existing = $this->dbQueryUnique('student_id', $data['student_id'], $excludeId);
            if ($existing) {
                throw new Exception("The Student ID '{$data['student_id']}' is already registered.");
            }
        }

        // 3. Check Employee ID
        if (!empty($data['employee_id'])) {
            $existing = $this->dbQueryUnique('employee_id', $data['employee_id'], $excludeId);
            if ($existing) {
                throw new Exception("The Employee ID '{$data['employee_id']}' is already registered.");
            }
        }
    }

    /**
     * Direct query helper for unique validation.
     */
    private function dbQueryUnique(string $field, string $value, ?int $excludeId = null): bool {
        $db = \App\Core\Database::getConnection();
        $sql = "SELECT COUNT(*) FROM users WHERE `{$field}` = :value";
        $params = ['value' => $value];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }
}
