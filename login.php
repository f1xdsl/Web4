<?php
require 'includes/auth.php';
$title = "Вход";
ob_start();
?>

<section class="auth-container">
    <h2>Вход</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post" class="auth-form">
        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" required placeholder="Введите логин">
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required placeholder="Введите пароль">
        </div>

        <button type="submit" class="btn">Войти</button>
    </form>
    <p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
</section>

<?php
$content = ob_get_clean();
include 'templates/template.php';
?>
