<?php

namespace App\Services;

use App\Repositories\LaboratoryRepository;
use App\Repositories\ComputerRepository;
use App\Core\Database;
use Exception;

class LaboratoryService {
    private LaboratoryRepository $labRepository;
    private ActivityLogService $logService;

    public function __construct() {
        $this->labRepository = new LaboratoryRepository();
        $this->logService = new ActivityLogService();
    }

    /**
     * Create a new lab.
     */
    public function createLab(array $data): int {
        $existing = $this->labRepository->findByCode($data['code']);
        if ($existing) {
            throw new Exception("Laboratory Code '{$data['code']}' is already in use.");
        }

        $labId = $this->labRepository->create($data);

        $this->logService->log('create_laboratory', [
            'laboratory_id' => $labId,
            'code' => $data['code'],
            'name' => $data['name']
        ]);

        return $labId;
    }

    /**
     * Update an existing lab.
     */
    public function updateLab(int $id, array $data): bool {
        $lab = $this->labRepository->findById($id);
        if (!$lab) {
            throw new Exception("Laboratory not found.");
        }

        $existing = $this->labRepository->findByCode($data['code']);
        if ($existing && (int)$existing['id'] !== $id) {
            throw new Exception("Laboratory Code '{$data['code']}' is already in use by another lab.");
        }

        $result = $this->labRepository->update($id, $data);

        if ($result) {
            $this->logService->log('update_laboratory', [
                'laboratory_id' => $id,
                'code' => $data['code'],
                'name' => $data['name']
            ]);
        }

        return $result;
    }

    /**
     * Delete a lab.
     */
    public function deleteLab(int $id): bool {
        $lab = $this->labRepository->findById($id);
        if (!$lab) {
            throw new Exception("Laboratory not found.");
        }

        // Prevent delete if computers exist in this lab
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM computers WHERE laboratory_id = :lab_id");
        $stmt->execute(['lab_id' => $id]);
        $computerCount = (int)$stmt->fetchColumn();

        if ($computerCount > 0) {
            throw new Exception("Cannot delete laboratory. There are {$computerCount} computers currently registered in it.");
        }

        // Prevent delete if reservations exist in this lab
        $stmt = $db->prepare("SELECT COUNT(*) FROM reservations WHERE laboratory_id = :lab_id");
        $stmt->execute(['lab_id' => $id]);
        $reservationCount = (int)$stmt->fetchColumn();

        if ($reservationCount > 0) {
            throw new Exception("Cannot delete laboratory. There are past or current reservations associated with this laboratory.");
        }

        $result = $this->labRepository->delete($id);

        if ($result) {
            $this->logService->log('delete_laboratory', [
                'laboratory_id' => $id,
                'code' => $lab['code'],
                'name' => $lab['name']
            ]);
        }

        return $result;
    }

    /**
     * Get laboratory by ID.
     */
    public function getLabById(int $id): ?array {
        return $this->labRepository->findById($id);
    }

    /**
     * Get active laboratories.
     */
    public function getActiveLabs(): array {
        return $this->labRepository->getActiveLabs();
    }

    /**
     * Get all laboratories.
     */
    public function getAllLabs(): array {
        return $this->labRepository->all();
    }

    /**
     * Get paginated laboratories.
     */
    public function getLabs(array $filters = [], int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        return $this->labRepository->getLabs($filters, $perPage, $offset);
    }

    /**
     * Count laboratories.
     */
    public function countLabs(array $filters = []): int {
        return $this->labRepository->countLabs($filters);
    }
}
