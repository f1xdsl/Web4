<?php
require 'includes/session.php';

$to = $_GET['to'] ?? '';

if ($to === 'request') {
    if (!isset($_SESSION['user'])) {
        $_SESSION['redirect_after_login'] = 'request.php';
        header('Location: login.php');
        exit;
    } else {
        header('Location: request.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
