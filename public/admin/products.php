<?php
require __DIR__ . '/../../app/config/config.php';
require_once HELPERS_PATH . '/auth.php';
require_once HELPERS_PATH . '/image.php';
require_once CONTROLLERS_PATH . '/ProductController.php';
require_once CONTROLLERS_PATH . '/CategoryController.php';

requireAdmin();

$productController = new ProductController();
$categoryController = new CategoryController();
$categories = $categoryController->getCategories();

$feedbackMessage = null;
$feedbackType = 'success';
$editingProductId = null;
$formMode = 'create';
$formValues = [
	'category_id' => '',
	'name' => '',
	'slug' => '',
	'manufacturer' => '',
	'image' => '',
	'short_description' => '',
	'description' => '',
	'price' => '',
	'quantity' => '0',
	'is_available' => '1',
	'sku' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
	$productId = (int) ($_POST['product_id'] ?? 0);
	$result = $productController->deleteProduct($productId);

	if ($result['success']) {
		$feedbackMessage = 'Товар успішно видалено.';
	} else {
		$feedbackMessage = $result['error'] ?? 'Не вдалося видалити товар.';
		$feedbackType = 'error';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_product'])) {
	$formValues = [
		'category_id' => (string) ($_POST['category_id'] ?? ''),
		'name' => trim((string) ($_POST['name'] ?? '')),
		'slug' => trim((string) ($_POST['slug'] ?? '')),
		'manufacturer' => trim((string) ($_POST['manufacturer'] ?? '')),
		'image' => trim((string) ($_POST['image'] ?? '')),
		'short_description' => trim((string) ($_POST['short_description'] ?? '')),
		'description' => trim((string) ($_POST['description'] ?? '')),
		'price' => (string) ($_POST['price'] ?? ''),
		'quantity' => (string) ($_POST['quantity'] ?? '0'),
		'is_available' => isset($_POST['is_available']) ? '1' : '0',
		'sku' => trim((string) ($_POST['sku'] ?? '')),
	];

	$result = $productController->createProduct($_POST);

	if ($result['success']) {
		$feedbackMessage = 'Товар успішно додано.';
		$formValues = [
			'category_id' => '',
			'name' => '',
			'slug' => '',
			'manufacturer' => '',
			'image' => '',
			'short_description' => '',
			'description' => '',
			'price' => '',
			'quantity' => '0',
			'is_available' => '1',
			'sku' => '',
		];
	} else {
		$feedbackMessage = $result['error'] ?? 'Не вдалося додати товар.';
		$feedbackType = 'error';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
	$productId = (int) ($_POST['product_id'] ?? 0);
	$formMode = 'edit';
	$editingProductId = $productId;
	$formValues = [
		'category_id' => (string) ($_POST['category_id'] ?? ''),
		'name' => trim((string) ($_POST['name'] ?? '')),
		'slug' => trim((string) ($_POST['slug'] ?? '')),
		'manufacturer' => trim((string) ($_POST['manufacturer'] ?? '')),
		'image' => trim((string) ($_POST['image'] ?? '')),
		'short_description' => trim((string) ($_POST['short_description'] ?? '')),
		'description' => trim((string) ($_POST['description'] ?? '')),
		'price' => (string) ($_POST['price'] ?? ''),
		'quantity' => (string) ($_POST['quantity'] ?? '0'),
		'is_available' => isset($_POST['is_available']) ? '1' : '0',
		'sku' => trim((string) ($_POST['sku'] ?? '')),
	];

	$result = $productController->updateProduct($productId, $_POST);

	if ($result['success']) {
		$feedbackMessage = 'Товар успішно оновлено.';
		$formMode = 'create';
		$editingProductId = null;
		$formValues = [
			'category_id' => '',
			'name' => '',
			'slug' => '',
			'manufacturer' => '',
			'image' => '',
			'short_description' => '',
			'description' => '',
			'price' => '',
			'quantity' => '0',
			'is_available' => '1',
			'sku' => '',
		];
	} else {
		$feedbackMessage = $result['error'] ?? 'Не вдалося оновити товар.';
		$feedbackType = 'error';
	}
}

if ($formMode === 'create' && isset($_GET['edit'])) {
	$productId = (int) $_GET['edit'];
	$product = $productController->getProductById($productId);

	if ($product) {
		$formMode = 'edit';
		$editingProductId = (int) $product['id'];
		$formValues = [
			'category_id' => (string) ($product['category_id'] ?? ''),
			'name' => (string) $product['name'],
			'slug' => (string) $product['slug'],
			'manufacturer' => (string) $product['manufacturer'],
			'image' => (string) ($product['image'] ?? ''),
			'short_description' => (string) ($product['short_description'] ?? ''),
			'description' => (string) ($product['description'] ?? ''),
			'price' => (string) ($product['price'] ?? ''),
			'quantity' => (string) ($product['quantity'] ?? '0'),
			'is_available' => !empty($product['is_available']) ? '1' : '0',
			'sku' => (string) ($product['sku'] ?? ''),
		];
	} else {
		$feedbackMessage = 'Товар не знайдено.';
		$feedbackType = 'error';
	}
}

$products = $productController->getProducts();
$imageMaxLength = PRODUCT_IMAGE_MAX_LENGTH;

require VIEWS_PATH . '/layouts/header.php';
?>
<section class="admin-product-page">
	<div class="admin-product-page__header">
		<div>
			<h1>Керування товарами</h1>
			<p class="admin-product-page__text">
				Тут можна додавати нові товари, редагувати їхній вміст і видаляти непотрібні позиції з каталогу.
			</p>
		</div>
	</div>

	<?php if ($feedbackMessage !== null): ?>
		<p class="admin-product-page__message admin-product-page__message--<?= htmlspecialchars($feedbackType, ENT_QUOTES, 'UTF-8'); ?>">
			<?= htmlspecialchars($feedbackMessage, ENT_QUOTES, 'UTF-8'); ?>
		</p>
	<?php endif; ?>

	<?php $imagePreview = resolveImageSource($formValues['image']); ?>

	<div class="admin-product-form-card">
		<h2><?= $formMode === 'edit' ? 'Редагування товару' : 'Додавання товару'; ?></h2>
		<form method="post" class="admin-product-form">
			<?php if ($formMode === 'edit' && $editingProductId !== null): ?>
				<input type="hidden" name="product_id" value="<?= $editingProductId; ?>">
			<?php endif; ?>

			<div class="admin-product-form__grid">
				<div class="admin-product-form__field">
					<label for="product-category">Категорія</label>
					<select id="product-category" name="category_id">
						<option value="">Без категорії</option>
						<?php foreach ($categories as $category): ?>
							<option value="<?= (int) $category['id']; ?>" <?= $formValues['category_id'] === (string) $category['id'] ? 'selected' : ''; ?>>
								<?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8'); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="admin-product-form__field">
					<label for="product-name">Назва</label>
					<input id="product-name" type="text" name="name" value="<?= htmlspecialchars($formValues['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
				</div>

				<div class="admin-product-form__field">
					<label for="product-slug">Slug</label>
					<input id="product-slug" type="text" name="slug" value="<?= htmlspecialchars($formValues['slug'], ENT_QUOTES, 'UTF-8'); ?>">
				</div>

				<div class="admin-product-form__field">
					<label for="product-manufacturer">Виробник</label>
					<input id="product-manufacturer" type="text" name="manufacturer" value="<?= htmlspecialchars($formValues['manufacturer'], ENT_QUOTES, 'UTF-8'); ?>" required>
				</div>

				<div class="admin-product-form__field admin-product-form__field--full">
					<label for="product-image">URL зображення</label>
					<input id="product-image" type="text" name="image" value="<?= htmlspecialchars($formValues['image'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="<?= $imageMaxLength; ?>" data-max-length="<?= $imageMaxLength; ?>" aria-describedby="product-image-hint product-image-counter product-image-error">
					<p id="product-image-hint" class="admin-product-form__hint">Використовуйте повний URL, наприклад https://example.com/image.jpg. Якщо вкажете адресу без https://, вона буде додана автоматично.</p>
					<p id="product-image-counter" class="admin-product-form__counter" aria-live="polite">0 / <?= $imageMaxLength; ?> символів</p>
					<p id="product-image-error" class="admin-product-form__counter admin-product-form__counter--error" aria-live="polite"></p>
					<?php if ($imagePreview !== ''): ?>
						<div class="admin-product-form__image-preview">
							<img src="<?= htmlspecialchars($imagePreview, ENT_QUOTES, 'UTF-8'); ?>" alt="Попередній перегляд зображення" class="admin-product-form__image-preview-img">
						</div>
					<?php endif; ?>
				</div>

				<div class="admin-product-form__field admin-product-form__field--full">
					<label for="product-short-description">Короткий опис</label>
					<textarea id="product-short-description" name="short_description" rows="3"><?= htmlspecialchars($formValues['short_description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>

				<div class="admin-product-form__field admin-product-form__field--full">
					<label for="product-description">Повний опис</label>
					<textarea id="product-description" name="description" rows="6"><?= htmlspecialchars($formValues['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>

				<div class="admin-product-form__field">
					<label for="product-price">Ціна</label>
					<input id="product-price" type="number" step="0.01" min="0" name="price" value="<?= htmlspecialchars($formValues['price'], ENT_QUOTES, 'UTF-8'); ?>">
				</div>

				<div class="admin-product-form__field">
					<label for="product-quantity">Кількість</label>
					<input id="product-quantity" type="number" min="0" name="quantity" value="<?= htmlspecialchars($formValues['quantity'], ENT_QUOTES, 'UTF-8'); ?>">
				</div>

				<div class="admin-product-form__field">
					<label for="product-sku">Артикул</label>
					<input id="product-sku" type="text" name="sku" value="<?= htmlspecialchars($formValues['sku'], ENT_QUOTES, 'UTF-8'); ?>">
				</div>

				<div class="admin-product-form__field admin-product-form__field--checkbox">
					<label>
						<input type="checkbox" name="is_available" value="1" <?= $formValues['is_available'] === '1' ? 'checked' : ''; ?>>
						Товар доступний для відображення
					</label>
				</div>
			</div>

			<div class="admin-product-form__actions">
				<?php if ($formMode === 'edit'): ?>
					<button type="submit" name="update_product" class="admin-product-form__submit">Зберегти зміни</button>
					<a href="/admin/products.php" class="admin-product-form__cancel">Скасувати</a>
				<?php else: ?>
					<button type="submit" name="create_product" class="admin-product-form__submit">Додати товар</button>
				<?php endif; ?>
			</div>
		</form>
	</div>

	<div class="admin-product-list">
		<div class="admin-product-list__header">
			<h2>Усі товари</h2>
			<p>Редагуйте або видаляйте товари прямо зі списку карток.</p>
		</div>

        <div class="admin-page-search" data-admin-search>
            <form class="admin-page-search__form" data-admin-search-form>
                <input
                    id="admin-products-search"
                    type="search"
                    class="admin-page-search__input"
                    placeholder="Пошук за назвою, категорією, виробником, ціною або артикулом"
                    aria-label="Пошук товарів"
                    data-admin-search-input
                >
                <button type="submit" class="admin-page-search__button">Знайти</button>
            </form>
            <p class="admin-page-search__empty admin-page-search__empty--hidden" data-admin-search-empty>
                За вашим запитом товарів не знайдено.
            </p>
        </div>

		<?php if ($products): ?>
			<div class="admin-product-list__grid">
				<?php foreach ($products as $product): ?>
					<?php $cardImage = resolveImageSource($product['image'] ?? ''); ?>
					<article class="admin-product-card" data-admin-search-item>
						<div class="admin-product-card__image-wrap">
							<?php if ($cardImage !== ''): ?>
								<img src="<?= htmlspecialchars($cardImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?>" class="admin-product-card__image">
							<?php else: ?>
								<div class="admin-product-card__placeholder">Без зображення</div>
							<?php endif; ?>
						</div>

						<div class="admin-product-card__content">
							<p class="admin-product-card__category"><?= htmlspecialchars((string) ($product['category_name'] ?? 'Без категорії'), ENT_QUOTES, 'UTF-8'); ?></p>
							<h3><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
							<p class="admin-product-card__meta">Виробник: <?= htmlspecialchars((string) $product['manufacturer'], ENT_QUOTES, 'UTF-8'); ?></p>
							<p class="admin-product-card__meta">Ціна: <?= number_format((float) $product['price'], 2, '.', ' '); ?> грн</p>
							<p class="admin-product-card__meta">Кількість: <?= (int) $product['quantity']; ?> шт.</p>
							<p class="admin-product-card__status <?= !empty($product['is_available']) ? 'admin-product-card__status--active' : 'admin-product-card__status--inactive'; ?>">
								<?= !empty($product['is_available']) ? 'Відображається в каталозі' : 'Прихований з каталогу'; ?>
							</p>
						</div>

						<div class="admin-product-card__actions">
							<a href="/admin/products.php?edit=<?= (int) $product['id']; ?>" class="admin-product-card__action admin-product-card__action--edit">Редагувати</a>
							<form method="post" class="admin-product-card__action-form">
								<input type="hidden" name="product_id" value="<?= (int) $product['id']; ?>">
								<button type="submit" name="delete_product" class="admin-product-card__action admin-product-card__action--delete" onclick="return confirm('Ви дійсно хочете видалити цей товар?');">Видалити</button>
							</form>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<p class="admin-product-list__empty">Товарів поки не знайдено.</p>
		<?php endif; ?>
	</div>

	<?php if ($formMode !== 'create'): ?>
		<div class="admin-product-page__footer">
			<a href="/admin/products.php" class="admin-product-page__add-button">Додати новий товар</a>
		</div>
	<?php endif; ?>
</section>
<script src="/assets/js/admin-search.js" defer></script>
<script src="/assets/js/admin-product-form.js" defer></script>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>