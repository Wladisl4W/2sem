<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Загрузка файла</title>
</head>
<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <label for="file">Выберите файл:</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">Загрузить</button>
    </form>
</body>
</html>
