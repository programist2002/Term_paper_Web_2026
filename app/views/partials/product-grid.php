<?php
require_once HELPERS_PATH . '/image.php';

$products = $products ?? [];
$productGridClass = $productGridClass ?? 'catalog-page__grid';
$emptyMessage = $emptyMessage ?? 'Товари поки що не додані.';
$showCategory = $showCategory ?? true;
?>

<?php if ($products): ?>
	<div class="<?= htmlspecialchars($productGridClass, ENT_QUOTES, 'UTF-8'); ?>">
		<?php foreach ($products as $product): ?>
			<?php $imageSource = resolveImageSource($product['image'] ?? ''); ?>
			<article class="catalog-card">
				<a href="/product.php?slug=<?= urlencode((string) $product['slug']); ?>" class="catalog-card__link" aria-label="Переглянути товар <?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?>">
					<div class="catalog-card__image-wrap">
						<?php if ($imageSource !== ''): ?>
							<img src="<?= htmlspecialchars($imageSource, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?>" class="catalog-card__image">
						<?php else: ?>
							<div class="catalog-card__placeholder">Без зображення</div>
						<?php endif; ?>
					</div>
					<div class="catalog-card__content">
						<?php if ($showCategory): ?>
							<p class="catalog-card__category"><?= htmlspecialchars((string) ($product['category_name'] ?? 'Без категорії'), ENT_QUOTES, 'UTF-8'); ?></p>
						<?php endif; ?>
						<h2><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
					</div>
				</a>
			</article>
		<?php endforeach; ?>
	</div>
<?php else: ?>
	<p class="catalog-page__empty"><?= htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>