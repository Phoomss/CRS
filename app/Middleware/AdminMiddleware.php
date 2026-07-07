<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Core\Response;

class AdminMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        $user = Session::get('user');
        
        if (!$user) {
            Response::redirect('/login');
            return false;
        }

        $role = $user['role_name'] ?? '';
        if ($role !== 'Super Administrator' && $role !== 'Department Administrator') {
            Session::setFlash('error', 'Unauthorized access. You do not have permission to view this page.');
            
            if ($request->isAjax()) {
                Response::json(['error' => 'Forbidden.'], 403);
            } else {
                Response::redirect('/dashboard');
            }
            return false;
        }

        return true;
    }
}
