<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();
include("../../db.php");
include("../../validation.php");

function parseInputData() {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $input = file_get_contents('php://input');

    if (stripos($contentType, 'application/json') !== false) {
        return json_decode($input, true);
    } elseif (stripos($contentType, 'application/xml') !== false) {
        $xml = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
        return json_decode(json_encode($xml), true);
    }

    return $_POST;
}

$data = parseInputData();
$errors = validateFormData($data);

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'errors' => $errors]);
    exit();
}

try {
    $db = getDatabaseConnection();

    $login = 'user_' . bin2hex(random_bytes(4));
    $password = bin2hex(random_bytes(4));
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO user_applications (full_name, phone_number, email_address, birth_date, gender, biography, user_login, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['FIO'], $data['tel'], $data['email'], $data['DR'], $data['sex'], $data['bio'], $login, $passwordHash
    ]);

    $applicationId = $db->lastInsertId();

    $stmt = $db->prepare("INSERT INTO users (user_login, password_hash, application_id) VALUES (?, ?, ?)");
    $stmt->execute([$login, $passwordHash, $applicationId]);

    $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
    foreach ($data['lang'] as $languageId) {
        $stmt->execute([$applicationId, $languageId]);
    }

    http_response_code(201);
    echo json_encode([
        'status' => 'success',
        'login' => $login,
        'password' => $password,
        'profile_url' => "/profile.php?id=$applicationId"
    ]);
    exit();
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Ошибка базы данных. Пожалуйста, попробуйте позже.']);
    exit();
}
?>
