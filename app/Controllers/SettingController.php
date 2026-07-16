<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Services\SettingService;
use App\Core\Database;
use Exception;
use PDO;

class SettingController extends Controller {
    private SettingService $settingService;

    public function __construct() {
        $this->settingService = new SettingService();
    }

    /**
     * Show general settings panel.
     */
    public function index(): void {
        $settings = $this->settingService->all();
        $this->render('settings.index', ['settings' => $settings]);
    }

    /**
     * Update configuration settings.
     */
    public function update(Request $request): void {
        $rules = [
            'dept_name'                 => 'required|max:150',
            'academic_year'             => 'required',
            'semester'                  => 'required',
            'max_reservation_hours'     => 'required|numeric',
            'max_reservations_per_user' => 'required|numeric',
            'check_in_expiry_minutes'   => 'required|numeric',
            'timezone'                  => 'required'
        ];

        $errors = $this->validate($request->all(), $rules);

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            $this->redirect('/settings');
        }

        $settings = [
            'dept_name'                  => $request->get('dept_name'),
            'academic_year'              => $request->get('academic_year'),
            'semester'                   => $request->get('semester'),
            'max_reservation_hours'      => $request->get('max_reservation_hours'),
            'max_reservations_per_user'  => $request->get('max_reservations_per_user'),
            'check_in_expiry_minutes'    => $request->get('check_in_expiry_minutes'),
            'notification_email_enabled' => $request->get('notification_email_enabled') === '1' ? '1' : '0',
            'smtp_host'                  => $request->get('smtp_host'),
            'smtp_port'                  => $request->get('smtp_port'),
            'smtp_user'                  => $request->get('smtp_user'),
            'smtp_pass'                  => $request->get('smtp_pass'),
            'smtp_encryption'            => $request->get('smtp_encryption'),
            'smtp_from_email'            => $request->get('smtp_from_email'),
            'smtp_from_name'             => $request->get('smtp_from_name'),
            'timezone'                   => $request->get('timezone')
        ];

        try {
            $this->settingService->updateMany($settings);
            Session::setFlash('success', 'Settings updated successfully.');
            $this->redirect('/settings');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            $this->redirect('/settings');
        }
    }

    /**
     * Download database backup. Programmatically dumps database schemas and rows.
     */
    public function backup(): void {
        try {
            $db = Database::getConnection();
            $tables = [];
            $result = $db->query("SHOW TABLES");
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            $sqlDump = "-- CRMS SQL Backup\n";
            $sqlDump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

            foreach ($tables as $table) {
                // Fetch create table script
                $resCreate = $db->query("SHOW CREATE TABLE `{$table}`")->fetch();
                $sqlDump .= $resCreate['Create Table'] . ";\n\n";

                // Fetch table rows
                $resRows = $db->query("SELECT * FROM `{$table}`");
                while ($row = $resRows->fetch(PDO::FETCH_ASSOC)) {
                    $keys = array_keys($row);
                    $escapedKeys = array_map(fn($k) => "`{$k}`", $keys);
                    
                    $escapedValues = [];
                    foreach ($row as $val) {
                        if ($val === null) {
                            $escapedValues[] = 'NULL';
                        } else {
                            $escapedValues[] = $db->quote($val);
                        }
                    }

                    $sqlDump .= "INSERT INTO `{$table}` (" . implode(', ', $escapedKeys) . ") VALUES (" . implode(', ', $escapedValues) . ");\n";
                }
                $sqlDump .= "\n";
            }
            $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

            $filename = "backup_crms_" . date('Ymd_His') . ".sql";
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $sqlDump;
            exit;

        } catch (Exception $e) {
            Session::setFlash('error', 'Backup failed: ' . $e->getMessage());
            $this->redirect('/settings');
        }
    }

    /**
     * Restore database from uploaded SQL file.
     */
    public function restore(Request $request): void {
        $file = $request->file('backup_file');

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Please select a valid SQL file.');
            $this->redirect('/settings');
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) !== 'sql') {
            Session::setFlash('error', 'Invalid file type. Only .sql files are allowed.');
            $this->redirect('/settings');
        }

        try {
            $sql = file_get_contents($file['tmp_name']);
            $db = Database::getConnection();
            
            // Execute restore statements
            $db->exec($sql);
            
            Session::setFlash('success', 'Database restored successfully! Please log in again.');
            
            // Force logout after database schema restore
            $auth = new AuthService();
            $auth->logout();
            
            $this->redirect('/login');

        } catch (Exception $e) {
            Session::setFlash('error', 'Restore failed: ' . $e->getMessage());
            $this->redirect('/settings');
        }
    }

    /**
     * Show personal security profile view.
     */
    public function profile(): void {
        $this->render('settings.profile');
    }
}
