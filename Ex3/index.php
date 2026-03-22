<?php
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=u67365', 'u67365', 'er5E$53s');
    echo "OK";
} catch (PDOException $e) {
    echo $e->getMessage();
}

$errors = [];

// Получаем данные
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$birthdate = $_POST['birthdate'] ?? '';
$gender = $_POST['gender'] ?? '';
$languages = $_POST['languages'] ?? [];
$bio = $_POST['bio'] ?? '';
$contract = isset($_POST['contract']) ? 1 : 0;

# ===================
# ВАЛИДАЦИЯ
# ===================

if (!preg_match("/^[a-zA-Zа-яА-Я\s]{1,150}$/u", $name)) {
    $errors[] = "Некорректное ФИО";
}

if (!preg_match("/^[0-9+\-\s]{5,20}$/", $phone)) {
    $errors[] = "Некорректный телефон";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Некорректный email";
}

if (!$birthdate) {
    $errors[] = "Укажите дату рождения";
}

if (!in_array($gender, ['male', 'female'])) {
    $errors[] = "Выберите пол";
}

if (empty($languages)) {
    $errors[] = "Выберите хотя бы один язык";
}

if (empty($bio)) {
    $errors[] = "Заполните биографию";
}

if (!$contract) {
    $errors[] = "Необходимо согласие с контрактом";
}

# ===================
# ЕСЛИ ЕСТЬ ОШИБКИ
# ===================

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: form.php");
    exit;
}

# ===================
# СОХРАНЕНИЕ
# ===================

try {
    $pdo->beginTransaction();

    // Вставка заявки
    $stmt = $pdo->prepare("
        INSERT INTO applications (name, phone, email, birthdate, gender, bio, contract_agreed)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $phone, $email, $birthdate, $gender, $bio, $contract]);

    $appId = $pdo->lastInsertId();

    // Вставка языков
    $stmt = $pdo->prepare("
        INSERT INTO application_languages (application_id, language_id)
        VALUES (?, ?)
    ");

    foreach ($languages as $lang) {
        $stmt->execute([$appId, $lang]);
    }

    $pdo->commit();

    $_SESSION['success'] = "Данные успешно сохранены!";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['errors'] = ["Ошибка базы данных"];
}

header("Location: form.php");
