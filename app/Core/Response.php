<?php

namespace App\Core;

class Response {
    /**
     * Set the HTTP status code.
     */
    public static function setStatusCode(int $code): void {
        http_response_code($code);
    }

    /**
     * Redirect to a specific URL.
     */
    public static function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Send a JSON response.
     */
    public static function json(mixed $data, int $statusCode = 200): void {
        self::setStatusCode($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
