<?php
session_start();
include("../db.php");
include("../validation.php");

$applicationId = $_GET['application_id'] ?? $_SESSION['application_id'] ?? null;

if (!$applicationId) {
    die('Ошибка: ID заявки не указан.');
}

try {
    $db = getDatabaseConnection();

    // Проверяем, существует ли заявка с указанным application_id
    $stmt = $db->prepare("SELECT COUNT(*) FROM user_applications WHERE application_id = ?");
    $stmt->execute([$applicationId]);
    $applicationExists = $stmt->fetchColumn();

    if (!$applicationExists) {
        die('Ошибка: заявка не найдена.');
    }

    // Получаем данные заявки
    $stmt = $db->prepare("SELECT full_name, phone_number, email_address, birth_date, gender, biography FROM user_applications WHERE application_id = ?");
    $stmt->execute([$applicationId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Получаем связанные языки программирования
    $stmt = $db->prepare("SELECT language_id FROM application_languages WHERE application_id = ?");
    $stmt->execute([$applicationId]);
    $selectedLanguages = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Получаем список всех языков программирования
    $stmt = $db->query("SELECT language_id, language_name FROM programming_languages");
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_SESSION['edit_values'])) {
        $data = array_merge($data, $_SESSION['edit_values']);
        $selectedLanguages = $_SESSION['edit_values']['lang'] ?? $selectedLanguages;
    }
} catch (PDOException $e) {
    die('Ошибка БД: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['logout'])) {
    $errors = validateFormData($_POST);

    if (!empty($errors)) {
        $_SESSION['edit_values'] = $_POST;
        $_SESSION['edit_errors'] = $errors;
    } else {
        try {
            $stmt = $db->prepare("UPDATE user_applications SET full_name = ?, phone_number = ?, email_address = ?, birth_date = ?, gender = ?, biography = ? WHERE application_id = ?");
            $stmt->execute([
                $_POST['FIO'], $_POST['tel'], $_POST['email'], $_POST['DR'], $_POST['sex'], $_POST['bio'], $applicationId
            ]);

            $stmt = $db->prepare("DELETE FROM application_languages WHERE application_id = ?");
            $stmt->execute([$applicationId]);

            $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            foreach ($_POST['lang'] as $languageId) {
                $stmt->execute([$applicationId, $languageId]);
            }

            $success = 'Данные успешно обновлены.';
            unset($_SESSION['edit_values'], $_SESSION['edit_errors']);
        } catch (PDOException $e) {
            die('Ошибка БД: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование данных</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-wide">
        <div class="header-box">Редактирование данных</div>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($logoutError)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($logoutError) ?></div>
        <?php endif; ?>
        <form method="post" class="p-4 border rounded bg-dark">
            <div class="mb-3">
                <label class="form-label">ФИО:</label>
                <input type="text" name="FIO" class="form-control <?= getEditError('FIO') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getEditValue('FIO') ?: $data['full_name']) ?>" maxlength="150" required>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('FIO')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Телефон:</label>
                <input type="tel" name="tel" class="form-control <?= getEditError('tel') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getEditValue('tel') ?: $data['phone_number']) ?>" maxlength="15" required>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('tel')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control <?= getEditError('email') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getEditValue('email') ?: $data['email_address']) ?>" maxlength="80" required>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('email')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата рождения:</label>
                <input type="date" name="DR" class="form-control <?= getEditError('DR') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getEditValue('DR') ?: $data['birth_date']) ?>" required>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('DR')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Пол:</label>
                <select name="sex" class="form-select <?= getEditError('sex') ? 'is-invalid' : '' ?>">
                    <option value="0" <?= (getEditValue('sex') ?: $data['gender']) == '0' ? 'selected' : '' ?>>Мужской</option>
                    <option value="1" <?= (getEditValue('sex') ?: $data['gender']) == '1' ? 'selected' : '' ?>>Женский</option>
                </select>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('sex')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Любимые языки программирования:</label>
                <?php foreach ($languages as $language): ?>
                    <div class="form-check">
                        <input type="checkbox" name="lang[]" value="<?= $language['language_id'] ?>" class="form-check-input"
                            <?= in_array($language['language_id'], $selectedLanguages) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= htmlspecialchars($language['language_name']) ?></label>
                    </div>
                <?php endforeach; ?>
                <?php if (getEditError('lang')): ?>
                    <div class="text-danger"><?= htmlspecialchars(getEditError('lang')) ?></div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Биография:</label>
                <textarea name="bio" class="form-control <?= getEditError('bio') ? 'is-invalid' : '' ?>" rows="4" maxlength="1000"><?= htmlspecialchars(getEditValue('bio') ?: $data['biography']) ?></textarea>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('bio')) ?></div>
            </div>
            <button type="submit" class="btn btn-custom w-100 mb-3">Сохранить</button>
            <button type="submit" name="logout" class="btn btn-danger w-100">Выйти</button>
        </form>
    </div>
    <?php
    unset($_SESSION['edit_errors']);
    ?>
</body>
</html>
