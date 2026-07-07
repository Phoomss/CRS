<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Core\Response;

class CSRFMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        // Only validate CSRF on POST, PUT, PATCH, DELETE requests
        $method = $request->getMethod();
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $token = $request->get('csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            
            if (!Session::validateCsrfToken($token)) {
                Response::setStatusCode(419);
                if ($request->isAjax()) {
                    Response::json(['error' => 'CSRF token mismatch. Please reload the page.'], 419);
                } else {
                    echo "<h1 style='text-align:center; margin-top:50px;'>419 Page Expired</h1><p style='text-align:center;'>CSRF Token verification failed. Please go back, refresh, and try again.</p>";
                }
                return false;
            }
        }
        return true;
    }
}
