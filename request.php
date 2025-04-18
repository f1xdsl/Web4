<?php
require 'includes/db.php';
require 'includes/session.php';

$title = "Подать заявку на страхование";
ob_start();

$user = $_SESSION['user'] ?? null;

if (!$user) {
    echo "<p>Для подачи заявки необходимо <a href='login.php'>войти в личный кабинет</a>.</p>";
    $content = ob_get_clean();
    include 'templates/template.php';
    exit;
}

// Получение списка предметов страхования
$itemsStmt = $pdo->query("SELECT id, название FROM предметы_страхования");
$insuranceItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Получение номера телефона из таблицы Клиенты
$stmtPhone = $pdo->prepare("SELECT Телефон FROM клиенты WHERE Код_Клиента = ?");
$stmtPhone->execute([$user['Код_Клиента']]);
$phoneRow = $stmtPhone->fetch(PDO::FETCH_ASSOC);
$registeredPhone = $phoneRow['Телефон'] ?? '';

$useRegisteredPhone = isset($_POST['use_registered_phone']);
$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $itemIds = $_POST['items'] ?? [];
    $itemIds = array_filter($itemIds); // Удалим пустые

    $description = trim($_POST['description'] ?? '');
    $useRegisteredPhone = isset($_POST['use_registered_phone']);
    $phone = $useRegisteredPhone ? $registeredPhone : trim($_POST['phone']);

    if (empty($itemIds) || !$description || !$phone) {
        $errorMessage = "Пожалуйста, заполните все поля и выберите хотя бы один предмет.";
    } elseif ($useRegisteredPhone && empty($registeredPhone)) {
        $errorMessage = "Указанный при регистрации номер телефона отсутствует. Введите номер вручную.";
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO заявки (Код_Клиента, Описание, Телефон, Дата_заявки)
                                   VALUES (?, ?, ?, NOW())");
            $stmt->execute([$user['Код_Клиента'], $description, $phone]);
            $lastInsertId = $pdo->lastInsertId();

            $stmtLink = $pdo->prepare("INSERT INTO заявки_предмет_страхования (Код_Заявки, Код_Предмета)
                                       VALUES (?, ?)");
            foreach ($itemIds as $itemId) {
                $stmtLink->execute([$lastInsertId, $itemId]);
            }

            $pdo->commit();
            $successMessage = "Заявка успешно отправлена!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $errorMessage = "Ошибка при сохранении заявки: " . $e->getMessage();
        }
    }
}
?>

<h2>Подать заявку на страхование</h2>

<?php if ($successMessage): ?>
    <p class="success"><?= htmlspecialchars($successMessage) ?></p>
<?php elseif ($errorMessage): ?>
    <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
<?php endif; ?>

<form method="post" class="form">
    <div id="items-container">
        <?php
        $selectedItems = $_POST['items'] ?? [''];
        foreach ($selectedItems as $selectedItemId):
        ?>
            <div class="item-group">
                <label>Предмет страхования:
                    <select name="items[]" required>
                        <option value="">-- Выберите предмет --</option>
                        <?php foreach ($insuranceItems as $item): ?>
                            <option value="<?= $item['id'] ?>"
                                <?= $selectedItemId == $item['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($item['название']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addItemField()">Добавить предмет страхования</button>

    <label>Описание:
        <textarea name="description" rows="5" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </label>

    <label>
        <input type="checkbox" name="use_registered_phone" id="use_registered_phone" <?= $useRegisteredPhone ? 'checked' : '' ?>>
        Использовать номер, указанный при регистрации
    </label>

    <?php if ($registeredPhone): ?>
        <p>Ваш номер: <strong><?= htmlspecialchars($registeredPhone) ?></strong></p>
    <?php else: ?>
        <p class="error">Номер телефона не указан в профиле!</p>
    <?php endif; ?>

    <label>Номер телефона для связи:
        <input type="tel" name="phone" id="phone_input"
               value="<?= htmlspecialchars($useRegisteredPhone ? $registeredPhone : ($_POST['phone'] ?? '')) ?>"
               <?= $useRegisteredPhone ? 'readonly' : '' ?> required>
    </label>

    <button type="submit">Отправить заявку</button>
</form>

<script>
const insuranceItems = <?= json_encode($insuranceItems) ?>;

function addItemField() {
    const container = document.getElementById('items-container');
    const itemGroup = document.createElement('div');
    itemGroup.className = 'item-group';

    const select = document.createElement('select');
    select.name = 'items[]';
    select.required = true;

    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = '-- Выберите предмет --';
    select.appendChild(defaultOption);

    insuranceItems.forEach(item => {
        const option = document.createElement('option');
        option.value = item.id;
        option.textContent = item.название;
        select.appendChild(option);
    });

    const label = document.createElement('label');
    label.textContent = 'Предмет страхования: ';
    label.appendChild(select);

    itemGroup.appendChild(label);
    container.appendChild(itemGroup);
}

document.getElementById('use_registered_phone').addEventListener('change', function () {
    const phoneInput = document.getElementById('phone_input');
    if (this.checked) {
        phoneInput.readOnly = true;
        phoneInput.value = '<?= htmlspecialchars($registeredPhone) ?>';
    } else {
        phoneInput.readOnly = false;
        phoneInput.value = '';
    }
});
</script>


<?php
$content = ob_get_clean();
include 'templates/template.php';
?>
