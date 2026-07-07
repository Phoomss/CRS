<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\ComputerService;
use App\Services\LaboratoryService;
use Exception;

class ComputerController extends Controller {
    private ComputerService $computerService;
    private LaboratoryService $laboratoryService;

    public function __construct() {
        $this->computerService = new ComputerService();
        $this->laboratoryService = new LaboratoryService();
    }

    /**
     * Display listing of computers.
     */
    public function index(Request $request): void {
        $page = (int)$request->get('page', 1);
        if ($page < 1) $page = 1;

        $filters = [
            'search'        => $request->get('search'),
            'laboratory_id' => $request->get('laboratory_id'),
            'status'        => $request->get('status')
        ];

        $perPage = 10;
        $computers = $this->computerService->getComputers($filters, $page, $perPage);
        $totalCount = $this->computerService->countComputers($filters);
        
        $totalPages = ceil($totalCount / $perPage);

        $labs = $this->laboratoryService->getActiveLabs();

        $this->render('computers.index', [
            'computers'  => $computers,
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
        $labs = $this->laboratoryService->getActiveLabs();
        $this->render('computers.create', ['labs' => $labs]);
    }

    /**
     * Store a new computer.
     */
    public function store(Request $request): void {
        $errors = $this->validate($request->all(), [
            'code'             => 'required|max:50',
            'name'             => 'required|max:100',
            'asset_number'     => 'required|max:100',
            'laboratory_id'    => 'required',
            'brand'            => 'required',
            'model'            => 'required',
            'cpu'              => 'required',
            'ram'              => 'required',
            'storage'          => 'required',
            'operating_system' => 'required',
            'ip_address'       => 'required'
        ]);

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            Session::setFlash('old_inputs', $request->all());
            $this->redirect('/computers/create');
        }

        $data = $request->all();
        $data['image_path'] = $this->handleImageUpload($request);

        try {
            $this->computerService->createComputer($data);
            Session::setFlash('success', 'Computer registered successfully!');
            $this->redirect('/computers');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Session::setFlash('old_inputs', $request->all());
            $this->redirect('/computers/create');
        }
    }

    /**
     * Show edit form.
     */
    public function showEdit(Request $request, string $id): void {
        $comp = $this->computerService->getComputerById((int)$id);
        if (!$comp) {
            $this->redirect('/computers');
        }

        $labs = $this->laboratoryService->getActiveLabs();
        $this->render('computers.edit', [
            'comp' => $comp,
            'labs' => $labs
        ]);
    }

    /**
     * Update an existing computer.
     */
    public function update(Request $request, string $id): void {
        $errors = $this->validate($request->all(), [
            'code'             => 'required|max:50',
            'name'             => 'required|max:100',
            'asset_number'     => 'required|max:100',
            'laboratory_id'    => 'required',
            'brand'            => 'required',
            'model'            => 'required',
            'cpu'              => 'required',
            'ram'              => 'required',
            'storage'          => 'required',
            'operating_system' => 'required',
            'ip_address'       => 'required',
            'status'           => 'required'
        ]);

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect("/computers/edit/{$id}");
        }

        $data = $request->all();
        
        $newImage = $this->handleImageUpload($request);
        if ($newImage) {
            $data['image_path'] = $newImage;
            
            // Delete old image if it exists
            $oldComp = $this->computerService->getComputerById((int)$id);
            if ($oldComp && !empty($oldComp['image_path'])) {
                $oldPath = __DIR__ . '/../../public' . $oldComp['image_path'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
        } else {
            $data['image_path'] = ''; // Service will preserve existing if empty
        }

        try {
            $this->computerService->updateComputer((int)$id, $data);
            Session::setFlash('success', 'Computer workstation updated successfully.');
            $this->redirect('/computers');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect("/computers/edit/{$id}");
        }
    }

    /**
     * Delete a computer.
     */
    public function delete(Request $request, string $id): void {
        try {
            // Delete image file first
            $comp = $this->computerService->getComputerById((int)$id);
            if ($comp && !empty($comp['image_path'])) {
                $filePath = __DIR__ . '/../../public' . $comp['image_path'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            $this->computerService->deleteComputer((int)$id);
            
            if ($request->isAjax()) {
                $this->json(['message' => 'Computer deleted successfully.']);
            } else {
                Session::setFlash('success', 'Computer deleted successfully.');
                $this->redirect('/computers');
            }
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['error' => $e->getMessage()], 400);
            } else {
                Session::setFlash('error', $e->getMessage());
                $this->redirect('/computers');
            }
        }
    }

    /**
     * Print barcodes/QR codes for workstations.
     */
    public function printCodes(Request $request): void {
        $labId = $request->get('laboratory_id');
        $filters = [];
        if ($labId) {
            $filters['laboratory_id'] = (int)$labId;
        }

        $computers = $this->computerService->getComputers($filters, 1, 1000);
        $this->render('computers.print', ['computers' => $computers], 'print');
    }

    /**
     * Handle physical file uploads of computer workstations.
     */
    private function handleImageUpload(Request $request): ?string {
        $file = $request->file('image');
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Invalid image format. Allowed formats: JPEG, PNG, WEBP.");
        }

        $maxSize = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $maxSize) {
            throw new Exception("Image size exceeds the limit of 2MB.");
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = 'comp_' . time() . '_' . uniqid() . '.' . $ext;
        
        $uploadDir = __DIR__ . '/../../public/uploads/computers';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $targetPath = $uploadDir . '/' . $newFilename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/computers/' . $newFilename;
        }

        return null;
    }
}
