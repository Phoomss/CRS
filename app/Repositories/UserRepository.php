<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UserRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Find a user by their ID.
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find a user by their email address.
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.email = :email
        ");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find a user by remember-me token.
     */
    public function findByRememberToken(string $token): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.remember_token = :token AND u.status = 'active'
        ");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Find a user by reset password token.
     */
    public function findByResetToken(string $token): ?array {
        $stmt = $this->db->prepare("
            SELECT u.*, r.name as role_name 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.reset_token = :token 
              AND u.reset_token_expires_at > NOW()
        ");
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Update user's remember-me token.
     */
    public function updateRememberToken(int $id, ?string $token): bool {
        $stmt = $this->db->prepare("UPDATE users SET remember_token = :token WHERE id = :id");
        return $stmt->execute(['token' => $token, 'id' => $id]);
    }

    /**
     * Update user's password reset token.
     */
    public function updateResetToken(string $email, ?string $token, ?string $expiresAt): bool {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET reset_token = :token, reset_token_expires_at = :expires_at 
            WHERE email = :email
        ");
        return $stmt->execute([
            'token' => $token,
            'expires_at' => $expiresAt,
            'email' => $email
        ]);
    }

    /**
     * Update user's password.
     */
    public function updatePassword(int $id, string $hash): bool {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password_hash = :hash, reset_token = NULL, reset_token_expires_at = NULL 
            WHERE id = :id
        ");
        return $stmt->execute(['hash' => $hash, 'id' => $id]);
    }

    /**
     * Fetch user permissions (for RBAC authorization checking).
     */
    public function getPermissionsByUserId(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT p.name 
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            JOIN users u ON rp.role_id = u.role_id
            WHERE u.id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO users (
                student_id, employee_id, first_name, last_name, 
                email, phone_number, password_hash, role_id, status, created_at
            ) VALUES (
                :student_id, :employee_id, :first_name, :last_name, 
                :email, :phone_number, :password_hash, :role_id, :status, NOW()
            )
        ");
        
        $stmt->execute([
            'student_id'    => $data['student_id'] ?: null,
            'employee_id'   => $data['employee_id'] ?: null,
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'phone_number'  => $data['phone_number'] ?: null,
            'password_hash' => $data['password_hash'],
            'role_id'       => (int)$data['role_id'],
            'status'        => $data['status'] ?? 'active'
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update an existing user.
     */
    public function update(int $id, array $data): bool {
        $fields = [
            'student_id = :student_id',
            'employee_id = :employee_id',
            'first_name = :first_name',
            'last_name = :last_name',
            'email = :email',
            'phone_number = :phone_number',
            'role_id = :role_id',
            'status = :status'
        ];

        $params = [
            'id'            => $id,
            'student_id'    => $data['student_id'] ?: null,
            'employee_id'   => $data['employee_id'] ?: null,
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'phone_number'  => $data['phone_number'] ?: null,
            'role_id'       => (int)$data['role_id'],
            'status'        => $data['status']
        ];

        if (!empty($data['password_hash'])) {
            $fields[] = 'password_hash = :password_hash';
            $params['password_hash'] = $data['password_hash'];
        }

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete a user.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get list of users with search, role filters, and pagination.
     */
    public function getUsers(array $filters = [], int $limit = 10, int $offset = 0): array {
        $sql = "
            SELECT u.*, r.name as role_name 
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (u.first_name LIKE :search 
                        OR u.last_name LIKE :search 
                        OR u.email LIKE :search 
                        OR u.student_id LIKE :search 
                        OR u.employee_id LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['role_id'])) {
            $sql .= " AND u.role_id = :role_id";
            $params['role_id'] = (int)$filters['role_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND u.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY u.created_at DESC LIMIT :limit OFFSET :offset";

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
     * Count users matching filter parameters.
     */
    public function countUsers(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM users u WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (u.first_name LIKE :search 
                        OR u.last_name LIKE :search 
                        OR u.email LIKE :search 
                        OR u.student_id LIKE :search 
                        OR u.employee_id LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['role_id'])) {
            $sql .= " AND u.role_id = :role_id";
            $params['role_id'] = (int)$filters['role_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND u.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Get all roles.
     */
    public function getRoles(): array {
        return $this->db->query("SELECT id, name, description FROM roles ORDER BY id ASC")->fetchAll();
    }
}
