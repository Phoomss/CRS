<?php
/**
 * CRMS Database Setup Script
 * Sets up database, schema, and seed data.
 */

echo "Starting CRMS Database Setup...\n";

// Simple manual .env parser
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_contains($line, '=') && !str_starts_with(trim($line), '#')) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim(trim($value), '"\'');
        }
    }
    echo "Loaded .env file successfully.\n";
} else {
    echo "Warning: .env file not found. Using defaults.\n";
}

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$dbName = $_ENV['DB_DATABASE'] ?? 'computer_booking';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';

try {
    // Connect to MySQL server without database first
    echo "Connecting to MySQL server at {$host}:{$port}...\n";
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create database if not exists
    echo "Creating database '{$dbName}' (if it doesn't exist)...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database created or verified.\n";

    // Re-connect to specific database
    $pdo->exec("USE `{$dbName}`");

    // Load Schema
    $schemaFile = __DIR__ . '/schema.sql';
    if (file_exists($schemaFile)) {
        echo "Executing schema migrations from schema.sql...\n";
        $schemaSql = file_get_contents($schemaFile);
        $pdo->exec($schemaSql);
        echo "Database schema migrated successfully.\n";
    } else {
        throw new Exception("schema.sql not found at {$schemaFile}");
    }

    // Load Seed Data
    $seedFile = __DIR__ . '/seed.sql';
    if (file_exists($seedFile)) {
        echo "Seeding database with default values from seed.sql...\n";
        $seedSql = file_get_contents($seedFile);
        $pdo->exec($seedSql);
        echo "Database seeded successfully.\n";
    } else {
        echo "Warning: seed.sql not found. Database seeded skipped.\n";
    }

    echo "CRMS Database setup complete! System is ready.\n";

} catch (Exception $e) {
    echo "\nError during database setup:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
