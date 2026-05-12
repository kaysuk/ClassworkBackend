<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=u67365', 'u67365', 'er5E$53s');
} catch (PDOException $e) {
    die("Ошибка подключения: " . htmlspecialchars($e->getMessage()));
}

$errors = [];
$errorFields = [];
$validationRules = [
    'name' => [
        'pattern' => "/^[a-zA-Zа-яА-Я\s]{1,150}$/u",
        'message' => "Некорректное ФИО",
        'allowed' => "Допустимо: буквы и пробелы"
    ],
    'phone' => [
        'pattern' => "/^[0-9+\-\s()]{5,20}$/",
        'message' => "Некорректный телефон",
        'allowed' => "Допустимо: цифры, +, -, скобки и пробелы"
    ],
    'email' => [
        'pattern' => "/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
        'message' => "Некорректный email",
        'allowed' => "Формат: name@example.com"
    ],
    'birthdate' => [
        'pattern' => null,
        'message' => "Укажите дату рождения",
        'allowed' => "Формат: YYYY-MM-DD"
    ],
    'bio' => [
        'pattern' => "/^[a-zA-Zа-яА-Я0-9\s.,!?-]{10,500}$/u",
        'message' => "Некорректная биография",
        'allowed' => "Допустимо: буквы, цифры, пробелы, . , ! ? -"
    ]
];

// Получаем данные из POST
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$birthdate = $_POST['birthdate'] ?? '';
$gender = $_POST['gender'] ?? '';
$languages = $_POST['languages'] ?? [];
$bio = $_POST['bio'] ?? '';
$contract = isset($_POST['contract']) ? 1 : 0;

// Проверяем, была ли отправлена форма
if (!empty($_POST)) {
    # ===================
    # ВАЛИДАЦИЯ
    # ===================

    if (empty($name)) {
        $errors[] = "ФИО не заполнено";
        $errorFields['name'] = $validationRules['name']['allowed'];
    } elseif (!preg_match($validationRules['name']['pattern'], $name)) {
        $errors[] = $validationRules['name']['message'] . ". " . $validationRules['name']['allowed'];
        $errorFields['name'] = $validationRules['name']['allowed'];
    }

    if (empty($phone)) {
        $errors[] = "Телефон не заполнен";
        $errorFields['phone'] = $validationRules['phone']['allowed'];
    } elseif (!preg_match($validationRules['phone']['pattern'], $phone)) {
        $errors[] = $validationRules['phone']['message'] . ". " . $validationRules['phone']['allowed'];
        $errorFields['phone'] = $validationRules['phone']['allowed'];
    }

    if (empty($email)) {
        $errors[] = "Email не заполнен";
        $errorFields['email'] = $validationRules['email']['allowed'];
    } elseif (!preg_match($validationRules['email']['pattern'], $email)) {
        $errors[] = $validationRules['email']['message'] . ". " . $validationRules['email']['allowed'];
        $errorFields['email'] = $validationRules['email']['allowed'];
    }

    if (!$birthdate) {
        $errors[] = $validationRules['birthdate']['message'];
        $errorFields['birthdate'] = $validationRules['birthdate']['allowed'];
    }

    if (!in_array($gender, ['male', 'female'])) {
        $errors[] = "Выберите пол";
        $errorFields['gender'] = "required";
    }

    if (empty($languages)) {
        $errors[] = "Выберите хотя бы один язык";
        $errorFields['languages'] = "required";
    }

    if (empty($bio)) {
        $errors[] = "Заполните биографию";
        $errorFields['bio'] = $validationRules['bio']['allowed'];
    } elseif (!preg_match($validationRules['bio']['pattern'], $bio)) {
        $errors[] = $validationRules['bio']['message'] . ". " . $validationRules['bio']['allowed'];
        $errorFields['bio'] = $validationRules['bio']['allowed'];
    }

    if (!$contract) {
        $errors[] = "Необходимо согласие с контрактом";
        $errorFields['contract'] = "required";
    }

    # ===================
    # ЕСЛИ ЕСТЬ ОШИБКИ
    # ===================

    if (!empty($errors)) {
        // Сохраняем ошибки и введенные значения в Cookies до конца сессии
        setcookie("form_errors", json_encode($errors), 0, "/");
        setcookie("form_error_fields", json_encode($errorFields), 0, "/");
        setcookie("form_name", $name, 0, "/");
        setcookie("form_phone", $phone, 0, "/");
        setcookie("form_email", $email, 0, "/");
        setcookie("form_birthdate", $birthdate, 0, "/");
        setcookie("form_gender", $gender, 0, "/");
        setcookie("form_bio", $bio, 0, "/");
        setcookie("form_languages", json_encode($languages), 0, "/");
        
        header("Location: form.php");
        exit;
    }

    # ===================
    # СОХРАНЕНИЕ
    # ===================

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO applications (name, phone, email, birthdate, gender, bio, contract_agreed)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $phone, $email, $birthdate, $gender, $bio, $contract]);

        $appId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
            INSERT INTO application_languages (application_id, language_id)
            VALUES (?, ?)
        ");

        foreach ($languages as $lang) {
            $stmt->execute([$appId, $lang]);
        }

        $pdo->commit();

        // Сохраняем успешно введенные данные на 1 год
        $oneYearExpiry = time() + (365 * 24 * 60 * 60);
        setcookie("form_name", $name, $oneYearExpiry, "/");
        setcookie("form_phone", $phone, $oneYearExpiry, "/");
        setcookie("form_email", $email, $oneYearExpiry, "/");
        setcookie("form_birthdate", $birthdate, $oneYearExpiry, "/");
        setcookie("form_gender", $gender, $oneYearExpiry, "/");
        setcookie("form_bio", $bio, $oneYearExpiry, "/");
        setcookie("form_languages", json_encode($languages), $oneYearExpiry, "/");

        // Удаляем Cookies с ошибками
        setcookie("form_errors", "", time() - 3600, "/");
        setcookie("form_error_fields", "", time() - 3600, "/");

        // Редирект с параметром успеха
        header("Location: form.php?success=1");
        exit;
    } catch (Exception $e) {
        $errors[] = "Ошибка базы данных: " . htmlspecialchars($e->getMessage());
        setcookie("form_errors", json_encode($errors), 0, "/");
        header("Location: form.php");
        exit;
    }
}
