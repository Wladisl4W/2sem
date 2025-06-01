<?php

function getDatabaseConnection() {
    include("/home/u68762/pass.php"); // Используем абсолютный путь к pass.php

    try {
        return new PDO("mysql:host=localhost;dbname=$dbname", $user, $pass, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        error_log('Database connection error: ' . $e->getMessage());
        die('Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.');
    }
}
?>
