<?php
require __DIR__ . '/../../app/config/config.php';
require_once HELPERS_PATH . '/auth.php';
require_once CONTROLLERS_PATH . '/CategoryController.php';

requireAdmin();

$categoryController = new CategoryController();
$feedbackMessage = null;
$feedbackType = 'success';
$editingCategory = null;
$formMode = 'create';
$formValues = [
	'name' => '',
	'slug' => '',
	'description' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
	$categoryId = (int) ($_POST['category_id'] ?? 0);
	$result = $categoryController->deleteCategory($categoryId);

	if ($result['success']) {
		$feedbackMessage = 'Категорію успішно видалено.';
	} else {
		$feedbackMessage = $result['error'] ?? 'Не вдалося видалити категорію.';
		$feedbackType = 'error';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_category'])) {
	$formValues = [
		'name' => trim((string) ($_POST['name'] ?? '')),
		'slug' => trim((string) ($_POST['slug'] ?? '')),
		'description' => trim((string) ($_POST['description'] ?? '')),
	];
	$result = $categoryController->createCategory($_POST);

	if ($result['success']) {
		$feedbackMessage = 'Категорію успішно додано.';
		$formValues = [
			'name' => '',
			'slug' => '',
			'description' => '',
		];
	} else {
		$feedbackMessage = $result['error'] ?? 'Не вдалося додати категорію.';
		$feedbackType = 'error';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
	$categoryId = (int) ($_POST['category_id'] ?? 0);
	$formMode = 'edit';
	$editingCategory = $categoryController->getCategoryById($categoryId);
	$formValues = [
		'name' => trim((string) ($_POST['name'] ?? '')),
		'slug' => trim((string) ($_POST['slug'] ?? '')),
		'description' => trim((string) ($_POST['description'] ?? '')),
	];
	$result = $categoryController->updateCategory($categoryId, $_POST);

	if ($result['success']) {
		$feedbackMessage = 'Категорію успішно оновлено.';
		$formMode = 'create';
		$editingCategory = null;
		$formValues = [
			'name' => '',
			'slug' => '',
			'description' => '',
		];
	} else {
		$feedbackMessage = $result['error'] ?? 'Не вдалося оновити категорію.';
		$feedbackType = 'error';
	}
}

if ($formMode === 'create' && isset($_GET['edit'])) {
	$categoryId = (int) $_GET['edit'];
	$editingCategory = $categoryController->getCategoryById($categoryId);

	if ($editingCategory) {
		$formMode = 'edit';
		$formValues = [
			'name' => (string) $editingCategory['name'],
			'slug' => (string) $editingCategory['slug'],
			'description' => (string) ($editingCategory['description'] ?? ''),
		];
	} else {
		$feedbackMessage = 'Категорію не знайдено.';
		$feedbackType = 'error';
	}
}

$categories = $categoryController->getCategories();

require VIEWS_PATH . '/layouts/header.php';
?>
<section class="admin-categories-page">
	<div class="admin-categories-page__header">
		<div>
			<h1>Категорії товарів</h1>
			<p class="admin-categories-page__text">
				Тут можна додавати, редагувати та видаляти категорії для подальшого групування товарів.
			</p>
		</div>
	</div>

	<?php if ($feedbackMessage !== null): ?>
		<p class="admin-categories-page__message admin-categories-page__message--<?= htmlspecialchars($feedbackType, ENT_QUOTES, 'UTF-8'); ?>">
			<?= htmlspecialchars($feedbackMessage, ENT_QUOTES, 'UTF-8'); ?>
		</p>
	<?php endif; ?>

	<div class="admin-categories-form-card">
		<h2><?= $formMode === 'edit' ? 'Редагування категорії' : 'Додавання категорії'; ?></h2>
		<form method="post" class="admin-categories-form">
			<?php if ($formMode === 'edit' && $editingCategory): ?>
				<input type="hidden" name="category_id" value="<?= (int) $editingCategory['id']; ?>">
			<?php endif; ?>

			<div class="admin-categories-form__grid">
				<div class="admin-categories-form__field">
					<label for="category-name">Назва</label>
					<input id="category-name" type="text" name="name" value="<?= htmlspecialchars($formValues['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
				</div>

				<div class="admin-categories-form__field">
					<label for="category-slug">Slug</label>
					<input id="category-slug" type="text" name="slug" value="<?= htmlspecialchars($formValues['slug'], ENT_QUOTES, 'UTF-8'); ?>">
				</div>

				<div class="admin-categories-form__field admin-categories-form__field--full">
					<label for="category-description">Опис</label>
					<textarea id="category-description" name="description" rows="4"><?= htmlspecialchars($formValues['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
				</div>
			</div>

			<div class="admin-categories-form__actions">
				<?php if ($formMode === 'edit'): ?>
					<button type="submit" name="update_category" class="admin-categories-form__submit">Зберегти зміни</button>
					<a href="/admin/categories.php" class="admin-categories-form__cancel">Скасувати</a>
				<?php else: ?>
					<button type="submit" name="create_category" class="admin-categories-form__submit">Додати категорію</button>
				<?php endif; ?>
			</div>
		</form>
	</div>

	<div class="admin-categories-page__table-wrap">
		<table class="admin-categories-table">
			<colgroup>
				<col class="admin-categories-table__col admin-categories-table__col--id">
				<col class="admin-categories-table__col admin-categories-table__col--name">
				<col class="admin-categories-table__col admin-categories-table__col--slug">
				<col class="admin-categories-table__col admin-categories-table__col--description">
				<col class="admin-categories-table__col admin-categories-table__col--actions">
			</colgroup>
			<thead>
				<tr>
					<th>ID</th>
					<th>Назва</th>
					<th>Slug</th>
					<th>Опис</th>
					<th>Дії</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($categories): ?>
					<?php foreach ($categories as $category): ?>
						<tr>
							<td><?= (int) $category['id']; ?></td>
							<td><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?= htmlspecialchars((string) $category['slug'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?= htmlspecialchars((string) ($category['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
							<td>
								<div class="admin-categories-table__actions">
									<a href="/admin/categories.php?edit=<?= (int) $category['id']; ?>" class="admin-categories-table__action admin-categories-table__action--edit">Редагувати</a>
									<form method="post" class="admin-categories-table__action-form">
										<input type="hidden" name="category_id" value="<?= (int) $category['id']; ?>">
										<button type="submit" name="delete_category" class="admin-categories-table__action admin-categories-table__action--delete" onclick="return confirm('Ви дійсно хочете видалити цю категорію?');">Видалити</button>
									</form>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="5">Категорій поки не знайдено.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
    <?php if ($formMode !== 'create'): ?>
        <div class="admin-categories-page__footer">
            <a href="/admin/categories.php" class="admin-categories-page__add-button">Додати категорію</a>
        </div>
    <?php endif; ?>
</section>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>
