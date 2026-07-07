<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\LaboratoryService;
use Exception;

class LaboratoryController extends Controller {
    private LaboratoryService $labService;

    public function __construct() {
        $this->labService = new LaboratoryService();
    }

    /**
     * Display list of laboratories.
     */
    public function index(Request $request): void {
        $page = (int)$request->get('page', 1);
        if ($page < 1) $page = 1;

        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status')
        ];

        $perPage = 10;
        $labs = $this->labService->getLabs($filters, $page, $perPage);
        $totalCount = $this->labService->countLabs($filters);
        
        $totalPages = ceil($totalCount / $perPage);

        $this->render('laboratories.index', [
            'labs'       => $labs,
            'page'       => $page,
            'totalPages' => $totalPages,
            'filters'    => $filters
        ]);
    }

    /**
     * Show creation form.
     */
    public function showCreate(): void {
        $this->render('laboratories.create');
    }

    /**
     * Store new laboratory.
     */
    public function store(Request $request): void {
        $errors = $this->validate($request->all(), [
            'code'     => 'required|max:50',
            'name'     => 'required|max:100',
            'building' => 'required|max:100',
            'floor'    => 'required|max:50',
            'capacity' => 'required|numeric'
        ]);

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            Session::setFlash('old_inputs', $request->all());
            $this->redirect('/laboratories/create');
        }

        try {
            $this->labService->createLab($request->all());
            Session::setFlash('success', 'Laboratory created successfully!');
            $this->redirect('/laboratories');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Session::setFlash('old_inputs', $request->all());
            $this->redirect('/laboratories/create');
        }
    }

    /**
     * Show edit form.
     */
    public function showEdit(Request $request, string $id): void {
        $lab = $this->labService->getLabById((int)$id);
        if (!$lab) {
            $this->redirect('/laboratories');
        }
        $this->render('laboratories.edit', ['lab' => $lab]);
    }

    /**
     * Update existing laboratory.
     */
    public function update(Request $request, string $id): void {
        $errors = $this->validate($request->all(), [
            'code'     => 'required|max:50',
            'name'     => 'required|max:100',
            'building' => 'required|max:100',
            'floor'    => 'required|max:50',
            'capacity' => 'required|numeric',
            'status'   => 'required'
        ]);

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect("/laboratories/edit/{$id}");
        }

        try {
            $this->labService->updateLab((int)$id, $request->all());
            Session::setFlash('success', 'Laboratory updated successfully.');
            $this->redirect('/laboratories');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect("/laboratories/edit/{$id}");
        }
    }

    /**
     * Delete laboratory.
     */
    public function delete(Request $request, string $id): void {
        try {
            $this->labService->deleteLab((int)$id);
            
            if ($request->isAjax()) {
                $this->json(['message' => 'Laboratory deleted successfully.']);
            } else {
                Session::setFlash('success', 'Laboratory deleted successfully.');
                $this->redirect('/laboratories');
            }
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['error' => $e->getMessage()], 400);
            } else {
                Session::setFlash('error', $e->getMessage());
                $this->redirect('/laboratories');
            }
        }
    }
}
