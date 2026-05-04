<?php
require __DIR__ . '/../app/config/config.php';
require VIEWS_PATH . '/layouts/header.php';
?>
<section class="contacts-page">
	<div class="contacts-page__intro">
		<h1>Контакти</h1>
		<p>
			Якщо у вас є запитання щодо роботи сайту, доступу до каталогу або наповнення сторінок,
			зв'яжіться з нами зручним для вас способом.
		</p>
	</div>

	<div class="contacts-page__grid">
		<article class="contacts-card">
			<h2>Основні контакти</h2>
			<p><strong>Електронна пошта:</strong> support@toyshop.local</p>
			<p><strong>Телефон:</strong> +380 00 000 00 00</p>
		</article>

		<article class="contacts-card">
			<h2>Графік відповіді</h2>
			<p>Понеділок – п'ятниця</p>
			<p>09:00 – 18:00</p>
		</article>

		<article class="contacts-card">
			<h2>Для чого можна звертатися</h2>
			<p>Питання щодо реєстрації, входу в систему, доступу до каталогу та загальної структури сайту.</p>
		</article>
	</div>
</section>
<?php require VIEWS_PATH . '/layouts/footer.php'; ?> 