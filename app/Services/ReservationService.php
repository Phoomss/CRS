<?php

namespace App\Services;

use App\Repositories\ReservationRepository;
use App\Repositories\ComputerRepository;
use App\Repositories\LaboratoryRepository;
use App\Repositories\UserRepository;
use App\Core\Session;
use Exception;

class ReservationService {
    private ReservationRepository $resRepository;
    private ComputerRepository $compRepository;
    private LaboratoryRepository $labRepository;
    private SettingService $settingService;
    private ActivityLogService $logService;
    private NotificationService $notificationService;

    public function __construct() {
        $this->resRepository = new ReservationRepository();
        $this->compRepository = new ComputerRepository();
        $this->labRepository = new LaboratoryRepository();
        $this->settingService = new SettingService();
        $this->logService = new ActivityLogService();
        $this->notificationService = new NotificationService();
    }

    /**
     * Create a reservation.
     */
    public function createReservation(array $data, array $computerIds): int {
        $userId = (int)$data['user_id'];
        $labId = (int)$data['laboratory_id'];
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];

        // 1. Basic validation: Start and End times
        $startTs = strtotime($startTime);
        $endTs = strtotime($endTime);
        $now = time();

        if ($startTs === false || $endTs === false) {
            throw new Exception("Invalid date and time formats.");
        }

        if ($startTs < $now - 86400) { // Allow 24 hours leeway to accommodate timezone offsets and Docker clock drift
            throw new Exception("Reservation start time must be in the future.");
        }

        if ($endTs <= $startTs) {
            throw new Exception("End time must be after the start time.");
        }

        // 2. Validate Reservation Duration
        $durationHours = ($endTs - $startTs) / 3600;
        $maxHours = $this->settingService->getInt('max_reservation_hours', 3);
        if ($durationHours > $maxHours) {
            throw new Exception("Reservation duration cannot exceed the configured limit of {$maxHours} hours.");
        }

        // 3. Check Maximum Reservations Limit Per User
        $user = (new UserRepository())->findById($userId);
        $userRole = $user['role_name'] ?? '';
        
        // Students have restrictions on max active bookings
        if ($userRole === 'Student') {
            $activeCount = $this->resRepository->countActiveReservationsByUser($userId);
            $maxBookings = $this->settingService->getInt('max_reservations_per_user', 5);
            if ($activeCount >= $maxBookings) {
                throw new Exception("You have reached the maximum active reservations limit of {$maxBookings}.");
            }
        }

        // 4. Verify lab and computers status
        $lab = $this->labRepository->findById($labId);
        if (!$lab || $lab['status'] !== 'active') {
            throw new Exception("The selected laboratory is currently inactive or not found.");
        }

        if (empty($computerIds)) {
            throw new Exception("You must select at least one computer workstation.");
        }

        foreach ($computerIds as $compId) {
            $comp = $this->compRepository->findById($compId);
            if (!$comp) {
                throw new Exception("One of the selected computers does not exist.");
            }
            if ($comp['laboratory_id'] !== $labId) {
                throw new Exception("Selected computer '{$comp['code']}' does not belong to the selected laboratory.");
            }
            // For normal users, check computer status in master table
            if ($comp['status'] !== 'available') {
                throw new Exception("Computer '{$comp['code']}' is currently '{$comp['status']}' and cannot be reserved.");
            }
        }

        // 5. Default status: Lecturers/Admins auto-approved, Students pending
        $autoApproveRoles = ['Super Administrator', 'Department Administrator', 'Lecturer'];
        $statusId = in_array($userRole, $autoApproveRoles) ? 2 : 1; // 2: Approved, 1: Pending

        $data['status_id'] = $statusId;

        // 6. Database creation via transaction
        $resId = $this->resRepository->create($data, $computerIds);

        // 7. Send notification & log activity
        $this->logService->log('create_reservation', [
            'reservation_id' => $resId,
            'status'         => $statusId == 2 ? 'Approved' : 'Pending',
            'start_time'     => $startTime,
            'end_time'       => $endTime,
            'computers'      => $computerIds
        ]);

        if ($statusId === 2) {
            $this->notificationService->send($userId, "Reservation Approved", "Your reservation #{$resId} for laboratory {$lab['name']} has been automatically approved.", 'both');
        } else {
            $this->notificationService->send($userId, "Reservation Received", "Your reservation #{$resId} is pending administrator review.", 'dashboard');
        }

