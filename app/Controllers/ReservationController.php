<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\ReservationService;
use App\Services\LaboratoryService;
use App\Services\ComputerService;
use Exception;

class ReservationController extends Controller {
    private ReservationService $resService;
    private LaboratoryService $labService;
    private ComputerService $compService;

    public function __construct() {
        $this->resService = new ReservationService();
        $this->labService = new LaboratoryService();
        $this->compService = new ComputerService();
    }

    /**
     * Display list of reservations.
     */
    public function index(Request $request): void {
        $user = Session::get('user');
        $role = $user['role_name'] ?? '';

        // Expire outdated bookings automatically
        $this->resService->releaseExpiredReservations();

        $page = (int)$request->get('page', 1);
        if ($page < 1) $page = 1;

        $filters = [];
        
        // Students/Lecturers see only their own reservations unless they are Admin/Staff
        if ($role === 'Student' || $role === 'Lecturer') {
            $filters['user_id'] = $user['id'];
        }

        if ($request->get('status_id')) {
            $filters['status_id'] = (int)$request->get('status_id');
        }

        if ($request->get('laboratory_id')) {
            $filters['laboratory_id'] = (int)$request->get('laboratory_id');
        }

        if ($request->get('search')) {
            $filters['search'] = $request->get('search');
        }

        if ($request->get('date')) {
            $filters['date'] = $request->get('date');
        }

        $perPage = 10;
        $reservations = $this->resService->getReservations($filters, $page, $perPage);
        $totalCount = $this->resService->countReservations($filters);
        
        $totalPages = ceil($totalCount / $perPage);

        $labs = $this->labService->getActiveLabs();

        $this->render('reservations.index', [
            'reservations' => $reservations,
            'labs'         => $labs,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'filters'      => $filters
        ]);
    }

    /**
     * Show Reservation creation page.
     */
    public function showCreate(): void {
        $labs = $this->labService->getActiveLabs();
        $this->render('reservations.create', ['labs' => $labs]);
    }

    /**
     * AJAX endpoint: fetch available computers in lab at specific timing.
     */
    public function getAvailableComputers(Request $request): void {
        $labId = (int)$request->get('laboratory_id');
        $date = $request->get('date');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');

        if (!$labId || !$date || !$startTime || !$endTime) {
            $this->json(['error' => 'All booking parameters are required.'], 400);
        }

        $startDateTime = $date . ' ' . $startTime . ':00';
        $endDateTime = $date . ' ' . $endTime . ':00';

        try {
            $computers = $this->compService->getAvailableComputers($labId, $startDateTime, $endDateTime);
            $this->json(['computers' => $computers]);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Store new reservation.
     */
    public function store(Request $request): void {
        $user = Session::get('user');
        $labId = $request->get('laboratory_id');
        $date = $request->get('date');
        $startTime = $request->get('start_time');
        $endTime = $request->get('end_time');
        $purpose = $request->get('purpose');
        $computerIds = $request->get('computers'); // Expect array of computer IDs

        $errors = $this->validate($request->all(), [
            'laboratory_id' => 'required',
            'date'          => 'required',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'purpose'       => 'required|max:255'
        ]);

        if (empty($computerIds) || !is_array($computerIds)) {
            $errors['computers'][] = 'Please select at least one computer workstation.';
        }

        if (!empty($errors)) {
            if ($request->isAjax()) {
                $this->json(['errors' => $errors], 422);
            } else {
                Session::setFlash('errors', $errors);
                Session::setFlash('old_inputs', $request->all());
                $this->redirect('/reservations/create');
            }
        }

        $startDateTime = $date . ' ' . $startTime . ':00';
        $endDateTime = $date . ' ' . $endTime . ':00';

        $data = [
            'user_id'       => $user['id'],
            'laboratory_id' => $labId,
            'purpose'       => $purpose,
            'start_time'    => $startDateTime,
            'end_time'      => $endDateTime
        ];

        try {
            $resId = $this->resService->createReservation($data, $computerIds);
            
            if ($request->isAjax()) {
                $this->json([
                    'message' => 'Reservation submitted successfully!',
                    'reservation_id' => $resId
                ]);
            } else {
                Session::setFlash('success', 'Reservation submitted successfully!');
                $this->redirect("/reservations/view/{$resId}");
            }
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['error' => $e->getMessage()], 400);
            } else {
                Session::setFlash('error', $e->getMessage());
                Session::setFlash('old_inputs', $request->all());
                $this->redirect('/reservations/create');
            }
        }
    }

