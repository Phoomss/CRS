<?php

/**
 * Computer Reservation Management System
 * Front Controller & Routing Entry Point
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Start output buffer with the Thai translator callback
ob_start([\App\Helpers\Translator::class, 'translate']);

use App\Core\Request;
use App\Core\Router;
use App\Core\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\CSRFMiddleware;

// 1. Load Environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// 2. Start Secure Session Management
Session::start();

// 3. Configure Timezone based on settings or default
date_default_timezone_set($_ENV['timezone'] ?? 'Asia/Bangkok');

// 4. Initialize Core Request & Router
$request = new Request();
$router = new Router();

// 5. Register Routes & Middleware mapping
// --- Authentication ---
$router->get('login', [App\Controllers\AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('login', [App\Controllers\AuthController::class, 'login'], [GuestMiddleware::class, CSRFMiddleware::class]);
$router->get('logout', [App\Controllers\AuthController::class, 'logout'], [AuthMiddleware::class]);
$router->post('logout', [App\Controllers\AuthController::class, 'logout'], [AuthMiddleware::class, CSRFMiddleware::class]);
$router->get('forgot-password', [App\Controllers\AuthController::class, 'showForgot'], [GuestMiddleware::class]);
$router->post('forgot-password', [App\Controllers\AuthController::class, 'forgot'], [GuestMiddleware::class, CSRFMiddleware::class]);
$router->get('reset-password/{token}', [App\Controllers\AuthController::class, 'showReset'], [GuestMiddleware::class]);
$router->post('reset-password/{token}', [App\Controllers\AuthController::class, 'reset'], [GuestMiddleware::class, CSRFMiddleware::class]);

// --- Dashboard ---
$router->get('', [App\Controllers\DashboardController::class, 'index'], [AuthMiddleware::class]);
$router->get('dashboard', [App\Controllers\DashboardController::class, 'index'], [AuthMiddleware::class]);

// --- Reservations ---
$router->get('reservations', [App\Controllers\ReservationController::class, 'index'], [AuthMiddleware::class]);
$router->get('reservations/create', [App\Controllers\ReservationController::class, 'showCreate'], [AuthMiddleware::class]);
$router->get('reservations/available-computers', [App\Controllers\ReservationController::class, 'getAvailableComputers'], [AuthMiddleware::class]);
$router->post('reservations/store', [App\Controllers\ReservationController::class, 'store'], [AuthMiddleware::class, CSRFMiddleware::class]);
$router->get('reservations/view/{id}', [App\Controllers\ReservationController::class, 'view'], [AuthMiddleware::class]);
$router->post('reservations/approve/{id}', [App\Controllers\ReservationController::class, 'approve'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->post('reservations/reject/{id}', [App\Controllers\ReservationController::class, 'reject'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->post('reservations/cancel/{id}', [App\Controllers\ReservationController::class, 'cancel'], [AuthMiddleware::class, CSRFMiddleware::class]);
$router->post('reservations/checkin', [App\Controllers\ReservationController::class, 'checkIn'], [AuthMiddleware::class, CSRFMiddleware::class]);
$router->post('reservations/checkout', [App\Controllers\ReservationController::class, 'checkOut'], [AuthMiddleware::class, CSRFMiddleware::class]);
$router->get('reservations/calendar-data', [App\Controllers\ReservationController::class, 'calendarData'], [AuthMiddleware::class]);

// --- Computer Workstations ---
$router->get('computers', [App\Controllers\ComputerController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('computers/create', [App\Controllers\ComputerController::class, 'showCreate'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('computers/store', [App\Controllers\ComputerController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->get('computers/edit/{id}', [App\Controllers\ComputerController::class, 'showEdit'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('computers/update/{id}', [App\Controllers\ComputerController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->post('computers/delete/{id}', [App\Controllers\ComputerController::class, 'delete'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->get('computers/print', [App\Controllers\ComputerController::class, 'printCodes'], [AuthMiddleware::class, AdminMiddleware::class]);

// --- Laboratories ---
$router->get('laboratories', [App\Controllers\LaboratoryController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('laboratories/create', [App\Controllers\LaboratoryController::class, 'showCreate'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('laboratories/store', [App\Controllers\LaboratoryController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->get('laboratories/edit/{id}', [App\Controllers\LaboratoryController::class, 'showEdit'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('laboratories/update/{id}', [App\Controllers\LaboratoryController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->post('laboratories/delete/{id}', [App\Controllers\LaboratoryController::class, 'delete'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);

// --- User Management ---
$router->get('users', [App\Controllers\UserController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('users/create', [App\Controllers\UserController::class, 'showCreate'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('users/store', [App\Controllers\UserController::class, 'store'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->get('users/edit/{id}', [App\Controllers\UserController::class, 'showEdit'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('users/update/{id}', [App\Controllers\UserController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->post('users/delete/{id}', [App\Controllers\UserController::class, 'delete'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);

// --- Reports & Exports ---
$router->get('reports', [App\Controllers\ReportController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('reports/view/{type}', [App\Controllers\ReportController::class, 'view'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->get('reports/export/{type}', [App\Controllers\ReportController::class, 'export'], [AuthMiddleware::class, AdminMiddleware::class]);

// --- Settings ---
$router->get('settings', [App\Controllers\SettingController::class, 'index'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('settings/update', [App\Controllers\SettingController::class, 'update'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->get('settings/backup', [App\Controllers\SettingController::class, 'backup'], [AuthMiddleware::class, AdminMiddleware::class]);
$router->post('settings/restore', [App\Controllers\SettingController::class, 'restore'], [AuthMiddleware::class, AdminMiddleware::class, CSRFMiddleware::class]);
$router->post('settings/change-password', [App\Controllers\AuthController::class, 'changePassword'], [AuthMiddleware::class, CSRFMiddleware::class]);

// 6. Resolve Route
$router->resolve($request);
