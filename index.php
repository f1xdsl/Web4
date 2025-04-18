<?php
$title = "Главная";
ob_start();
?>

<section class="hero">
    <h1>Добро пожаловать в страховую компанию "ГарантЗащита"</h1>
    <p>Надёжность. Уверенность. Будущее под защитой.</p>
    <a href="redirect.php?to=request" class="btn">Оформить заявку</a>
</section>

<section class="info">
    <h2>Наши преимущества</h2>
    <ul>
        <li>Более 10 лет опыта на рынке страхования</li>
        <li>Квалифицированные специалисты</li>
        <li>Прозрачные условия страхования</li>
        <li>Поддержка 24/7</li>
    </ul>
</section>

<section class="links">
    <h2>Разделы сайта</h2>
    <div class="cards">
        <a href="gallery.php" class="card">Галерея</a>
        <a href="news.php" class="card">Новости</a>
        <a href="contacts.php" class="card">Контакты</a>
        <a href="login.php" class="card">Вход в личный кабинет</a>
    </div>
</section>

<?php
$content = ob_get_clean();
include 'templates/template.php';
