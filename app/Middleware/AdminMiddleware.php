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

        $path = trim($request->getPath(), '/');
        
        // Map request paths to their required RBAC permissions
        $requiredPermission = null;
        if (str_starts_with($path, 'computers')) {
            $requiredPermission = 'manage_computers';
        } elseif (str_starts_with($path, 'laboratories')) {
            $requiredPermission = 'manage_laboratories';
        } elseif (str_starts_with($path, 'users')) {
            $requiredPermission = 'manage_users';
        } elseif (str_starts_with($path, 'reports')) {
            $requiredPermission = 'view_reports';
        } elseif (str_starts_with($path, 'settings')) {
            $requiredPermission = 'manage_settings';
        } elseif (str_starts_with($path, 'reservations/approve') || str_starts_with($path, 'reservations/reject')) {
            $requiredPermission = 'manage_reservations';
        }

        // If this route requires a specific permission, validate against the user's permissions array
        if ($requiredPermission) {
            $permissions = Session::get('user_permissions', []);
            if (!in_array($requiredPermission, $permissions)) {
                Session::setFlash('error', 'Unauthorized access. You do not have permission to view this page.');
                
                if ($request->isAjax()) {
                    Response::json(['error' => 'Forbidden.'], 403);
                } else {
                    Response::redirect('/dashboard');
                }
                return false;
            }
        } else {
            // Fallback for safety: if no explicit permission mapping but route is guarded by AdminMiddleware,
            // restrict it to administrators only.
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
        }

        return true;
    }
}