        return $resId;
    }

    /**
     * Approve reservation.
     */
    public function approveReservation(int $id, int $adminId, ?string $remarks = null): bool {
        $res = $this->resRepository->findById($id);
        if (!$res) {
            throw new Exception("Reservation not found.");
        }

        if ((int)$res['status_id'] !== 1) {
            throw new Exception("Only Pending reservations can be approved.");
        }

        $result = $this->resRepository->updateStatus($id, 2, $remarks, $adminId); // 2 = Approved
        
        if ($result) {
            $this->logService->log('approve_reservation', ['reservation_id' => $id, 'remarks' => $remarks]);
            $this->notificationService->send((int)$res['user_id'], "Reservation Approved", "Your reservation #{$id} has been approved. Please check in on time.", 'both');
        }

        return $result;
    }

    /**
     * Reject reservation.
     */
    public function rejectReservation(int $id, int $adminId, ?string $remarks = null): bool {
        $res = $this->resRepository->findById($id);
        if (!$res) {
            throw new Exception("Reservation not found.");
        }

        if ((int)$res['status_id'] !== 1) {
            throw new Exception("Only Pending reservations can be rejected.");
        }

        $result = $this->resRepository->updateStatus($id, 3, $remarks, $adminId); // 3 = Rejected
        
        if ($result) {
            $this->logService->log('reject_reservation', ['reservation_id' => $id, 'remarks' => $remarks]);
            $this->notificationService->send((int)$res['user_id'], "Reservation Rejected", "Your reservation #{$id} has been rejected. Reason: {$remarks}", 'both');
        }

        return $result;
    }

    /**
     * Cancel reservation.
     */
    public function cancelReservation(int $id, int $userId): bool {
        $res = $this->resRepository->findById($id);
        if (!$res) {
            throw new Exception("Reservation not found.");
        }

        // Validate cancellation permission
        $user = (new UserRepository())->findById($userId);
        $role = $user['role_name'] ?? '';

        if ($role !== 'Super Administrator' && $role !== 'Department Administrator' && (int)$res['user_id'] !== $userId) {
            throw new Exception("You are not authorized to cancel this reservation.");
        }

        // Allow cancellation before start time
        if (strtotime($res['start_time']) < time() && $role === 'Student') {
            throw new Exception("Students cannot cancel a reservation after the scheduled start time.");
        }

        if (in_array((int)$res['status_id'], [4, 5, 6])) {
            throw new Exception("This reservation has already been completed, cancelled, or expired.");
        }

        $result = $this->resRepository->updateStatus($id, 4, 'Cancelled by user/admin'); // 4 = Cancelled
        
        if ($result) {
            $this->logService->log('cancel_reservation', ['reservation_id' => $id]);
            $this->notificationService->send((int)$res['user_id'], "Reservation Cancelled", "Your reservation #{$id} has been successfully cancelled.", 'both');
        }

        return $result;
    }

    /**
     * Handle user check-in.
     */
    public function checkIn(int $reservationId, int $computerId, int $userId): bool {
        $res = $this->resRepository->findById($reservationId);
        if (!$res) {
            throw new Exception("Reservation not found.");
        }

        if ((int)$res['user_id'] !== $userId) {
            throw new Exception("You can only check in to your own reservations.");
        }

        if ((int)$res['status_id'] !== 2) {
            throw new Exception("You can only check in to an Approved reservation.");
        }

        $now = time();
        $startTs = strtotime($res['start_time']);
        $endTs = strtotime($res['end_time']);

        // Cannot check in early (e.g. more than 15 mins before start time)
        if ($now < $startTs - (15 * 60)) {
            throw new Exception("It is too early to check in. Check-in opens 15 minutes before the reservation starts.");
        }

        if ($now > $endTs) {
            throw new Exception("This reservation time block has already passed.");
        }

        // Record check in
        $result = $this->resRepository->recordCheckIn($reservationId, $computerId);
        if ($result) {
            $this->logService->log('check_in', ['reservation_id' => $reservationId, 'computer_id' => $computerId]);
        }
        return $result;
    }

    /**
     * Handle user check-out.
     */
    public function checkOut(int $reservationId, int $computerId, int $userId): bool {
        $res = $this->resRepository->findById($reservationId);
        if (!$res) {
            throw new Exception("Reservation not found.");
        }

        if ((int)$res['user_id'] !== $userId) {
            throw new Exception("You can only check out from your own reservations.");
        }

        if ($res['check_in_time'] === null) {
            throw new Exception("You must check in before checking out.");
        }

        if ($res['check_out_time'] !== null) {
            throw new Exception("You have already checked out of this reservation.");
        }

        // Record check out
        $result = $this->resRepository->recordCheckOut($reservationId, $computerId);
        
        if ($result) {
            // Update reservation status to Completed
            $this->resRepository->updateStatus($reservationId, 5, 'Check-out completed'); // 5 = Completed
            $this->logService->log('check_out', ['reservation_id' => $reservationId, 'computer_id' => $computerId]);
            $this->notificationService->send($userId, "Reservation Completed", "Thank you for using the computer lab. Your session has ended.", 'dashboard');
        }

        return $result;
    }

    /**
     * Run expiry check: updates all missed reservations to "Expired".
     */
    public function releaseExpiredReservations(): int {
        $expiryMinutes = $this->settingService->getInt('check_in_expiry_minutes', 15);
        $expiredIds = $this->resRepository->findExpiredReservations($expiryMinutes);
        
        $count = 0;
        foreach ($expiredIds as $id) {
            $res = $this->resRepository->findById((int)$id);
            if ($res) {
                $this->resRepository->updateStatus((int)$id, 6, 'Check-in window expired'); // 6 = Expired
                $this->logService->log('expire_reservation', ['reservation_id' => $id]);
                $this->notificationService->send((int)$res['user_id'], "Reservation Expired", "Your reservation #{$id} has expired because you failed to check in on time.", 'both');
                $count++;
            }
        }
        return $count;
    }

    /**
     * Get reservation by ID.
     */
    public function getReservationById(int $id): ?array {
        return $this->resRepository->findById($id);
    }

    /**
     * Get reservations.
     */
    public function getReservations(array $filters = [], int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        return $this->resRepository->getReservations($filters, $perPage, $offset);
    }

    /**
     * Count reservations.
     */
    public function countReservations(array $filters = []): int {
        return $this->resRepository->countReservations($filters);
    }

    /**
     * Get reservations for Calendar.
     */
    public function getReservationsForCalendar(string $start, string $end): array {
        return $this->resRepository->getReservationsForCalendar($start, $end);
    }
}
