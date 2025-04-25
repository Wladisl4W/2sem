<?php
session_start();

// Проверяем CSRF-токен
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Ошибка CSRF: недействительный токен.');
    }

    // Проверяем, был ли файл загружен
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        die('Ошибка загрузки файла.');
    }

    $file = $_FILES['file'];

    // Ограничиваем типы файлов
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    $fileMimeType = mime_content_type($file['tmp_name']);
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($fileMimeType, $allowedMimeTypes) || !in_array($fileExtension, $allowedExtensions)) {
        die('Недопустимый тип файла.');
    }

    // Генерируем уникальное имя файла
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFileName = uniqid('upload_', true) . '.' . $fileExtension;
    $destination = $uploadDir . $newFileName;

    // Перемещаем файл
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        die('Ошибка сохранения файла.');
    }

    echo 'Файл успешно загружен.';
}
?>
