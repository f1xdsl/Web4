<?php
require 'includes/register.php';
$title = "Регистрация";
ob_start();
?>

<section class="auth-container">
    <h2>Регистрация</h2>
    <form method="post" class="auth-form">
        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" required placeholder="Введите логин">
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required placeholder="Введите пароль">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required placeholder="Введите ваш email">
        </div>

        <div class="form-group">
            <label for="name">Имя:</label>
            <input type="text" id="name" name="name" required placeholder="Введите ваше имя">
        </div>

        <div class="form-group">
            <label for="surname">Фамилия:</label>
            <input type="text" id="surname" name="surname" required placeholder="Введите вашу фамилию">
        </div>

        <div class="form-group">
            <label for="phone">Номер телефона:</label>
            <input type="text" id="phone" name="phone" required placeholder="Введите номер телефона">
        </div>

        <button type="submit" class="btn">Зарегистрироваться</button>
    </form>
    <p>Уже зарегистрированы? <a href="login.php">Войти</a></p>
</section>

<?php
$content = ob_get_clean();
include 'templates/template.php';
?>
