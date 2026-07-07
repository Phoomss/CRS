<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\AuthService;
use Exception;

class AuthController extends Controller {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    /**
     * Show Login form.
     */
    public function showLogin(): void {
        // If already logged in, redirect to dashboard
        if (Session::get('user')) {
            $this->redirect('/dashboard');
        }
        $this->render('auth.login', [], 'auth');
    }

    /**
     * Handle Login request.
     */
    public function login(Request $request): void {
        $email = $request->get('email');
        $password = $request->get('password');
        $rememberMe = $request->get('remember_me') === 'on';

        $errors = $this->validate($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            Session::setFlash('old_inputs', $request->all());
            $this->redirect('/login');
        }

        try {
            if ($this->authService->login($email, $password, $rememberMe)) {
                $this->redirect('/dashboard');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Session::setFlash('old_inputs', $request->all());
            $this->redirect('/login');
        }
    }

    /**
     * Show Forgot Password form.
     */
    public function showForgot(): void {
        $this->render('auth.forgot', [], 'auth');
    }

    /**
     * Handle Forgot Password request.
     */
    public function forgot(Request $request): void {
        $email = $request->get('email');
        
        $errors = $this->validate($request->all(), [
            'email' => 'required|email'
        ]);

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect('/forgot-password');
        }

        try {
            $token = $this->authService->forgotPassword($email);
            // Simulate sending reset link
            $resetLink = "/reset-password/{$token}";
            
            Session::setFlash('success', "A password reset link has been simulated. Click here to reset: <a href='{$resetLink}' class='alert-link'>Reset Password</a>");
            $this->redirect('/forgot-password');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect('/forgot-password');
        }
    }

    /**
     * Show Reset Password form.
     */
    public function showReset(Request $request, string $token): void {
        $this->render('auth.reset', ['token' => $token], 'auth');
    }

    /**
     * Handle Reset Password request.
     */
    public function reset(Request $request, string $token): void {
        $password = $request->get('password');
        $confirm = $request->get('confirm_password');

        $errors = $this->validate($request->all(), [
            'password' => 'required|min:6'
        ]);

        if ($password !== $confirm) {
            $errors['confirm_password'][] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect("/reset-password/{$token}");
        }

        try {
            if ($this->authService->resetPassword($token, $password)) {
                Session::setFlash('success', 'Your password has been reset successfully. You can now log in.');
                $this->redirect('/login');
            }
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect("/reset-password/{$token}");
        }
    }

    /**
     * Handle Logout.
     */
    public function logout(): void {
        $this->authService->logout();
        $this->redirect('/login');
    }

    /**
     * Change Password (from user profile).
     */
    public function changePassword(Request $request): void {
        $user = Session::get('user');
        if (!$user) {
            $this->json(['error' => 'Unauthenticated'], 401);
        }

        $oldPassword = $request->get('old_password');
        $newPassword = $request->get('new_password');
        $confirmPassword = $request->get('confirm_new_password');

        $errors = $this->validate($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        if ($newPassword !== $confirmPassword) {
            $errors['confirm_new_password'][] = 'New passwords do not match.';
        }

        if (!empty($errors)) {
            if ($request->isAjax()) {
                $this->json(['errors' => $errors], 422);
            } else {
                Session::setFlash('errors', $errors);
                $this->redirect('/settings/profile');
            }
        }

        try {
            $this->authService->changePassword((int)$user['id'], $oldPassword, $newPassword);
            if ($request->isAjax()) {
                $this->json(['message' => 'Password updated successfully!']);
            } else {
                Session::setFlash('success', 'Password updated successfully!');
                $this->redirect('/settings/profile');
            }
        } catch (Exception $e) {
            if ($request->isAjax()) {
                $this->json(['error' => $e->getMessage()], 400);
            } else {
                Session::setFlash('error', $e->getMessage());
                $this->redirect('/settings/profile');
            }
        }
    }
}
