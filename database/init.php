<?php
require_once __DIR__ . '/../app/config/config.php';
require_once HELPERS_PATH . '/database.php';

$serverConnection = getDatabaseConnection(false);
$serverConnection->exec(
	'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET ' . DB_CHARSET . ' COLLATE ' . DB_CHARSET . '_unicode_ci'
);

$databaseConnection = getDatabaseConnection();
$schemaPath = __DIR__ . '/schema.sql';
$sql = file_get_contents($schemaPath);

if ($sql === false) {
	throw new RuntimeException('Не вдалося прочитати файл schema.sql');
}

$databaseConnection->exec($sql);
$databaseConnection->exec('ALTER TABLE products MODIFY image VARCHAR(2048) NULL');

echo 'Database initialized successfully.' . PHP_EOL;