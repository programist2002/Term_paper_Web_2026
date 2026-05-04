<?php
require_once __DIR__ . '/../app/config/config.php';
require_once HELPERS_PATH . '/database.php';

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit('This script can only be run from the CLI.' . PHP_EOL);
}

$pdo = getDatabaseConnection();

$seedPath = __DIR__ . '/seed.sql';
$sql = file_get_contents($seedPath);

if ($sql === false) {
	throw new RuntimeException('Не вдалося прочитати файл seed.sql');
}

$replacements = [
	'{{ADMIN_PASSWORD_HASH}}' => $pdo->quote(password_hash('Admin123!', PASSWORD_DEFAULT)),
	'{{USER_IRYNA_PASSWORD_HASH}}' => $pdo->quote(password_hash('User123!', PASSWORD_DEFAULT)),
	'{{USER_OLEH_PASSWORD_HASH}}' => $pdo->quote(password_hash('User123!', PASSWORD_DEFAULT)),
];

$sql = strtr($sql, $replacements);

try {
	$pdo->exec($sql);

	echo 'Database seeded successfully.' . PHP_EOL;
	echo 'Users inserted: 3' . PHP_EOL;
	echo 'Categories inserted: 4' . PHP_EOL;
	echo 'Products inserted: 8' . PHP_EOL;
	echo PHP_EOL;
	echo 'Test accounts:' . PHP_EOL;
	echo '- admin@toyshop.local / Admin123!' . PHP_EOL;
	echo '- iryna@toyshop.local / User123!' . PHP_EOL;
	echo '- oleh@toyshop.local / User123!' . PHP_EOL;
} catch (Throwable $exception) {
	try {
		$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
	} catch (Throwable $restoreException) {
	}

	fwrite(STDERR, 'Seeding failed: ' . $exception->getMessage() . PHP_EOL);
	exit(1);
}