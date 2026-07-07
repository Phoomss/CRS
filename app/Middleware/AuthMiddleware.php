<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Core\Response;

class AuthMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        if (!Session::get('user')) {
            Session::setFlash('error', 'You must log in to access this page.');
            
            if ($request->isAjax()) {
                Response::json(['error' => 'Unauthenticated.'], 401);
            } else {
                Response::redirect('/login');
            }
            return false;
        }
        return true;
    }
}
