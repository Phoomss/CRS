<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class SettingRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get value of a specific setting key.
     */
    public function get(string $key): ?string {
        $stmt = $this->db->prepare("SELECT value FROM settings WHERE `key` = :key");
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetch();
        return $result ? $result['value'] : null;
    }

    /**
     * Set a setting value (insert or update).
     */
    public function set(string $key, ?string $value): bool {
        $stmt = $this->db->prepare("
            INSERT INTO settings (`key`, `value`) 
            VALUES (:key, :value) 
            ON DUPLICATE KEY UPDATE `value` = :value_update, `updated_at` = NOW()
        ");
        return $stmt->execute([
            'key' => $key,
            'value' => $value,
            'value_update' => $value
        ]);
    }

    /**
     * Retrieve all settings as a key-value associative array.
     */
    public function all(): array {
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings");
        $results = $stmt->fetchAll();
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }
}
