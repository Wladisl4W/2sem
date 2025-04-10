<?php
require_once __DIR__.'/session.php';
header('Content-Type: text/html; charset=UTF-8');
require __DIR__.'/../../../pass.php';

// Получение списка заявок, связанных с текущим пользователем
$stmt = $db->prepare("SELECT application_id FROM application_users WHERE user_login=?");
$stmt->execute([$_SESSION['login']]);
$apps = $stmt->fetchAll(PDO::FETCH_NUM);
?>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР5</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #f5f5f5;
        }
        .card {
            background-color: #1e1e1e;
            color: #f5f5f5;
            border: 1px solid #444;
        }
        .card-header {
            background-color: #ff69b4;
            color: #121212;
        }
        .btn-primary {
            background-color: #ff69b4;
            border-color: #ff69b4;
        }
        .btn-primary:hover {
            background-color: #ff85c1;
            border-color: #ff85c1;
        }
    </style>
</head>
<body>
    <?php flash(); ?>
    <div class="container my-5">
        <div class="row text-center justify-content-center">
            <!-- Карточка для создания новой заявки -->
            <div class="card col-3 mx-2 my-2">
                <div class="card-header">Новая заявка</div>
                <div class="card-body">
                    <a href="form.php" class="btn btn-primary">Создать</a>
                </div>
            </div>
            <!-- Карточки для существующих заявок -->
            <?php foreach ($apps as $app): ?>
                <div class="card col-3 mx-2 my-2">
                    <div class="card-header">Заявка №<?= htmlspecialchars($app[0], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="card-body">
                        <a href="form.php?numer=<?= htmlspecialchars($app[0], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary">Изменить</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>