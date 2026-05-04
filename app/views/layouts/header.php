<?php
require_once __DIR__ . '/../../config/config.php';
require_once HELPERS_PATH . '/auth.php';

$isLoggedIn = isAuthenticated();
$isAdmin = isAdmin();
$title = $isAdmin ? 'Бебі Бос' : 'Малюк';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Каталог дитячих іграшок</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header>
    <div class="header__logo-container">
        <img src="/assets/images/logo.png" alt="Children's toys logo" class="header__logo"/>
        <a href="index.php" class="header__title"><?= $title; ?></a>
    </div>
    <nav class="header__nav">
        <a href="/index.php" class="header__link">Головна</a>
        <a href="/contacts.php" class="header__link">Контакти</a>
        <?php if ($isLoggedIn): ?>
            <a href="/catalog.php" class="header__link">Каталог</a>
            <a href="/categories.php" class="header__link">Категорії</a>
            <?php if ($isAdmin): ?>
                <a href="/admin" class="header__link">Адмінпанель</a>
            <?php endif; ?>
            <a href="/logout.php" class="header__login">Вийти</a>
        <?php else: ?>
            <a href="/login.php" class="header__login">Увійти</a>
        <?php endif; ?>
    </nav>
</header>

<main>