<?php

$host = '127.0.0.1';
$dbName = 'sports_tournament';
$dbUser = 'root';
$dbPass = '';
$charset = 'utf8mb4';

function getDb(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $host = '127.0.0.1';
    $dbName = 'sports_tournament';
    $dbUser = 'root';
    $dbPass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host={$host};dbname={$dbName};charset={$charset}";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
        return $pdo;
    } catch (PDOException $e) {
        throw new PDOException('Database connection failed. Please import the database schema first.');
    }
}
