<?php
function validateFormData($data) {
    $errors = [];

    // Валидация ФИО
    if (empty($data['FIO']) || strlen($data['FIO']) > 150) {
        $errors['FIO'] = 'ФИО не должно быть пустым и не должно превышать 150 символов.';
    } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u', $data['FIO'])) {
        $errors['FIO'] = 'ФИО должно содержать только буквы, пробелы и дефисы.';
    }

    // Валидация телефона
    if (empty($data['tel']) || !preg_match('/^\+?[0-9]{10,15}$/', $data['tel'])) {
        $errors['tel'] = 'Введите корректный номер телефона (только цифры, от 10 до 15 символов).';
    }

    // Валидация email
    if (empty($data['email']) || !preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $data['email'])) {
        $errors['email'] = 'Введите корректный email.';
    }

    // Валидация даты рождения
    if (empty($data['DR']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['DR'])) {
        $errors['DR'] = 'Введите корректную дату рождения.';
    } else {
        $birthDate = strtotime($data['DR']);
        $minDate = strtotime('1900-01-01');
        $currentDate = time();

        if ($birthDate < $minDate) {
            $errors['DR'] = 'Дата рождения не может быть раньше 1900 года.';
        } elseif ($birthDate > $currentDate) {
            $errors['DR'] = 'Дата рождения не может быть в будущем.';
        }
    }

    // Валидация пола
    if (!isset($data['sex']) || !in_array($data['sex'], ['0', '1'])) {
        $errors['sex'] = 'Выберите корректный пол.';
    }

    // Валидация языков программирования
    if (empty($data['lang']) || !is_array($data['lang'])) {
        $errors['lang'] = 'Выберите хотя бы один язык программирования.';
    }

    // Валидация биографии
    if (empty($data['bio']) || strlen($data['bio']) > 1000) {
        $errors['bio'] = 'Биография не должна быть пустой и не должна превышать 1000 символов.';
    }

    return $errors;
}
?>
