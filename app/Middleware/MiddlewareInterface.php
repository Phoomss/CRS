<?php

namespace App\Middleware;

use App\Core\Request;

interface MiddlewareInterface {
    /**
     * Intercept and process the HTTP request.
     * Returns true if processing should continue, or false to halt and handle response.
     */
    public function handle(Request $request): bool;
}
