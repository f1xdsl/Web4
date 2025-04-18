<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $phone = $_POST['phone'];
    $date = date("Y-m-d");

    var_dump($_POST);

    $stmt = $pdo->prepare("INSERT INTO `клиенты` (`Логин`, `Пароль`, `Имя`, `Фамилия`, `Телефон`, `Статус`) VALUES (?, ?, ?, ?, ?, 'активный')");

    $stmt->execute([$login, $password, $name, $surname, $phone]);

    header("Location: /login.php");
    exit();
}
?>
