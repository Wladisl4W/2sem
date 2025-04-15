<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

function getValue($name) {
    return $_SESSION['values'][$name] ?? '';
}

function getError($name) {
    return $_SESSION['errors'][$name] ?? '';
}

$languages = isset($_SESSION['values']['lang']) ? $_SESSION['values']['lang'] : [];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212; /* Чуть светлее фон сайта */
            color: #ffffff;
            padding-top: 0; /* Убрали отступ сверху */
            padding-bottom: 50px;
        }
        .container {
            max-width: 600px;
        }
        .form-control, .form-select, .form-check-input {
            background-color: #2a2a2a; /* Чуть светлее ячейки формы */
            color: #ffffff;
            border: 1px solid #444;
        }
        .form-control:focus, .form-select:focus {
            border-color: #e91e63; /* Розовый акцент */
            box-shadow: 0 0 5px #e91e63;
        }
        .form-check-input:checked {
            background-color: #e91e63;
            border-color: #e91e63;
        }
        .btn-custom {
            background-color: #e91e63;
            color: #ffffff;
            border: none;
        }
        .btn-custom:hover {
            background-color: #c2185b;
        }
        .alert {
            background-color: #333;
            color: #ff4d4d;
            border: 1px solid #ff4d4d;
        }
        .bg-dark {
            background-color: #1e1e1e !important; /* Фон формы без оттенков */
        }
        .header-box {
            background-color: #e91e63; /* Розовый фон */
            color: #ffffff; /* Белый текст */
            text-align: center; /* Центрирование текста */
            padding: 10px 0; /* Отступы сверху и снизу */
            border-radius: 5px; /* Закругленные углы */
            margin-bottom: 20px; /* Отступ снизу */
            width: 100%; /* Растягиваем на всю ширину */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="header-box">Регистрация</div>

        <form action="register.php" method="post" class="p-4 border rounded bg-dark">
            <div class="mb-3">
                <label class="form-label">ФИО:</label>
                <input type="text" name="FIO" class="form-control <?= getError('FIO') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getValue('FIO')) ?>">
                <div class="invalid-feedback"><?= htmlspecialchars(getError('FIO')) ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Телефон:</label>
                <input type="tel" name="tel" class="form-control <?= getError('tel') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getValue('tel')) ?>">
                <div class="invalid-feedback"><?= htmlspecialchars(getError('tel')) ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control <?= getError('email') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getValue('email')) ?>">
                <div class="invalid-feedback"><?= htmlspecialchars(getError('email')) ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Дата рождения:</label>
                <input type="date" name="DR" class="form-control <?= getError('DR') ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars(getValue('DR')) ?>">
                <div class="invalid-feedback"><?= htmlspecialchars(getError('DR')) ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Пол:</label>
                <select name="sex" class="form-select <?= getError('sex') ? 'is-invalid' : '' ?>">
                    <option value="0" <?= getValue('sex') == '0' ? 'selected' : '' ?>>Мужской</option>
                    <option value="1" <?= getValue('sex') == '1' ? 'selected' : '' ?>>Женский</option>
                </select>
                <div class="invalid-feedback"><?= htmlspecialchars(getError('sex')) ?></div>
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
                        <label class="form-check-label"><?= $lang ?></label>
                    </div>
                <?php endforeach; ?>
                <?php if (getError('lang')): ?>
                    <div class="text-danger"><?= htmlspecialchars(getError('lang')) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Биография:</label>
                <textarea name="bio" class="form-control <?= getError('bio') ? 'is-invalid' : '' ?>" rows="4"><?= htmlspecialchars(getValue('bio')) ?></textarea>
                <div class="invalid-feedback"><?= htmlspecialchars(getError('bio')) ?></div>
            </div>

            <button type="submit" class="btn btn-custom w-100">Отправить</button>
        </form>
    </div>
    <?php
    // Очищаем ошибки и значения после отображения
    unset($_SESSION['errors'], $_SESSION['values']);
    ?>
</body>
</html>
