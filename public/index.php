<?php
require __DIR__ . '/../app/config/config.php';
require_once HELPERS_PATH . '/auth.php';
require_once CONTROLLERS_PATH . '/ProductController.php';

$productController = new ProductController();
$products = array_slice($productController->getAvailableProducts(), 0, 5);
$productGridClass = 'catalog-page__grid';
$emptyMessage = 'Наразі товари для швидкого перегляду недоступні.';
$showCategory = true;

require VIEWS_PATH . '/layouts/header.php';

$isAuthenticated = isAuthenticated();
?>
<?php if ($isAuthenticated): ?>
	<section class="home-products">
	<div class="home-products__intro">
		<h2>Що можна переглянути вже зараз</h2>
		<p>
			Кілька позицій із каталогу, щоб швидко ознайомитися з асортиментом магазину.
		</p>
	</div>

	<?php require VIEWS_PATH . '/partials/product-grid.php'; ?>

	<div class="home-products__footer">
		<a href="catalog.php" class="guest-access__button guest-access__button--primary">Перейти до каталогу</a>
	</div>
</section>
<?php else: ?>
	<section class="guest-access">
		<div class="guest-access__content">
			<h1>Іграшки, які легко обирати батькам і приємно отримувати дітям</h1>
			<p class="guest-access__text">
				На сайті зібрано каталог дитячих товарів для різного віку та інтересів: від м'яких іграшок до розвивальних наборів.
				Ми зробили головну сторінку простою і зрозумілою, щоб ви швидко розібралися, як працює система.
			</p>

			<div class="guest-access__actions">
				<a href="register.php" class="guest-access__button guest-access__button--primary">Зареєструватися</a>
				<a href="login.php" class="guest-access__button guest-access__button--secondary">Увійти в систему</a>
			</div>

			<div class="guest-access__notice">
				<strong>Зверніть увагу:</strong>
				каталог товарів доступний лише після реєстрації або входу в систему.
			</div>
		</div>

		<div class="guest-access__visual">
			<div class="guest-access__image-wrap">
				<img src="assets/images/logo.png" alt="Логотип магазину дитячих іграшок" class="guest-access__image">
			</div>
		</div>
	</section>

	<section class="home-info">
		<article class="home-info__card">
			<h2>Що є в каталозі</h2>
			<p>М'які іграшки, набори для творчості, розвивальні товари та подарункові ідеї для різного віку.</p>
		</article>
		<article class="home-info__card">
			<h2>Як це працює</h2>
			<p>Спочатку створюєте акаунт або входите в систему, після цього отримуєте доступ до перегляду товарів і подальшої роботи з каталогом.</p>
		</article>
		<article class="home-info__card">
			<h2>Для кого цей сайт</h2>
			<p>Для батьків, родичів і всіх, хто хоче швидко знайти цікаву іграшку та зорієнтуватися в асортименті без зайвої складності.</p>
		</article>
	</section>

	<section class="home-steps">
		<div class="home-steps__intro">
			<h2>Що зробити, щоб почати</h2>
		</div>
		<div class="home-steps__grid">
			<div class="home-step">
				<span class="home-step__number">1</span>
				<h3>Створіть обліковий запис</h3>
				<p>Реєстрація відкриє доступ до каталогу та подальшої роботи із сайтом.</p>
			</div>
			<div class="home-step">
				<span class="home-step__number">2</span>
				<h3>Увійдіть у систему</h3>
				<p>Після авторизації сайт розпізнає вас і надає доступ до вмісту для зареєстрованих користувачів.</p>
			</div>
			<div class="home-step">
				<span class="home-step__number">3</span>
				<h3>Переглядайте каталог</h3>
				<p>Коли авторизація успішна, можна перейти до каталогу та знайомитися з товарами.</p>
			</div>
		</div>
	</section>
<?php endif; ?>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?>