<?php
require __DIR__ . '/../app/config/config.php';
require CONTROLLERS_PATH . '/AuthController.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errorMessage = 'Введіть електронну пошту та пароль.';
    } else {
        $authController = new AuthController();
        $user = $authController->authenticateUser($email, $password);

        if ($user !== null) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: /');
            exit;
        }

        $errorMessage = 'Невірний логін або пароль.';
    }
}

require VIEWS_PATH . '/layouts/header.php';
?>
<section class="login-section">
    <h2>Увійти</h2>
    <form action="login.php" method="post" class="login-form<?php echo $errorMessage !== '' ? ' has-error' : ''; ?>">
        <div class="login-form__fields">
            <div class="login-form__field">
                <label for="email">Електронна пошта:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="login-form__field">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
        </div>
        <?php if ($errorMessage !== ''): ?>
        	<p class="login-form__error"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <button type="submit">Увійти</button>
    </form>
    <p class="login-form__register-link">
        Ще не зареєстровані? <a href="register.php">Створити акаунт</a>
    </p>
</section>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?> 