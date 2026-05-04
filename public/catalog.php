<?php
require __DIR__ . '/../app/config/config.php';
require_once CONTROLLERS_PATH . '/ProductController.php';
require_once CONTROLLERS_PATH . '/CategoryController.php';
require_once HELPERS_PATH . '/auth.php';

requireLogin();

$productController = new ProductController();
$categoryController = new CategoryController();

$selectedCategory = null;
$categorySlug = trim((string) ($_GET['category'] ?? ''));

if ($categorySlug !== '') {
	$selectedCategory = $categoryController->getCategoryBySlug($categorySlug);
	$products = $selectedCategory
		? $productController->getProductsByCategory((int) $selectedCategory['id'])
		: [];
} else {
	$products = $productController->getAvailableProducts();
}

$productGridClass = 'catalog-page__grid';
$emptyMessage = $selectedCategory ? 'У цій категорії поки немає товарів.' : 'Товари поки що не додані.';
$showCategory = true;

require VIEWS_PATH . '/layouts/header.php';
?>
<section class="catalog-page">
	<div class="catalog-page__intro">
		<h1><?= $selectedCategory ? htmlspecialchars((string) $selectedCategory['name'], ENT_QUOTES, 'UTF-8') : 'Усі товари'; ?></h1>
		<p>
			<?= $selectedCategory
				? htmlspecialchars((string) ($selectedCategory['description'] ?: 'Товари обраної категорії.'), ENT_QUOTES, 'UTF-8')
				: 'Перегляньте товари, які зараз доступні в каталозі.'; ?>
		</p>
		<?php if ($selectedCategory): ?>
			<a href="/categories.php" class="catalog-page__back-link">Повернутися до всіх категорій</a>
		<?php endif; ?>
	</div>

	<?php if ($products): ?>
		<div class="catalog-page__search">
			<form class="catalog-page__search-form" data-catalog-search>
				<input
					id="catalog-search"
					type="search"
					class="catalog-page__search-input"
					placeholder="Введіть назву або категорію товару"
					autocomplete="off"
				>
				<button type="submit" class="catalog-page__search-button">Пошук</button>
			</form>
		</div>
		<p id="catalog-search-empty" class="catalog-page__empty catalog-page__empty--hidden">За вашим запитом товарів не знайдено.</p>
	<?php endif; ?>

	<?php require VIEWS_PATH . '/partials/product-grid.php'; ?>
</section>
<?php if ($products): ?>
	<script src="/assets/js/catalog-search.js" defer></script>
<?php endif; ?>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>