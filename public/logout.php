<?php
require __DIR__ . '/../app/config/config.php';
require HELPERS_PATH . '/auth.php';

logout();

header('Location: /login.php');
exit;