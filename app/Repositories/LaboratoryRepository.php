<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class LaboratoryRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Find a laboratory by its ID.
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM laboratories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $lab = $stmt->fetch();
        return $lab ?: null;
    }

    /**
     * Find a laboratory by its code.
     */
    public function findByCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM laboratories WHERE code = :code");
        $stmt->execute(['code' => $code]);
        $lab = $stmt->fetch();
        return $lab ?: null;
    }

    /**
     * Create a new laboratory.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO laboratories (code, name, building, floor, capacity, description, status, created_at)
            VALUES (:code, :name, :building, :floor, :capacity, :description, :status, NOW())
        ");
        $stmt->execute([
            'code'        => $data['code'],
            'name'        => $data['name'],
            'building'    => $data['building'],
            'floor'       => $data['floor'],
            'capacity'    => (int)$data['capacity'],
            'description' => $data['description'] ?: null,
            'status'      => $data['status'] ?? 'active'
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update an existing laboratory.
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE laboratories 
            SET code = :code, name = :name, building = :building, floor = :floor, 
                capacity = :capacity, description = :description, status = :status
            WHERE id = :id
        ");
        return $stmt->execute([
            'id'          => $id,
            'code'        => $data['code'],
            'name'        => $data['name'],
            'building'    => $data['building'],
            'floor'       => $data['floor'],
            'capacity'    => (int)$data['capacity'],
            'description' => $data['description'] ?: null,
            'status'      => $data['status']
        ]);
    }

    /**
     * Delete a laboratory.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM laboratories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Retrieve all laboratories.
     */
    public function all(): array {
        return $this->db->query("SELECT * FROM laboratories ORDER BY code ASC")->fetchAll();
    }

    /**
     * Retrieve active laboratories.
     */
    public function getActiveLabs(): array {
        return $this->db->query("SELECT * FROM laboratories WHERE status = 'active' ORDER BY code ASC")->fetchAll();
    }

    /**
     * Get paginated and filtered laboratories.
     */
    public function getLabs(array $filters = [], int $limit = 10, int $offset = 0): array {
        $sql = "SELECT * FROM laboratories WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (code LIKE :search OR name LIKE :search OR building LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY code ASC LIMIT :limit OFFSET :offset";

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
     * Count laboratories matching filters.
     */
    public function countLabs(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM laboratories WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (code LIKE :search OR name LIKE :search OR building LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }
}
