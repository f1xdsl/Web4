<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? "SafeGuard" ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Страховая компания "ГарантЗащита"</h1>
        <nav>
            <a href="index.php">Главная</a>
            <a href="news.php">Новости</a>
            <a href="gallery.php">Галерея</a>
            <a href="about.php">О нас</a>
            <a href="contacts.php">Контакты</a>
            <?php if (isset($_SESSION['user'])): ?>
                <a href="/cabinet.php">Кабинет</a>
                <a href="/logout.php">Выход</a>
            <?php else: ?>
                <a href="/login.php">Вход</a>
                <a href="/register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?= $content ?>
    </main>
    <footer>
        <p>&copy; <?= date("Y") ?> SafeGuard</p>
    </footer>
</body>
</html>
