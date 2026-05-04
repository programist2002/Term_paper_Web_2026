<?php
require __DIR__ . '/../app/config/config.php';
require_once CONTROLLERS_PATH . '/ProductController.php';
require_once HELPERS_PATH . '/image.php';
require_once HELPERS_PATH . '/auth.php';

requireLogin();
$productController = new ProductController();
$productSlug = trim((string) ($_GET['slug'] ?? ''));
$product = $productSlug !== '' ? $productController->getProductBySlug($productSlug) : null;
$imageSource = $product ? resolveImageSource($product['image'] ?? '') : '';

require VIEWS_PATH . '/layouts/header.php';
?>
<section class="product-page">
	<?php if (!$product): ?>
		<div class="product-page__empty">
			<h1>Сторінка недоступна</h1>
			<p>Схоже, що такого товару не існує або його slug вказано неправильно.</p>
			<a href="/catalog.php" class="product-page__back-link">Повернутися до каталогу</a>
		</div>
	<?php else: ?>
		<div class="product-page__layout">
			<div class="product-page__media">
				<?php if ($imageSource !== ''): ?>
					<img src="<?= htmlspecialchars($imageSource, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?>" class="product-page__image">
				<?php else: ?>
					<div class="product-page__placeholder">Без зображення</div>
				<?php endif; ?>
			</div>

			<div class="product-page__content">
				<p class="product-page__eyebrow"><?= htmlspecialchars((string) ($product['category_name'] ?? 'Без категорії'), ENT_QUOTES, 'UTF-8'); ?></p>
				<h1><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
				<p class="product-page__price"><?= number_format((float) $product['price'], 2, '.', ' '); ?> грн</p>

				<div class="product-page__details">
					<div class="product-page__detail">
						<span>Виробник</span>
						<strong><?= htmlspecialchars((string) ($product['manufacturer'] ?: 'Не вказано'), ENT_QUOTES, 'UTF-8'); ?></strong>
					</div>
					<div class="product-page__detail">
						<span>Артикул</span>
						<strong><?= htmlspecialchars((string) ($product['sku'] ?: 'Не вказано'), ENT_QUOTES, 'UTF-8'); ?></strong>
					</div>
					<div class="product-page__detail">
						<span>Наявність</span>
						<strong><?= !empty($product['is_available']) && (int) $product['quantity'] > 0 ? 'Є в наявності' : 'Немає в наявності'; ?></strong>
					</div>
					<div class="product-page__detail">
						<span>Кількість</span>
						<strong><?= (int) $product['quantity']; ?> шт.</strong>
					</div>
				</div>

				<?php if (!empty($product['short_description'])): ?>
					<div class="product-page__section">
						<h2>Короткий опис</h2>
						<p><?= nl2br(htmlspecialchars((string) $product['short_description'], ENT_QUOTES, 'UTF-8')); ?></p>
					</div>
				<?php endif; ?>

				<?php if (!empty($product['description'])): ?>
					<div class="product-page__section">
						<h2>Опис товару</h2>
						<p><?= nl2br(htmlspecialchars((string) $product['description'], ENT_QUOTES, 'UTF-8')); ?></p>
					</div>
				<?php endif; ?>

				<a href="/catalog.php" class="product-page__back-link">Повернутися до каталогу</a>
			</div>
		</div>
	<?php endif; ?>
</section>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>