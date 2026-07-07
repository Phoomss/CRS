<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\UserService;
use Exception;

class UserController extends Controller {
    private UserService $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    /**
     * Display list of users.
     */
    public function index(Request $request): void {
        $page = (int)$request->get('page', 1);
        if ($page < 1) $page = 1;

        $filters = [
            'search'  => $request->get('search'),
            'role_id' => $request->get('role_id'),
            'status'  => $request->get('status')
        ];

        $perPage = 10;
        $users = $this->userService->getUsers($filters, $page, $perPage);
        $totalCount = $this->userService->countUsers($filters);
        
        $totalPages = ceil($totalCount / $perPage);
        $roles = $this->userService->getRoles();

        $this->render('users.index', [
            'users'      => $users,
            'roles'      => $roles,
            'page'       => $page,
            'totalPages' => $totalPages,
            'filters'    => $filters
        ]);
    }

    /**
     * Show create user page.
     */
    public function showCreate(): void {
        $roles = $this->userService->getRoles();
        $this->render('users.create', ['roles' => $roles]);
    }

    /**
     * Store new user.
     */
    public function store(Request $request): void {
        $errors = $this->validate($request->all(), [
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'email'      => 'required|email|max:150',
            'role_id'    => 'required',
            'password'   => 'required|min:6'
        ]);

        $roleId = (int)$request->get('role_id');
        
        // Custom validations based on role
        if ($roleId === 5 && empty($request->get('student_id'))) { // Student
            $errors['student_id'][] = 'Student ID is required for student accounts.';
        }
        if (in_array($roleId, [1, 2, 3, 4]) && empty($request->get('employee_id'))) { // Staff/Admin/Lecturer
            $errors['employee_id'][] = 'Employee ID is required for staff/lecturer accounts.';
        }

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            Session::setFlash('old_inputs', $request->all());
            $this->redirect('/users/create');
        }

        try {
            $this->userService->createUser($request->all());
            Session::setFlash('success', 'User account created successfully!');
            $this->redirect('/users');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Session::setFlash('old_inputs', $request->all());
            $this->redirect('/users/create');
        }
    }

    /**
     * Show edit user page.
     */
    public function showEdit(Request $request, string $id): void {
        $user = $this->userService->getUserById((int)$id);
        if (!$user) {
            $this->redirect('/users');
        }

        $roles = $this->userService->getRoles();
        $this->render('users.edit', [
            'targetUser' => $user,
            'roles'      => $roles
        ]);
    }

    /**
     * Update existing user.
     */
    public function update(Request $request, string $id): void {
        $errors = $this->validate($request->all(), [
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'email'      => 'required|email|max:150',
            'role_id'    => 'required',
            'status'     => 'required'
        ]);

        $roleId = (int)$request->get('role_id');
        
        if ($roleId === 5 && empty($request->get('student_id'))) {
            $errors['student_id'][] = 'Student ID is required for student accounts.';
        }
        if (in_array($roleId, [1, 2, 3, 4]) && empty($request->get('employee_id'))) {
            $errors['employee_id'][] = 'Employee ID is required for staff/lecturer accounts.';
        }

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect("/users/edit/{$id}");
        }

        try {
            $this->userService->updateUser((int)$id, $request->all());
            Session::setFlash('success', 'User account updated successfully.');
            $this->redirect('/users');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect("/users/edit/{$id}");
        }
    }

    /**
     * Delete user.
     */
    public function delete(Request $request, string $id): void {
        try {
            $this->userService->deleteUser((int)$id);
            
            if ($request->isAjax()) {
                $this->json(['message' => 'User deleted successfully.']);
            } else {
                Session::setFlash('success', 'User deleted successfully.');
                $this->redirect('/users');
            }
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['error' => $e->getMessage()], 400);
            } else {
                Session::setFlash('error', $e->getMessage());
                $this->redirect('/users');
            }
        }
    }
}
