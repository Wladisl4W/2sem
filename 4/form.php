<?php
header('Content-Type: text/html; charset=UTF-8');

function getValue($name) {
    return $_COOKIE[$name . '_value'] ?? '';
}

function getError($name) {
    return $_COOKIE[$name . '_error'] ?? '';
}

$languages = isset($_COOKIE['lang_value']) ? explode('|', $_COOKIE['lang_value']) : [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма регистрации</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <div class="container mt-5">
        <h1 class="text-center text-warning">Регистрация</h1>

        <?php if (!empty($_COOKIE['save'])): ?>
            <div class="alert alert-success"><?= $_COOKIE['save'] ?></div>
            <?php setcookie('save', '', time() - 3600, "/"); ?>
        <?php endif; ?>

        <form action="index.php" method="post" class="p-4 border rounded bg-secondary">
            <!-- ФИО -->
            <div class="mb-3">
                <label class="form-label">ФИО:</label>
                <input type="text" name="FIO" class="form-control <?= getError('fio') ? 'is-invalid' : '' ?>" value="<?= getValue('fio') ?>">
                <div class="invalid-feedback"><?= getError('fio') ?></div>
            </div>

            <!-- Телефон -->
            <div class="mb-3">
                <label class="form-label">Телефон:</label>
                <input type="tel" name="tel" class="form-control <?= getError('tel') ? 'is-invalid' : '' ?>" value="<?= getValue('tel') ?>">
                <div class="invalid-feedback"><?= getError('tel') ?></div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control <?= getError('email') ? 'is-invalid' : '' ?>" value="<?= getValue('email') ?>">
                <div class="invalid-feedback"><?= getError('email') ?></div>
            </div>

            <!-- Дата рождения -->
            <div class="mb-3">
                <label class="form-label">Дата рождения:</label>
                <input type="date" name="DR" class="form-control <?= getError('dr') ? 'is-invalid' : '' ?>" value="<?= getValue('dr') ?>">
                <div class="invalid-feedback"><?= getError('dr') ?></div>
            </div>

            <!-- Пол -->
            <div class="mb-3">
                <label class="form-label">Пол:</label>
                <select name="sex" class="form-select <?= getError('sex') ? 'is-invalid' : '' ?>">
                    <option value="0" <?= getValue('sex') == '0' ? 'selected' : '' ?>>Мужской</option>
                    <option value="1" <?= getValue('sex') == '1' ? 'selected' : '' ?>>Женский</option>
                </select>
                <div class="invalid-feedback"><?= getError('sex') ?></div>
            </div>

            <!-- Языки программирования -->
            <div class="mb-3">
                <label class="form-label">Любимые языки программирования:</label>
                <?php
                $langs = ['C++', 'Python', 'JavaScript', 'PHP', 'Java'];
                foreach ($langs as $index => $lang):
                ?>
                    <div class="form-check">
                        <input type="checkbox" name="lang[]" value="<?= $index + 1 ?>" class="form-check-input"
                            <?= in_array((string)($index + 1), $languages) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $lang ?></label>
                    </div>
                <?php endforeach; ?>
                <?php if (getError('lang')): ?>
                    <div class="text-danger"><?= getError('lang') ?></div>
                <?php endif; ?>
            </div>

            <!-- Биография -->
            <div class="mb-3">
                <label class="form-label">Биография:</label>
                <textarea name="bio" class="form-control <?= getError('bio') ? 'is-invalid' : '' ?>" rows="4"><?= getValue('bio') ?></textarea>
                <div class="invalid-feedback"><?= getError('bio') ?></div>
            </div>

            <!-- Кнопка отправки -->
            <button type="submit" class="btn btn-warning">Отправить</button>
        </form>
    </div>
</body>
</html>
