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
    <style>
        body {
            background-color: #1a1a1a;
            color: #ff66b2;
        }
        .container {
            max-width: 600px;
        }
        .form-control, .form-select, .form-check-input {
            background-color: #333;
            color: #ff66b2;
            border: 1px solid #ff66b2;
        }
        .form-control:focus, .form-select:focus {
            border-color: #ff99cc;
            box-shadow: 0 0 5px #ff99cc;
        }
        .form-check-input:checked {
            background-color: #ff66b2;
            border-color: #ff66b2;
        }
        .btn-custom {
            background-color: #ff66b2;
            color: #1a1a1a;
            border: none;
        }
        .btn-custom:hover {
            background-color: #ff99cc;
        }
        .alert {
            color: #1a1a1a;
            background-color: #ff99cc;
            border-color: #ff66b2;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Регистрация</h1>

        <?php if (!empty($_COOKIE['save'])): ?>
            <div class="alert alert-success text-center"><?= $_COOKIE['save'] ?></div>
            <?php setcookie('save', '', time() - 3600, "/"); ?>
        <?php endif; ?>

        <form action="index.php" method="post" class="p-4 border rounded bg-dark">
            <div class="mb-3">
                <label class="form-label">ФИО:</label>
                <input type="text" name="FIO" class="form-control <?= getError('fio') ? 'is-invalid' : '' ?>" value="<?= getValue('fio') ?>">
                <div class="invalid-feedback"><?= getError('fio') ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Телефон:</label>
                <input type="tel" name="tel" class="form-control <?= getError('tel') ? 'is-invalid' : '' ?>" value="<?= getValue('tel') ?>">
                <div class="invalid-feedback"><?= getError('tel') ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control <?= getError('email') ? 'is-invalid' : '' ?>" value="<?= getValue('email') ?>">
                <div class="invalid-feedback"><?= getError('email') ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Дата рождения:</label>
                <input type="date" name="DR" class="form-control <?= getError('dr') ? 'is-invalid' : '' ?>" value="<?= getValue('dr') ?>">
                <div class="invalid-feedback"><?= getError('dr') ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Пол:</label>
                <select name="sex" class="form-select <?= getError('sex') ? 'is-invalid' : '' ?>">
                    <option value="0" <?= getValue('sex') == '0' ? 'selected' : '' ?>>Мужской</option>
                    <option value="1" <?= getValue('sex') == '1' ? 'selected' : '' ?>>Женский</option>
                </select>
                <div class="invalid-feedback"><?= getError('sex') ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Любимые языки программирования:</label>
                <?php
                $langs = [
                    1 => 'Pascal', 2 => 'C', 3 => 'C++', 4 => 'JavaScript',
                    5 => 'PHP', 6 => 'Python', 7 => 'Java', 8 => 'Haskel',
                    9 => 'Clojure', 10 => 'Prolog', 11 => 'Scala'
                ];
                foreach ($langs as $index => $lang):
                ?>
                    <div class="form-check">
                        <input type="checkbox" name="lang[]" value="<?= $index ?>" class="form-check-input"
                            <?= in_array((string)$index, $languages) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= $lang ?></label>
                    </div>
                <?php endforeach; ?>
                <?php if (getError('lang')): ?>
                    <div class="text-danger"><?= getError('lang') ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Биография:</label>
                <textarea name="bio" class="form-control <?= getError('bio') ? 'is-invalid' : '' ?>" rows="4"><?= getValue('bio') ?></textarea>
                <div class="invalid-feedback"><?= getError('bio') ?></div>
            </div>

            <button type="submit" class="btn btn-custom w-100">Отправить</button>
        </form>
    </div>
</body>
</html>
