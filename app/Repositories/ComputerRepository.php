<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class ComputerRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Find computer by ID.
     */
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT c.*, l.name as laboratory_name, l.code as laboratory_code 
            FROM computers c
            JOIN laboratories l ON c.laboratory_id = l.id
            WHERE c.id = :id
        ");
        $stmt->execute(['id' => $id]);
        $comp = $stmt->fetch();
        return $comp ?: null;
    }

    /**
     * Find computer by Code.
     */
    public function findByCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM computers WHERE code = :code");
        $stmt->execute(['code' => $code]);
        $comp = $stmt->fetch();
        return $comp ?: null;
    }

    /**
     * Find computer by Asset Number.
     */
    public function findByAssetNumber(string $assetNumber): ?array {
        $stmt = $this->db->prepare("SELECT * FROM computers WHERE asset_number = :asset_number");
        $stmt->execute(['asset_number' => $assetNumber]);
        $comp = $stmt->fetch();
        return $comp ?: null;
    }

    /**
     * Create a new computer.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO computers (
                code, name, asset_number, laboratory_id, brand, model, 
                cpu, ram, storage, operating_system, ip_address, status, image_path, created_at
            ) VALUES (
                :code, :name, :asset_number, :laboratory_id, :brand, :model, 
                :cpu, :ram, :storage, :operating_system, :ip_address, :status, :image_path, NOW()
            )
        ");
        
        $stmt->execute([
            'code'             => $data['code'],
            'name'             => $data['name'],
            'asset_number'     => $data['asset_number'],
            'laboratory_id'    => (int)$data['laboratory_id'],
            'brand'            => $data['brand'],
            'model'            => $data['model'],
            'cpu'              => $data['cpu'],
            'ram'              => $data['ram'],
            'storage'          => $data['storage'],
            'operating_system' => $data['operating_system'],
            'ip_address'       => $data['ip_address'],
            'status'           => $data['status'] ?? 'available',
            'image_path'       => $data['image_path'] ?: null
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update computer details.
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE computers 
            SET code = :code, name = :name, asset_number = :asset_number, laboratory_id = :laboratory_id, 
                brand = :brand, model = :model, cpu = :cpu, ram = :ram, storage = :storage, 
                operating_system = :operating_system, ip_address = :ip_address, status = :status, 
                image_path = :image_path
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id'               => $id,
            'code'             => $data['code'],
            'name'             => $data['name'],
            'asset_number'     => $data['asset_number'],
            'laboratory_id'    => (int)$data['laboratory_id'],
            'brand'            => $data['brand'],
            'model'            => $data['model'],
            'cpu'              => $data['cpu'],
            'ram'              => $data['ram'],
            'storage'          => $data['storage'],
            'operating_system' => $data['operating_system'],
            'ip_address'       => $data['ip_address'],
            'status'           => $data['status'],
            'image_path'       => $data['image_path'] ?: null
        ]);
    }

    /**
     * Delete a computer.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM computers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Update computer status.
     */
    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE computers SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Get paginated computers with filters.
     */
    public function getComputers(array $filters = [], int $limit = 10, int $offset = 0): array {
        $sql = "
            SELECT c.*, l.name as laboratory_name, l.code as laboratory_code 
            FROM computers c
            JOIN laboratories l ON c.laboratory_id = l.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (c.code LIKE :search OR c.name LIKE :search OR c.asset_number LIKE :search OR c.brand LIKE :search OR c.cpu LIKE :search OR c.ip_address LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['laboratory_id'])) {
            $sql .= " AND c.laboratory_id = :laboratory_id";
            $params['laboratory_id'] = (int)$filters['laboratory_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY c.code ASC LIMIT :limit OFFSET :offset";

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
     * Count computers matching filters.
     */
    public function countComputers(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM computers c WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (c.code LIKE :search OR c.name LIKE :search OR c.asset_number LIKE :search OR c.brand LIKE :search OR c.cpu LIKE :search OR c.ip_address LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['laboratory_id'])) {
            $sql .= " AND c.laboratory_id = :laboratory_id";
            $params['laboratory_id'] = (int)$filters['laboratory_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Retrieve available computers in a specific lab that do NOT overlap with any active reservations.
     * Active reservations = status_id IN (1, 2) (1=Pending, 2=Approved)
     * Overlap formula: r.start_time < :end_time AND r.end_time > :start_time
     */
    public function getAvailableComputers(int $labId, string $startTime, string $endTime): array {
        $stmt = $this->db->prepare("
            SELECT c.* 
            FROM computers c
            WHERE c.laboratory_id = :laboratory_id
              AND c.status = 'available'
              AND c.id NOT IN (
                  SELECT cd.computer_id 
                  FROM reservation_details cd
                  JOIN reservations r ON cd.reservation_id = r.id
                  WHERE r.laboratory_id = :laboratory_id2
                    AND r.status_id IN (1, 2)
                    AND r.start_time < :end_time
                    AND r.end_time > :start_time
              )
            ORDER BY c.code ASC
        ");
        
        $stmt->execute([
            'laboratory_id'  => $labId,
            'laboratory_id2' => $labId,
            'start_time'     => $startTime,
            'end_time'       => $endTime
        ]);

        return $stmt->fetchAll();
    }
}
