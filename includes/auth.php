<?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM `клиенты` WHERE `Логин` = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['Пароль'])) {
       // После успешной авторизации

        $_SESSION['user'] = $user;
        // Перенаправление после логина
        if (!empty($_SESSION['redirect_after_login'])) {
            
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']);
            header("Location: $redirect");
            exit;
        }
        else
        {
            header('Location: cabinet.php');
            exit;
        }

    } else {
        $error = "Неверный логин или пароль";
    }
}
?>
