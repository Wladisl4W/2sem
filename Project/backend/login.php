<?php
header('Content-Type: application/json');
session_start();
include("../../db.php");

$input = json_decode(file_get_contents('php://input'), true);

if (empty($input['login']) || empty($input['password'])) {
    echo json_encode(['success' => false, 'message' => 'Логин и пароль обязательны.']);
    exit();
}

try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT * FROM user_applications WHERE user_login = ?");
    $stmt->execute([$input['login']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($input['password'], $user['password_hash'])) {
        $_SESSION['user_id'] = $user['application_id'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Неверный логин или пароль.']);
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ошибка сервера.']);
}
?>
