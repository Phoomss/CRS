<?php

use App\Core\Session;
use App\Core\Response;

if (!function_exists('esc')) {
    /**
     * Escape HTML output for XSS protection.
     */
    function esc(?string $value): string {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('session')) {
    /**
     * Get or set session variables.
     */
    function session(?string $key = null, $default = null) {
        if ($key === null) {
            return Session::getInstance();
        }
        return Session::get($key, $default);
    }
}

if (!function_exists('old')) {
    /**
     * Retrieve old input values from session for form autofill.
     */
    function old(string $key, $default = '') {
        $old = Session::getFlash('old_inputs');
        // Put it back in flash so it persists if needed, or check request flash
        return $old[$key] ?? $default;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get the current CSRF token, generating one if not exists.
     */
    function csrf_token(): string {
        return Session::getCsrfToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Output a hidden input field with the CSRF token.
     */
    function csrf_field(): string {
        return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
    }
}

if (!function_exists('auth')) {
    /**
     * Get the currently authenticated user.
     */
    function auth() {
        return Session::get('user');
    }
}

if (!function_exists('has_permission')) {
    /**
     * Check if the authenticated user has a specific permission.
     */
    function has_permission(string $permission): bool {
        $user = auth();
        if (!$user) {
            return false;
        }
        
        // Super Administrator has all permissions
        if (($user['role_name'] ?? '') === 'Super Administrator') {
            return true;
        }

        $permissions = Session::get('user_permissions', []);
        return in_array($permission, $permissions);
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if the authenticated user has a specific role.
     */
    function has_role(string|array $roles): bool {
        $user = auth();
        if (!$user) {
            return false;
        }
        
        $userRole = $user['role_name'] ?? '';
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        return $userRole === $roles;
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect the user to a specific URI and stop execution.
     */
    function redirect(string $uri): void {
        header('Location: ' . $uri);
        exit;
    }
}

if (!function_exists('route')) {
    /**
     * Helper to prefix URIs with the base URL.
     */
    function route(string $path = ''): string {
        $base = $_SERVER['BASE_URL'] ?? '/';
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die.
     */
    function dd(...$vars): void {
        echo '<pre style="background: #111; color: #0f0; padding: 15px; border-radius: 5px; font-family: monospace;">';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit;
    }
}
