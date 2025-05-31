<?php
function getDatabaseConnection() {
    include("../../../pass.php");

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
