<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

function getValue($name) {
    return htmlspecialchars($_SESSION['values'][$name] ?? '');
}

function getError($name) {
    return htmlspecialchars($_SESSION['errors'][$name] ?? '');
}

$languages = isset($_SESSION['values']['lang']) ? $_SESSION['values']['lang'] : [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-wide">
        <div class="header-box">Регистрация</div>

        <form action="register.php" method="post" class="p-4 border rounded bg-dark">
            <div class="mb-3">
                <label class="form-label">ФИО:</label>
                <input type="text" name="FIO" class="form-control <?= getError('FIO') ? 'is-invalid' : '' ?>" value="<?= getValue('FIO') ?>" maxlength="150">
                <div class="invalid-feedback"><?= getError('FIO') ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Телефон:</label>
                <input type="tel" name="tel" class="form-control <?= getError('tel') ? 'is-invalid' : '' ?>" value="<?= getValue('tel') ?>" maxlength="15">
                <div class="invalid-feedback"><?= getError('tel') ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control <?= getError('email') ? 'is-invalid' : '' ?>" value="<?= getValue('email') ?>" maxlength="80">
                <div class="invalid-feedback"><?= getError('email') ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Дата рождения:</label>
                <input type="date" name="DR" class="form-control <?= getError('DR') ? 'is-invalid' : '' ?>" value="<?= getValue('DR') ?>">
                <div class="invalid-feedback"><?= getError('DR') ?></div>
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
                    5 => 'PHP', 6 => 'Python', 7 => 'Java', 8 => 'Haskell',
                    9 => 'Clojure', 10 => 'Prolog', 11 => 'Scala'
                ];
                foreach ($langs as $index => $lang):
                ?>
                    <div class="form-check">
                        <input type="checkbox" name="lang[]" value="<?= $index ?>" class="form-check-input"
                            <?= in_array((string)$index, $languages) ? 'checked' : '' ?>>
                        <label class="form-check-label"><?= htmlspecialchars($lang) ?></label>
                    </div>
                <?php endforeach; ?>
                <?php if (getError('lang')): ?>
                    <div class="text-danger"><?= getError('lang') ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Биография:</label>
                <textarea name="bio" class="form-control <?= getError('bio') ? 'is-invalid' : '' ?>" rows="4" maxlength="1000"><?= getValue('bio') ?></textarea>
                <div class="invalid-feedback"><?= getError('bio') ?></div>
            </div>

            <button type="submit" class="btn btn-custom w-100">Отправить</button>
        </form>
    </div>
    <?php
    unset($_SESSION['errors'], $_SESSION['values']);
    ?>
</body>
</html>
