<?php

namespace App\Core;

class Request {
    private array $params = [];

    public function __construct() {
        $this->parseParams();
    }

    /**
     * Get the HTTP request method (GET, POST, etc.).
     */
    public function getMethod(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get the sanitized request path.
     */
    public function getPath(): string {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        return '/' . trim($path, '/');
    }

    /**
     * Check if request is AJAX.
     */
    public function isAjax(): bool {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    /**
     * Parse and sanitize inputs.
     */
    private function parseParams(): void {
        // Parse Query String
        foreach ($_GET as $key => $value) {
            $this->params[$key] = $this->sanitize($value);
        }

        // Parse Post Body
        foreach ($_POST as $key => $value) {
            $this->params[$key] = $this->sanitize($value);
        }

        // Parse JSON Raw Input
        if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
            $json = json_decode(file_get_contents('php://input'), true);
            if (is_array($json)) {
                foreach ($json as $key => $value) {
                    $this->params[$key] = $this->sanitize($value);
                }
            }
        }
    }

    /**
     * Sanitize input value.
     */
    private function sanitize(mixed $value): mixed {
        if (is_array($value)) {
            return array_map([$this, 'sanitize'], $value);
        }
        if (is_string($value)) {
            return trim($value);
        }
        return $value;
    }

    /**
     * Get a specific input value or default.
     */
    public function get(string $key, $default = null) {
        return $this->params[$key] ?? $default;
    }

    /**
     * Get all inputs.
     */
    public function all(): array {
        return $this->params;
    }

    /**
     * Get request files.
     */
    public function file(string $key): ?array {
        return $_FILES[$key] ?? null;
    }
}
