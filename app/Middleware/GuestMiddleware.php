<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Core\Response;

class GuestMiddleware implements MiddlewareInterface {
    public function handle(Request $request): bool {
        if (Session::get('user')) {
            Response::redirect('/dashboard');
            return false;
        }
        return true;
    }
}