    /**
     * Show Reservation details.
     */
    public function view(Request $request, string $id): void {
        $res = $this->resService->getReservationById((int)$id);
        if (!$res) {
            $this->redirect('/reservations');
        }

        // Security check: User can only view their own booking unless staff/admin
        $user = Session::get('user');
        $role = $user['role_name'] ?? '';
        if ($role !== 'Super Administrator' && $role !== 'Department Administrator' && $role !== 'Staff' && (int)$res['user_id'] !== (int)$user['id']) {
            Session::setFlash('error', 'Unauthorized access.');
            $this->redirect('/reservations');
        }

        $this->render('reservations.view', ['res' => $res]);
    }

    /**
     * Approve reservation (Admin).
     */
    public function approve(Request $request, string $id): void {
        $user = Session::get('user');
        $remarks = $request->get('remarks');

        try {
            $this->resService->approveReservation((int)$id, (int)$user['id'], $remarks);
            
            if ($request->isAjax()) {
                $this->json(['message' => 'Reservation approved successfully.']);
            } else {
                Session::setFlash('success', 'Reservation approved successfully.');
                $this->redirect("/reservations/view/{$id}");
            }
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['error' => $e->getMessage()], 400);
            } else {
                Session::setFlash('error', $e->getMessage());
                $this->redirect("/reservations/view/{$id}");
            }
        }
    }

    /**
     * Reject reservation (Admin).
     */
    public function reject(Request $request, string $id): void {
        $user = Session::get('user');
        $remarks = $request->get('remarks');

        if (empty($remarks)) {
            if ($request->isAjax()) {
                $this->json(['error' => 'Rejection remarks are required.'], 422);
            } else {
                Session::setFlash('error', 'Rejection remarks are required.');
                $this->redirect("/reservations/view/{$id}");
            }
            return;
        }

        try {
            $this->resService->rejectReservation((int)$id, (int)$user['id'], $remarks);
            
            if ($request->isAjax()) {
                $this->json(['message' => 'Reservation rejected.']);
            } else {
                Session::setFlash('success', 'Reservation has been rejected.');
                $this->redirect("/reservations/view/{$id}");
            }
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['error' => $e->getMessage()], 400);
            } else {
                Session::setFlash('error', $e->getMessage());
                $this->redirect("/reservations/view/{$id}");
            }
        }
    }

    /**
     * Cancel reservation.
     */
    public function cancel(Request $request, string $id): void {
        $user = Session::get('user');
        try {
            $this->resService->cancelReservation((int)$id, (int)$user['id']);
            
            if ($request->isAjax()) {
                $this->json(['message' => 'Reservation cancelled.']);
            } else {
                Session::setFlash('success', 'Reservation cancelled.');
                $this->redirect("/reservations/view/{$id}");
            }
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['error' => $e->getMessage()], 400);
            } else {
                Session::setFlash('error', $e->getMessage());
                $this->redirect("/reservations/view/{$id}");
            }
        }
    }

    /**
     * Check-in to computer.
     */
    public function checkIn(Request $request): void {
        $user = Session::get('user');
        $resId = (int)$request->get('reservation_id');
        $compId = (int)$request->get('computer_id');

        try {
            $this->resService->checkIn($resId, $compId, (int)$user['id']);
            $this->json(['message' => 'Checked in successfully! Workstation is now active.']);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Check-out from computer.
     */
    public function checkOut(Request $request): void {
        $user = Session::get('user');
        $resId = (int)$request->get('reservation_id');
        $compId = (int)$request->get('computer_id');

        try {
            $this->resService->checkOut($resId, $compId, (int)$user['id']);
            $this->json(['message' => 'Checked out successfully. Reservation marked as Completed.']);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * AJAX endpoint: fetch reservation data arrays for FullCalendar integration.
     */
    public function calendarData(Request $request): void {
        $start = $request->get('start');
        $end = $request->get('end');

        if (!$start || !$end) {
            $this->json(['error' => 'Start and end boundaries are required.'], 400);
        }

        $events = $this->resService->getReservationsForCalendar($start, $end);
        
        $formattedEvents = [];
        foreach ($events as $e) {
            // Pick color according to status
            // Pending=1, Approved=2, Rejected=3, Cancelled=4, Completed=5, Expired=6
            $color = '#ffc107'; // yellow (Pending)
            if ($e['status_id'] == 2) $color = '#17a2b8'; // blue (Approved)
            if ($e['status_id'] == 5) $color = '#28a745'; // green (Completed)
            if ($e['status_id'] == 6) $color = '#6c757d'; // gray (Expired)

            $formattedEvents[] = [
                'id'              => $e['id'],
                'title'           => '[' . $e['lab_name'] . '] ' . $e['title'],
                'start'           => $e['start'],
                'end'             => $e['end'],
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => '#fff',
                'url'             => '/reservations/view/' . $e['id'],
                'userId'          => (int)$e['user_id']
            ];
        }

        $this->json($formattedEvents);
    }
}
