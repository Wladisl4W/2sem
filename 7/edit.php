<?php
session_start();

// Используем whitelisting для подключаемых файлов
$allowedIncludes = ['../db.php', '../validation.php'];

foreach ($allowedIncludes as $file) {
    if (file_exists($file)) {
        include($file);
    } else {
        error_log("Попытка подключения отсутствующего файла: $file");
        die('Ошибка подключения файла.');
    }
}

// Генерация CSRF-токена
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (empty($_SESSION['user_id']) && empty($_GET['id'])) {
    header('Location: login.php');
    exit();
}

function getEditValue($name) {
    return $_SESSION['edit_values'][$name] ?? '';
}

function getEditError($name) {
    return $_SESSION['edit_errors'][$name] ?? '';
}

$errors = [];
$success = null;
$logoutError = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Ошибка CSRF: недействительный токен.');
    }

    $_SESSION = [];
    if (session_destroy()) {
        header('Location: login.php');
        exit();
    } else {
        $logoutError = 'Не удалось завершить сессию. Попробуйте снова.';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['logout'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Ошибка CSRF: недействительный токен.');
    }

    $errors = validateFormData($_POST);

    if (!empty($errors)) {
        $_SESSION['edit_values'] = $_POST;
        $_SESSION['edit_errors'] = $errors;
        $success = null;
    } else {
        try {
            $db = getDatabaseConnection();

            $stmt = $db->prepare("UPDATE user_applications SET full_name = ?, phone_number = ?, email_address = ?, birth_date = ?, gender = ?, biography = ? WHERE application_id = ?");
            $stmt->execute([
                htmlspecialchars($_POST['FIO']), htmlspecialchars($_POST['tel']), htmlspecialchars($_POST['email']),
                htmlspecialchars($_POST['DR']), htmlspecialchars($_POST['sex']), htmlspecialchars($_POST['bio']),
                intval($_SESSION['application_id'])
            ]);

            $stmt = $db->prepare("DELETE FROM application_languages WHERE application_id = ?");
            $stmt->execute([intval($_SESSION['application_id'])]);

            if (!empty($_POST['lang']) && is_array($_POST['lang'])) {
                $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
                foreach ($_POST['lang'] as $languageId) {
                    if (is_numeric($languageId)) {
                        $stmt->execute([intval($_SESSION['application_id']), intval($languageId)]);
                    }
                }
            }

            $success = 'Данные успешно обновлены.';
            unset($_SESSION['edit_values'], $_SESSION['edit_errors']);
        } catch (PDOException $e) {
            error_log('Database Error: ' . $e->getMessage());
            $success = null;
            $errors['db'] = 'Произошла ошибка при обновлении данных. Попробуйте позже.';
        }
    }
}

try {
    $db = getDatabaseConnection();

    // Если передан ID через GET, используем его
    if (isset($_GET['id'])) {
        $applicationId = intval($_GET['id']);
    } else {
        // Если ID не передан, пытаемся получить его из сессии
        $stmt = $db->prepare("SELECT application_id FROM users WHERE user_id = ?");
        $stmt->execute([intval($_SESSION['user_id'])]);
        $applicationId = $stmt->fetchColumn();
    }

    if (!$applicationId) {
        die('Ошибка: заявка не найдена.');
    }

    $_SESSION['application_id'] = $applicationId;

    $stmt = $db->prepare("SELECT full_name, phone_number, email_address, birth_date, gender, biography FROM user_applications WHERE application_id = ?");
    $stmt->execute([intval($applicationId)]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die('Ошибка: данные пользователя не найдены.');
    }

    $stmt = $db->prepare("SELECT language_id FROM application_languages WHERE application_id = ?");
    $stmt->execute([intval($applicationId)]);
    $selectedLanguages = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $db->query("SELECT language_id, language_name FROM programming_languages");
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($_SESSION['edit_values'])) {
        $data = array_merge($data, $_SESSION['edit_values']);
        $selectedLanguages = $_SESSION['edit_values']['lang'] ?? $selectedLanguages;
    }
} catch (PDOException $e) {
    // Логируем ошибку вместо отображения пользователю
    error_log('Database error: ' . $e->getMessage());
    die('Ошибка базы данных. Пожалуйста, попробуйте позже.');
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
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="mb-3">
                <label class="form-label">ФИО:</label>
                <input type="text" name="FIO" class="form-control <?= getEditError('FIO') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getEditValue('FIO') ?: htmlspecialchars($data['full_name'])) ?>" maxlength="150" required>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('FIO')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Телефон:</label>
                <input type="tel" name="tel" class="form-control <?= getEditError('tel') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getEditValue('tel') ?: htmlspecialchars($data['phone_number'])) ?>" maxlength="15" required>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('tel')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control <?= getEditError('email') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getEditValue('email') ?: htmlspecialchars($data['email_address'])) ?>" maxlength="80" required>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('email')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Дата рождения:</label>
                <input type="date" name="DR" class="form-control <?= getEditError('DR') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getEditValue('DR') ?: htmlspecialchars($data['birth_date'])) ?>" required>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('DR')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Пол:</label>
                <select name="sex" class="form-select <?= getEditError('sex') ? 'is-invalid' : '' ?>">
                    <option value="0" <?= (getEditValue('sex') ?: htmlspecialchars($data['gender'])) == '0' ? 'selected' : '' ?>>Мужской</option>
                    <option value="1" <?= (getEditValue('sex') ?: htmlspecialchars($data['gender'])) == '1' ? 'selected' : '' ?>>Женский</option>
                </select>
                <div class="invalid-feedback"><?= htmlspecialchars(getEditError('sex')) ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Любимые языки программирования:</label>
                <?php foreach ($languages as $language): ?>
                    <div class="form-check">
                        <input type="checkbox" name="lang[]" value="<?= htmlspecialchars($language['language_id']) ?>" class="form-check-input"
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
                <textarea name="bio" class="form-control <?= getEditError('bio') ? 'is-invalid' : '' ?>" rows="4" maxlength="1000"><?= htmlspecialchars(getEditValue('bio') ?: htmlspecialchars($data['biography'])) ?></textarea>
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
