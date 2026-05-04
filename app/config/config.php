<?php
define('BASE_PATH', dirname(__DIR__, 2));
define('VIEWS_PATH', BASE_PATH . '/app/views');
define('CONTROLLERS_PATH', BASE_PATH . '/app/controllers');
define('MODELS_PATH', BASE_PATH . '/app/models');
define('HELPERS_PATH', BASE_PATH . '/app/helpers');
define('PRODUCT_IMAGE_MAX_LENGTH', 2048);

 if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

