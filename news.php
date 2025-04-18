<?php
require 'includes/db.php';
require 'includes/session.php';

$title = "Новости";
ob_start();

$user = $_SESSION['user'] ?? null;

// Проверка на администратора
$is_admin = $user && isset($user['Роль']) && $user['Роль'] === 'admin';

// Добавление новости
if ($is_admin && $_SERVER["REQUEST_METHOD"] === "POST") {
    $title_post = trim($_POST['title']);
    $text_post = trim($_POST['text']);

    if ($title_post && $text_post) {
        $stmt = $pdo->prepare("INSERT INTO новости (`Заголовок`, `Текст`, `Дата`) 
                               VALUES (?, ?, NOW())");
        $stmt->execute([$title_post, $text_post]);
        echo "<p class='success'>Новость добавлена!</p>";
    } else {
        echo "<p class='error'>Пожалуйста, заполните все поля.</p>";
    }
}

// Получение новостей
$stmt = $pdo->query("SELECT * FROM новости ORDER BY Дата DESC");
$news = $stmt->fetchAll();
?>

<h2>Новости страховой компании</h2>

<div class="news-list">
    <?php foreach ($news as $n): ?>
        <div class="news-item">
            <h3><?= htmlspecialchars($n['Заголовок']) ?></h3>
            <p class="date"><?= date("d.m.Y H:i", strtotime($n['Дата'])) ?></p>
            <button class="toggle-btn" onclick="toggleText('news-<?= $n['id'] ?>')">Подробнее</button>
            <div class="news-text" id="news-<?= $n['id'] ?>">
                <p><?= nl2br(htmlspecialchars($n['Текст'])) ?></p>
            </div>
            <hr>
        </div>
    <?php endforeach; ?>
</div>

<?php if ($is_admin): ?>
    <h3>Добавить новость</h3>
    <form method="post" class="form">
        <label>Заголовок:
            <input type="text" name="title" required>
        </label>
        <label>Текст новости:
            <textarea name="text" rows="6" required></textarea>
        </label>
        <button type="submit">Опубликовать</button>
    </form>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'templates/template.php';
?>

<!-- JavaScript для раскрытия текста новости -->
<script>
function toggleText(id) {
    const text = document.getElementById(id);
    const button = text.previousElementSibling;

    if (text.style.display === 'block') {
        text.style.display = 'none';
        button.textContent = 'Подробнее';
    } else {
        text.style.display = 'block';
        button.textContent = 'Скрыть';
    }
}
</script>
