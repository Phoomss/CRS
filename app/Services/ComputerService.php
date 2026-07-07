<?php

namespace App\Services;

use App\Repositories\ComputerRepository;
use App\Core\Database;
use Exception;

class ComputerService {
    private ComputerRepository $computerRepository;
    private ActivityLogService $logService;

    public function __construct() {
        $this->computerRepository = new ComputerRepository();
        $this->logService = new ActivityLogService();
    }

    /**
     * Create a new computer workstation.
     */
    public function createComputer(array $data): int {
        $this->validateComputerUniqueness($data);

        $compId = $this->computerRepository->create($data);

        $this->logService->log('create_computer', [
            'computer_id' => $compId,
            'code' => $data['code'],
            'asset_number' => $data['asset_number']
        ]);

        return $compId;
    }

    /**
     * Update an existing workstation.
     */
    public function updateComputer(int $id, array $data): bool {
        $comp = $this->computerRepository->findById($id);
        if (!$comp) {
            throw new Exception("Computer not found.");
        }

        $this->validateComputerUniqueness($data, $id);

        // Keep existing image if not replacing
        if (empty($data['image_path'])) {
            $data['image_path'] = $comp['image_path'];
        }

        $result = $this->computerRepository->update($id, $data);

        if ($result) {
            $this->logService->log('update_computer', [
                'computer_id' => $id,
                'code' => $data['code'],
                'asset_number' => $data['asset_number']
            ]);
        }

        return $result;
    }

    /**
     * Delete a computer.
     */
    public function deleteComputer(int $id): bool {
        $comp = $this->computerRepository->findById($id);
        if (!$comp) {
            throw new Exception("Computer not found.");
        }

        // Prevent delete if associated with any reservations
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM reservation_details WHERE computer_id = :comp_id");
        $stmt->execute(['comp_id' => $id]);
        $resCount = (int)$stmt->fetchColumn();

        if ($resCount > 0) {
            throw new Exception("Cannot delete computer workstation. It has active or past reservation history. Change status to 'disabled' or 'offline' instead.");
        }

        $result = $this->computerRepository->delete($id);

        if ($result) {
            $this->logService->log('delete_computer', [
                'computer_id' => $id,
                'code' => $comp['code']
            ]);
        }

        return $result;
    }

    /**
     * Get computer by ID.
     */
    public function getComputerById(int $id): ?array {
        return $this->computerRepository->findById($id);
    }

    /**
     * Get paginated computers list.
     */
    public function getComputers(array $filters = [], int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        return $this->computerRepository->getComputers($filters, $perPage, $offset);
    }

    /**
     * Count computers.
     */
    public function countComputers(array $filters = []): int {
        return $this->computerRepository->countComputers($filters);
    }

    /**
     * Update computer status.
     */
    public function updateStatus(int $id, string $status): bool {
        $validStatuses = ['available', 'reserved', 'maintenance', 'offline', 'disabled'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid computer status.");
        }

        $result = $this->computerRepository->updateStatus($id, $status);
        if ($result) {
            $this->logService->log('update_computer_status', [
                'computer_id' => $id,
                'status' => $status
            ]);
        }
        return $result;
    }

    /**
     * Fetch available computers for booking details.
     */
    public function getAvailableComputers(int $labId, string $startTime, string $endTime): array {
        return $this->computerRepository->getAvailableComputers($labId, $startTime, $endTime);
    }

    /**
     * Validate computer code and asset number uniqueness.
     */
    private function validateComputerUniqueness(array $data, ?int $excludeId = null): void {
        // 1. Code
        $existing = $this->computerRepository->findByCode($data['code']);
        if ($existing && ($excludeId === null || (int)$existing['id'] !== $excludeId)) {
            throw new Exception("Computer Code '{$data['code']}' is already registered.");
        }

        // 2. Asset Number
        $existing = $this->computerRepository->findByAssetNumber($data['asset_number']);
        if ($existing && ($excludeId === null || (int)$existing['id'] !== $excludeId)) {
            throw new Exception("Asset Number '{$data['asset_number']}' is already registered.");
        }
    }
}
