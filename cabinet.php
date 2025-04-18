<?php
require 'includes/db.php';
require 'includes/session.php';

$title = "Личный кабинет";
ob_start();

$user = $_SESSION['user'] ?? null;

if (!$user) {
    echo "<p>Для доступа в личный кабинет необходимо <a href='login.php'>войти</a>.</p>";
    $content = ob_get_clean();
    include 'templates/template.php';
    exit;
}

// Получаем заявки пользователя
$stmt_requests = $pdo->prepare("SELECT * FROM заявки WHERE Код_Клиента = ? ORDER BY Дата_заявки DESC");
$stmt_requests->execute([$user['Код_Клиента']]);
$requests_raw = $stmt_requests->fetchAll(PDO::FETCH_ASSOC);

// Получаем предметы для всех заявок
$requests = [];

if (!empty($requests_raw)) {
    $request_ids = array_column($requests_raw, 'Код_Заявки');
    $inQuery = implode(',', array_fill(0, count($request_ids), '?'));

    $stmt_items = $pdo->prepare("
        SELECT zp.Код_Заявки, ps.Название 
        FROM заявки_предмет_страхования zp
        JOIN предметы_страхования ps ON zp.Код_Предмета = ps.id
        WHERE zp.Код_Заявки IN ($inQuery)
    ");
    $stmt_items->execute($request_ids);
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    // Группируем предметы по заявке
    $items_by_request = [];
    foreach ($items as $item) {
        $items_by_request[$item['Код_Заявки']][] = $item['Название'];
    }

    // Собираем полные заявки
    foreach ($requests_raw as $request) {
        $request['Предметы'] = $items_by_request[$request['Код_Заявки']] ?? [];
        $requests[] = $request;
    }
}

// Получаем договоры пользователя
$stmt_contracts = $pdo->prepare("SELECT д.*, п.Название AS Название_Предмета 
                                FROM договоры д
                                LEFT JOIN предметы_страхования п ON д.Предмет_страхования = п.id 
                                WHERE д.Код_Клиента = ? 
                                ORDER BY д.Дата_заключения DESC");
$stmt_contracts->execute([$user['Код_Клиента']]);
$contracts = $stmt_contracts->fetchAll();
?>

<h2>Личный кабинет</h2>

<script>
function toggleDetails(id) {
    const btn = document.getElementById('toggle-btn-' + id);
    const details = document.getElementById('details-' + id);
    btn.classList.toggle('active');
    if (details.style.display === 'block') {
        details.style.display = 'none';
    } else {
        details.style.display = 'block';
    }
}
</script>

<!-- Заявки -->
<h3>Ваши заявки на страхование</h3>
<?php if ($requests): ?>
    <?php foreach ($requests as $index => $request): ?>
        <div class="request-block">
            <div class="toggle-btn" id="toggle-btn-r<?= $index ?>" onclick="toggleDetails('r<?= $index ?>')">
                <?= htmlspecialchars(implode(', ', $request['Предметы'])) ?> — <?= htmlspecialchars($request['Статус']) ?>
            </div>
            <div class="details" id="details-r<?= $index ?>">
                <table>
                    <tr><th>Дата подачи</th><td><?= $request['Дата_заявки'] ?></td></tr>
                    <tr><th>Описание</th><td><?= nl2br(htmlspecialchars($request['Описание'] ?? '')) ?></td></tr>
                    <tr><th>Статус</th><td><?= htmlspecialchars($request['Статус']) ?></td></tr>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>У вас пока нет заявок.</p>
<?php endif; ?>

<!-- Договоры -->
<h3>Ваши заключенные договоры</h3>
<?php if ($contracts): ?>
    <?php foreach ($contracts as $index => $contract): ?>
        <div class="contract-block">
            <div class="toggle-btn" id="toggle-btn-c<?= $index ?>" onclick="toggleDetails('c<?= $index ?>')">
                Договор №<?= htmlspecialchars($contract['Номер_договора']) ?>
            </div>
            <div class="details" id="details-c<?= $index ?>">
                <table>
                    <tr><th>Дата заключения</th><td><?= $contract['Дата_заключения'] ?></td></tr>
                    <tr><th>Предмет страхования</th><td><?= htmlspecialchars($contract['Название_Предмета']) ?></td></tr>
                    <tr><th>Сумма страхования</th><td><?= htmlspecialchars($contract['Сумма']) ?> ₽</td></tr>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>У вас нет заключенных договоров.</p>
<?php endif; ?>

<h3>
    <a href="request.php" class="btn-create-request">Создать новую заявку</a>
</h3>

<?php
$content = ob_get_clean();
include 'templates/template.php';
?>
