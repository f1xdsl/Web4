<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = trim($_POST['name']);
    $userEmail = trim($_POST['email']);
    $userMessage = trim($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // Настройки SMTP-сервера Gmail
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'mikeybx222@gmail.com'; // Твой Gmail
        $mail->Password = 'puye ebnt xsub jjfd'; // Пароль приложения
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // От кого и кому
        $mail->setFrom('mikeybx222@gmail.com', 'Insurance Site');
        $mail->addAddress('mikeybx222@gmail.com', 'Insurance');

        // Устанавливаем email отправителя как ответный адрес
        $mail->addReplyTo($userEmail, $userName);

        // Содержание письма
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $mail->Subject = 'Запрос обратной связи с сайта';

        $mail->Body = "
            <strong>Имя:</strong> {$userName}<br>
            <strong>Email:</strong> {$userEmail}<br><br>
            <strong>Сообщение:</strong><br>
            " . nl2br(htmlspecialchars($userMessage));

        $mail->AltBody = "Имя: {$userName}\nEmail: {$userEmail}\n\nСообщение:\n{$userMessage}";

        $mail->send();
        $success = "Сообщение успешно отправлено!";
    } catch (Exception $e) {
        $error = "Ошибка при отправке: {$mail->ErrorInfo}";
    }
}
?>

<h2>Свяжитесь с нами</h2>

<?php if ($success): ?>
    <p class="success"><?= $success ?></p>
<?php elseif ($error): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="post" class="form">
    <label>Ваше имя:
        <input type="text" name="name" required>
    </label>
    <label>Email:
        <input type="email" name="email" required>
    </label>
    <label>Сообщение:
        <textarea name="message" required rows="5"></textarea>
    </label>
    <button type="submit">Отправить</button>
</form>

<?php
$content = ob_get_clean();
include 'templates/template.php';
?>
