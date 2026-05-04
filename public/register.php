<?php
require __DIR__ . '/../app/config/config.php';
require VIEWS_PATH . '/layouts/header.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $passwordConfirmation === '') {
        $errorMessage = 'Будь ласка, заповніть всі поля.';
    } elseif ($password !== $passwordConfirmation) {
        $errorMessage = 'Паролі не співпадають.';
    } else {
        require_once MODELS_PATH . '/User.php';
        require_once CONTROLLERS_PATH . '/AuthController.php';

        $authController = new AuthController();
        $user = $authController->registerUser($name, $email, $password);

        if ($user) {
            header('Location: login.php');
            exit;
        } else {
            $errorMessage = 'Користувач з такою електронною поштою вже існує.';
        }
    }

}

?>
<section class="register-section">
	<h2>Реєстрація користувача</h2>
	<form action="register.php" method="post" class="register-form<?php echo $errorMessage !== '' ? ' has-error' : ''; ?>">
		<div class="register-form__fields">
			<div class="register-form__field">
				<label for="name">Ім'я:</label>
				<input type="text" id="name" name="name"  value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
			</div>
			<div class="register-form__field">
				<label for="email">Електронна пошта:</label>
				<input type="email" id="email" name="email"  value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
			</div>
			<div class="register-form__field">
				<label for="password">Пароль:</label>
				<input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
			</div>
			<div class="register-form__field">
				<label for="password_confirmation">Підтвердіть пароль:</label>
				<input type="password" id="password_confirmation" name="password_confirmation" value="<?php echo htmlspecialchars($passwordConfirmation ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
			</div>
		</div>
        <?php if ($errorMessage !== ''): ?>
        	<p class="register-form__error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
		<button type="submit">Зареєструватися</button>
	</form>
	<p class="register-form__login-link">
		Вже маєте акаунт? <a href="login.php">Увійти</a>
	</p>
</section>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>