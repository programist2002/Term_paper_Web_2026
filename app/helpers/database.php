<?php
require_once __DIR__ . '/../config/db.php';

function getDatabaseConnection(bool $withDatabase = true): PDO
{
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHARSET;

    if ($withDatabase) {
        $dsn .= ';dbname=' . DB_NAME;
    }

    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}