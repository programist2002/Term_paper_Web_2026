<?php
require __DIR__ . '/../app/config/config.php';
require_once CONTROLLERS_PATH . '/CategoryController.php';
require_once HELPERS_PATH . '/auth.php';

requireLogin();

$categoryController = new CategoryController();
$categories = $categoryController->getCategories();

require VIEWS_PATH . '/layouts/header.php';
?>
<section class="categories-page">
	<div class="categories-page__intro">
		<h1>Оберіть категорію</h1>
		<p>
			Перейдіть до потрібної категорії, щоб переглянути доступні товари та швидко знайти те, що вас цікавить.
		</p>
	</div>

	<?php if ($categories): ?>
		<div class="categories-page__grid">
			<?php foreach ($categories as $category): ?>
				<a href="/catalog.php?category=<?= urlencode((string) $category['slug']); ?>" class="category-card">
					<h2><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
					<p><?= htmlspecialchars((string) ($category['description'] ?: 'Перегляньте товари цієї категорії.'), ENT_QUOTES, 'UTF-8'); ?></p>
					<span class="category-card__link">Переглянути товари</span>
				</a>
			<?php endforeach; ?>
		</div>
	<?php else: ?>
		<p class="categories-page__empty">Категорії поки що не додані.</p>
	<?php endif; ?>
</section>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>
