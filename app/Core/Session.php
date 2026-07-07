<?php

namespace App\Core;

class Session {
    private static bool $started = false;

    /**
     * Start the session securely.
     */
    public static function start(): void {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session cookie parameters
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            session_start([
                'cookie_lifetime' => 0, // Until browser closes
                'cookie_path'     => '/',
                'cookie_secure'   => $secure,
                'cookie_httponly' => true,
                'cookie_samesite' => 'Lax',
            ]);
        }

        self::$started = true;
        self::ageFlashData();
        self::checkTimeout();
    }

    /**
     * Get session instance helper.
     */
    public static function getInstance(): self {
        self::start();
        return new self();
    }

    /**
     * Get a session value.
     */
    public static function get(string $key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a session value.
     */
    public static function set(string $key, $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Remove a session value.
     */
    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the session (logout).
     */
    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        self::$started = false;
    }

    /**
     * Set flash data (only available in the next request).
     */
    public static function setFlash(string $key, $value): void {
        self::start();
        $_SESSION['_flash']['new'][$key] = $value;
    }

    /**
     * Get flash data.
     */
    public static function getFlash(string $key, $default = null) {
        self::start();
        return $_SESSION['_flash']['old'][$key] ?? $_SESSION['_flash']['new'][$key] ?? $default;
    }

    /**
     * Age flash data (moves 'new' to 'old' and discards previous 'old').
     */
    private static function ageFlashData(): void {
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = ['new' => [], 'old' => []];
            return;
        }

        $_SESSION['_flash']['old'] = $_SESSION['_flash']['new'];
        $_SESSION['_flash']['new'] = [];
    }

    /**
     * Retrieve or generate CSRF token for the session.
     */
    public static function getCsrfToken(): string {
        self::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token.
     */
    public static function validateCsrfToken(?string $token): bool {
        self::start();
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check if session has timed out (inactive for X seconds).
     */
    private static function checkTimeout(): void {
        $timeout = (int)($_ENV['SESSION_LIFETIME'] ?? 1800); // 30 mins default
        
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::destroy();
            self::start();
            self::setFlash('error', 'Your session has expired due to inactivity. Please log in again.');
        } else {
            $_SESSION['last_activity'] = time();
        }
    }
}
