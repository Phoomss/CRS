<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Core\Session;
use Exception;

class AuthService {
    private UserRepository $userRepository;
    private ActivityLogService $logService;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->logService = new ActivityLogService();
    }

    /**
     * Login user.
     */
    public function login(string $email, string $password, bool $rememberMe = false): bool {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            $this->logService->log('login_failed', ['email' => $email, 'reason' => 'User not found']);
            throw new Exception("Invalid email or password.");
        }

        if ($user['status'] !== 'active') {
            $this->logService->log('login_failed', ['email' => $email, 'reason' => 'Account ' . $user['status']]);
            throw new Exception("Your account has been " . $user['status'] . ". Please contact administration.");
        }

        // Verify password hash
        if (!password_verify($password, $user['password_hash'])) {
            $this->logService->log('login_failed', ['email' => $email, 'reason' => 'Password incorrect']);
            throw new Exception("Invalid email or password.");
        }

        // Set session
        $this->setAuthSession($user);

        // Handle Remember Me
        if ($rememberMe) {
            $token = bin2hex(random_bytes(32));
            $this->userRepository->updateRememberToken((int)$user['id'], $token);
            // Set remember cookie for 30 days
            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
        }

        $this->logService->log('login', ['email' => $email]);
        return true;
    }

    /**
     * Set session variables for authenticated user.
     */
    private function setAuthSession(array $user): void {
        // Clean password hash from session storage
        unset($user['password_hash']);
        unset($user['remember_token']);
        unset($user['reset_token']);
        unset($user['reset_token_expires_at']);

        Session::set('user', $user);

        // Load permissions
        $permissions = $this->userRepository->getPermissionsByUserId((int)$user['id']);
        Session::set('user_permissions', $permissions);
    }

    /**
     * Check if user is remembered via cookie.
     */
    public function checkRememberMe(): bool {
        if (Session::get('user')) {
            return true;
        }

        $token = $_COOKIE['remember_token'] ?? null;
        if ($token) {
            $user = $this->userRepository->findByRememberToken($token);
            if ($user) {
                $this->setAuthSession($user);
                $this->logService->log('login_remember_me', ['email' => $user['email']]);
                return true;
            }
        }
        return false;
    }

    /**
     * Logout user.
     */
    public function logout(): void {
        $user = Session::get('user');
        if ($user) {
            $this->logService->log('logout', ['email' => $user['email']]);
            $this->userRepository->updateRememberToken((int)$user['id'], null);
        }

        // Delete cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

        Session::destroy();
    }

    /**
     * Initiate password reset flow.
     */
    public function forgotPassword(string $email): string {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            throw new Exception("No account found with that email address.");
        }

        $token = bin2hex(random_bytes(20));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->userRepository->updateResetToken($email, $token, $expiresAt);

        $this->logService->log('forgot_password_requested', ['email' => $email]);

        // Return token so controller can generate the link (e.g. to print in dashboard for simulation or send via notification service)
        return $token;
    }

    /**
     * Reset password using token.
     */
    public function resetPassword(string $token, string $password): bool {
        $user = $this->userRepository->findByResetToken($token);
        if (!$user) {
            throw new Exception("Invalid or expired password reset link.");
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $result = $this->userRepository->updatePassword((int)$user['id'], $hash);

        if ($result) {
            $this->logService->log('password_reset_success', ['email' => $user['email']]);
        }

        return $result;
    }

    /**
     * Change password for logged-in user.
     */
    public function changePassword(int $userId, string $oldPassword, string $newPassword): bool {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new Exception("User not found.");
        }

        // We need password_hash to verify, findById doesn't return password_hash? Let's check:
        // Wait, the findById repository method does select u.*, which includes password_hash! Excellent.
        if (!password_verify($oldPassword, $user['password_hash'])) {
            throw new Exception("Current password is incorrect.");
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $result = $this->userRepository->updatePassword($userId, $hash);

        if ($result) {
            $this->logService->log('password_changed', ['email' => $user['email']]);
        }

        return $result;
    }
}
