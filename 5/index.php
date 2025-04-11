<?php
require_once __DIR__.'/session.php';
header('Content-Type: text/html; charset=UTF-8');
require __DIR__.'/../../../pass.php';
$stmt=$db->prepare("SELECT id_app FROM app_users WHERE id_user=?");
$stmt->execute([$_SESSION['login']]);
$apps=$stmt->fetchAll(PDO::FETCH_NUM);
?>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР5</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <?php flash();?>
    <div class="d-flex row gx-0 text-center w-75 mx-auto my-5 justify-content-evenly">
        <div class="card col-2 mx-1 my-2">
            <div class="card-header">Новая таблица</div>
            <div class="card-body">
                <a href="form.php" class="btn btn-primary">Создать</a>
            </div>
            </div>
    <?php
    foreach ($apps as $app){
        print('
        <div class="card col-2 mx-1 my-2">
        <div class="card-header">Таблица '.$app[0].'</div>
        <div class="card-body">
            <a href="form.php?numer='.$app[0].'" class="btn btn-primary">Изменить</a>
            </div>
        </div>');
    }
    ?>
    </div>
</body>
</html>