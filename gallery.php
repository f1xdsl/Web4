<?php
require 'includes/db.php';

$title = "Галерея застрахованного имущества";
ob_start();

// Получаем договоры с изображением предмета
$stmt = $pdo->prepare("
    SELECT договоры.Предмет_страхования AS Предмет, 
           договоры.Дата_заключения AS Дата, 
           договоры.Изображение, 
           'Договор' AS Источник
    FROM договоры
    WHERE договоры.Предмет_страхования IN (1, 2)
");

$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Сортируем по дате (новее — выше)
usort($items, function($a, $b) {
    return strtotime($b['Дата']) - strtotime($a['Дата']);
});
?>

<h2>Галерея застрахованного имущества</h2>

<div class="gallery">
    <?php if ($items): ?>
        <?php foreach ($items as $item): ?>
            <div class="gallery-item">
                <!-- Изображение, клик по которому откроет модальное окно -->
                <img src="<?= htmlspecialchars($item['Изображение'] ?? '/assets/images/placeholder.jpg') ?>" 
                     alt="Имущество" class="gallery-img" 
                     onclick="openModal('<?= htmlspecialchars($item['Изображение'] ?? '/assets/images/placeholder.jpg') ?>')">
                <p><strong><?= htmlspecialchars($item['Предмет']) ?></strong></p>
                <p><?= $item['Дата'] ?> (<?= $item['Источник'] ?>)</p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Пока нет застрахованного имущества.</p>
    <?php endif; ?>
</div>

<!-- Модальное окно для отображения увеличенного изображения -->
<div id="myModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modal-img">
    <div id="caption"></div>
</div>

<script>
// Функция для открытия модального окна с изображением
function openModal(imageSrc) {
    var modal = document.getElementById("myModal");
    var modalImg = document.getElementById("modal-img");
    var caption = document.getElementById("caption");

    modal.style.display = "block";  // Показываем модальное окно
    modalImg.src = imageSrc;  // Устанавливаем изображение
    caption.innerHTML = "Полный размер";  // Подпись (можно добавить динамически)
}

// Функция для закрытия модального окна
function closeModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
}
</script>



<?php
$content = ob_get_clean();
include 'templates/template.php';
?>
