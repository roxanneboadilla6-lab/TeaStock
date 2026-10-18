<?php
declare(strict_types=1);

$host = 'localhost';
$dbname = 'inventory_system';
$username = 'root';
$password = ''; // Default XAMPP MySQL password is usually blank. Update if yours is different.
$charset = 'utf8mb4';

$dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Check that MySQL is running and config/database.php credentials are correct.');
}
