<?php
require_once __DIR__ . '/../../app/config/config.php';
require_once HELPERS_PATH . '/auth.php';

requireAdmin();
require VIEWS_PATH . '/layouts/header.php';
?>
<section class="admin-panel">
	<div class="admin-panel__intro">
		<h2>Панель адміністратора</h2>
		<p>Оберіть розділ, з яким потрібно працювати: керування користувачами, товарами, категоріями або додавання нових позицій.</p>
	</div>

	<div class="admin-panel__grid">
		<a href="../admin/users.php" class="admin-card">
			<span class="admin-card__title">Користувачі</span>
			<span class="admin-card__text">Перегляд і керування обліковими записами користувачів.</span>
		</a>
		<a href="../admin/products.php" class="admin-card">
			<span class="admin-card__title">Товари</span>
			<span class="admin-card__text">Редагування та додавання товарів, описів і їх наявності.</span>
		</a>
		<a href="../admin/categories.php" class="admin-card">
			<span class="admin-card__title">Категорії</span>
			<span class="admin-card__text">Створення та впорядкування категорій каталогу.</span>
		</a>
	</div>
</section>
<?php 
require_once VIEWS_PATH . '/layouts/footer.php'; 
?>