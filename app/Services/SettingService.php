<?php

namespace App\Services;

use App\Repositories\SettingRepository;

class SettingService {
    private SettingRepository $repository;
    private static ?array $cache = null;

    public function __construct() {
        $this->repository = new SettingRepository();
    }

    /**
     * Load settings cache.
     */
    private function loadCache(): void {
        if (self::$cache === null) {
            self::$cache = $this->repository->all();
        }
    }

    /**
     * Get setting value by key.
     */
    public function get(string $key, $default = null) {
        $this->loadCache();
        return self::$cache[$key] ?? $default;
    }

    /**
     * Get setting value as integer.
     */
    public function getInt(string $key, int $default = 0): int {
        return (int)$this->get($key, $default);
    }

    /**
     * Get setting value as boolean.
     */
    public function getBool(string $key, bool $default = false): bool {
        $val = $this->get($key);
        if ($val === null) {
            return $default;
        }
        return $val === '1' || $val === 'true' || $val === true;
    }

    /**
     * Set a setting value.
     */
    public function set(string $key, ?string $value): void {
        $this->repository->set($key, $value);
        if (self::$cache !== null) {
            self::$cache[$key] = $value;
        }
    }

    /**
     * Update multiple settings.
     */
    public function updateMany(array $settings): void {
        $logService = new ActivityLogService();
        $changes = [];
        
        foreach ($settings as $key => $value) {
            $oldValue = $this->get($key);
            if ($oldValue !== $value) {
                $this->set($key, $value);
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $value
                ];
            }
        }

        if (!empty($changes)) {
            $logService->log('update_settings', ['changes' => $changes]);
        }
    }

    /**
     * Get all settings.
     */
    public function all(): array {
        $this->loadCache();
        return self::$cache;
    }
}
