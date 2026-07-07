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

        // Dynamically translate error/success strings in AJAX API payloads
        if (is_array($data)) {
            if (isset($data['error']) && is_string($data['error'])) {
                $data['error'] = \App\Helpers\Translator::translateText($data['error']);
            }
            if (isset($data['message']) && is_string($data['message'])) {
                $data['message'] = \App\Helpers\Translator::translateText($data['message']);
            }
            if (isset($data['errors']) && is_array($data['errors'])) {
                foreach ($data['errors'] as $field => $fieldErrors) {
                    if (is_array($fieldErrors)) {
                        foreach ($fieldErrors as $k => $errMsg) {
                            $data['errors'][$field][$k] = \App\Helpers\Translator::translateText($errMsg);
                        }
                    } elseif (is_string($fieldErrors)) {
                        $data['errors'][$field] = \App\Helpers\Translator::translateText($fieldErrors);
                    }
                }
            }
        }

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
