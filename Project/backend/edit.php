<?php
session_start();
include("db.php");
include("validation.php");

if (empty($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = validateFormData($_POST);

    if (empty($errors)) {
        try {
            $db = getDatabaseConnection();

            $stmt = $db->prepare("UPDATE user_applications SET full_name = ?, phone_number = ?, email_address = ?, birth_date = ?, gender = ?, biography = ? WHERE application_id = ?");
            $stmt->execute([
                $_POST['FIO'], $_POST['tel'], $_POST['email'], $_POST['DR'], $_POST['sex'], $_POST['bio'], $_SESSION['application_id']
            ]);

            $stmt = $db->prepare("DELETE FROM application_languages WHERE application_id = ?");
            $stmt->execute([$_SESSION['application_id']]);

            if (!empty($_POST['lang']) && is_array($_POST['lang'])) {
                $stmt = $db->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
                foreach ($_POST['lang'] as $languageId) {
                    $stmt->execute([$_SESSION['application_id'], $languageId]);
                }
            }

            $success = 'Данные успешно обновлены.';
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            $errors['db'] = 'Произошла ошибка при обновлении данных. Попробуйте позже.';
        }
    }
}
?>
