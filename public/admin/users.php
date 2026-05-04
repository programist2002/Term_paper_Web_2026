<?php
require __DIR__ . '/../../app/config/config.php';
require_once HELPERS_PATH . '/auth.php';
require_once CONTROLLERS_PATH . '/UserController.php';

requireAdmin();

$userController = new UserController();
$feedbackMessage = null;
$feedbackType = 'success';
$editingUser = null;
$formMode = 'create';
$formValues = [
	'name' => '',
	'email' => '',
	'role' => 'user',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
	$userId = (int) ($_POST['user_id'] ?? 0);
	$result = $userController->deleteUser($userId);

	if ($result['success']) {
		$feedbackMessage = $result['message'];
	} else {
		$feedbackMessage = $result['error'];
		$feedbackType = 'error';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
	$formValues = [
		'name' => trim((string) ($_POST['name'] ?? '')),
		'email' => trim((string) ($_POST['email'] ?? '')),
		'role' => (string) ($_POST['role'] ?? 'user'),
	];
	$result = $userController->createUser($_POST);

	if ($result['success']) {
		$feedbackMessage = $result['message'];
		$formValues = [
			'name' => '',
			'email' => '',
			'role' => 'user',
		];
	} else {
		$feedbackMessage = $result['error'];
		$feedbackType = 'error';
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
	$userId = (int) $_POST['user_id'];
	$formMode = 'edit';
	$editingUser = $userController->getUserById($userId);
	$formValues = [
		'name' => trim((string) ($_POST['name'] ?? '')),
		'email' => trim((string) ($_POST['email'] ?? '')),
		'role' => (string) ($_POST['role'] ?? 'user'),
	];
	$result = $userController->updateUser($userId, $_POST);

	if ($result['success']) {
		$feedbackMessage = $result['message'];
		$formMode = 'create';
		$editingUser = null;
		$formValues = [
			'name' => '',
			'email' => '',
			'role' => 'user',
		];
	} else {
		$feedbackMessage = $result['error'];
		$feedbackType = 'error';
	}
}

if ($formMode === 'create' && isset($_GET['edit'])) {
	$userId = (int) $_GET['edit'];
	$editingUser = $userController->getUserById($userId);

	if ($editingUser) {
		$formMode = 'edit';
		$formValues = [
			'name' => (string) $editingUser['name'],
			'email' => (string) $editingUser['email'],
			'role' => (string) $editingUser['role'],
		];
	} else {
		$feedbackMessage = 'Користувача не знайдено.';
		$feedbackType = 'error';
	}
}

$users = $userController->getUsers();

function formatUserRole(string $role): string
{
	return match ($role) {
		'admin' => 'Адміністратор',
		default => 'Користувач',
	};
}

require VIEWS_PATH . '/layouts/header.php';
?>
<section class="admin-users-page">
	<div class="admin-users-page__header">
		<div>
			<h1>Користувачі системи</h1>
			<p class="admin-users-page__text">
				Тут можна переглядати список користувачів, а також підготувати дії для додавання,
				редагування та видалення облікових записів.
			</p>
		</div>
	</div>

	<?php if ($feedbackMessage !== null): ?>
		<p class="admin-users-page__message admin-users-page__message--<?= htmlspecialchars($feedbackType, ENT_QUOTES, 'UTF-8'); ?>">
			<?= htmlspecialchars($feedbackMessage, ENT_QUOTES, 'UTF-8'); ?>
		</p>
	<?php endif; ?>

	<div class="admin-users-form-card">
		<h2><?= $formMode === 'edit' ? 'Редагування користувача' : 'Додавання користувача'; ?></h2>
		<form method="post" class="admin-users-form">
			<?php if ($formMode === 'edit' && $editingUser): ?>
				<input type="hidden" name="user_id" value="<?= (int) $editingUser['id']; ?>">
			<?php endif; ?>

			<div class="admin-users-form__grid">
				<div class="admin-users-form__field">
					<label for="user-name">Ім'я</label>
					<input id="user-name" type="text" name="name" value="<?= htmlspecialchars($formValues['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
				</div>

				<div class="admin-users-form__field">
					<label for="user-email">Електронна пошта</label>
					<input id="user-email" type="email" name="email" value="<?= htmlspecialchars($formValues['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
				</div>

				<div class="admin-users-form__field">
					<label for="user-password">Пароль<?= $formMode === 'edit' ? ' (залиш порожнім, щоб не змінювати)' : ''; ?></label>
					<input id="user-password" type="password" name="password" <?= $formMode === 'create' ? 'required' : ''; ?>>
				</div>

				<div class="admin-users-form__field">
					<label for="user-role">Роль</label>
					<select id="user-role" name="role">
						<option value="user" <?= $formValues['role'] === 'user' ? 'selected' : ''; ?>>Користувач</option>
						<option value="admin" <?= $formValues['role'] === 'admin' ? 'selected' : ''; ?>>Адміністратор</option>
					</select>
				</div>
			</div>

			<div class="admin-users-form__actions">
				<?php if ($formMode === 'edit'): ?>
					<button type="submit" name="update_user" class="admin-users-form__submit">Зберегти зміни</button>
					<a href="/admin/users.php" class="admin-users-form__cancel">Скасувати</a>
				<?php else: ?>
					<button type="submit" name="create_user" class="admin-users-form__submit">Додати користувача</button>
				<?php endif; ?>
			</div>
		</form>
	</div>

	<div class="admin-page-search" data-admin-search>
		<form class="admin-page-search__form" data-admin-search-form>
			<input
				id="admin-users-search"
				type="search"
				class="admin-page-search__input"
				placeholder="Пошук за ID, ім'ям, поштою або роллю"
				aria-label="Пошук користувачів"
				data-admin-search-input
			>
			<button type="submit" class="admin-page-search__button">Знайти</button>
		</form>
		<p class="admin-page-search__empty admin-page-search__empty--hidden" data-admin-search-empty>
			За вашим запитом користувачів не знайдено.
		</p>
	</div>

	<div class="admin-users-page__table-wrap">
		<table class="admin-users-table">
			<colgroup>
				<col class="admin-users-table__col admin-users-table__col--id">
				<col class="admin-users-table__col admin-users-table__col--name">
				<col class="admin-users-table__col admin-users-table__col--email">
				<col class="admin-users-table__col admin-users-table__col--role">
				<col class="admin-users-table__col admin-users-table__col--actions">
			</colgroup>
			<thead>
				<tr>
					<th>ID</th>
					<th>Ім'я</th>
					<th>Електронна пошта</th>
					<th>Роль</th>
					<th>Дії</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($users): ?>
					<?php foreach ($users as $user): ?>
						<tr data-admin-search-item>
							<td><?= (int) $user['id']; ?></td>
							<td><?= htmlspecialchars((string) $user['name'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?= htmlspecialchars((string) $user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
							<td><?= htmlspecialchars(formatUserRole((string) $user['role']), ENT_QUOTES, 'UTF-8'); ?></td>
							<td>
								<div class="admin-users-table__actions">
									<a href="/admin/users.php?edit=<?= (int) $user['id']; ?>" class="admin-users-table__action admin-users-table__action--edit">Редагувати</a>
									<form method="post" class="admin-users-table__action-form">
										<input type="hidden" name="user_id" value="<?= (int) $user['id']; ?>">
										<button type="submit" name="delete_user" class="admin-users-table__action admin-users-table__action--delete" onclick="return confirm('Ви дійсно хочете видалити цього користувача?');">Видалити</button>
									</form>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="5">Користувачів не знайдено.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php if ($formMode !== 'create'): ?>
		<div class="admin-users-page__footer">
			<a href="/admin/users.php" class="admin-users-page__add-button">Додати користувача</a>
		</div>
	<?php endif; ?>
</section>
<script src="/assets/js/admin-search.js" defer></script>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?> 