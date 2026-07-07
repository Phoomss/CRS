<?php

namespace App\Core;

class Controller {
    /**
     * Render a view file inside a layout.
     */
    protected function render(string $view, array $data = [], string $layout = 'main'): void {
        // Extract variables to be available in view and layout
        extract($data);

        // Start output buffering for the view content
        ob_start();
        $viewFile = __DIR__ . '/../../views/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "View file [{$view}] not found.";
        }
        $content = ob_get_clean();

        // Include the layout file
        $layoutFile = __DIR__ . '/../../views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content; // If layout not found, output content directly
        }
    }

    /**
     * Helper to output JSON data.
     */
    protected function json(mixed $data, int $statusCode = 200): void {
        Response::json($data, $statusCode);
    }

    /**
     * Helper to redirect.
     */
    protected function redirect(string $url): void {
        Response::redirect($url);
    }

    /**
     * Run validation rules on input data.
     * Returns array of errors if validation fails, or empty array if passes.
     */
    protected function validate(array $data, array $rules): array {
        $errors = [];
        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? null;
            $ruleArray = is_string($ruleset) ? explode('|', $ruleset) : $ruleset;

            foreach ($ruleArray as $rule) {
                if ($rule === 'required') {
                    if ($value === null || $value === '') {
                        $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                        break; // Skip other checks if missing
                    }
                }
                
                if ($value !== null && $value !== '') {
                    if ($rule === 'email') {
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = 'Please enter a valid email address.';
                        }
                    }
                    
                    if (str_starts_with($rule, 'min:')) {
                        $min = (int)substr($rule, 4);
                        if (strlen((string)$value) < $min) {
                            $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.";
                        }
                    }

                    if (str_starts_with($rule, 'max:')) {
                        $max = (int)substr($rule, 4);
                        if (strlen((string)$value) > $max) {
                            $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$max} characters.";
                        }
                    }

                    if ($rule === 'numeric') {
                        if (!is_numeric($value)) {
                            $errors[$field][] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number.';
                        }
                    }
                }
            }
        }
        return $errors;
    }
}
