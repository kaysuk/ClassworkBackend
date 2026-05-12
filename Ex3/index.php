<?php
session_start();

require_once 'db.php';
require_once 'auth.php';
require_once 'validation.php';

initDB();

$action = $_GET['action'] ?? 'register';
$isLoggedIn = isLoggedIn();

// =================== РЕГИСТРАЦИЯ ===================
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST' && !$isLoggedIn) {
    $data = [
        'name' => $_POST['name'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'email' => $_POST['email'] ?? '',
        'birthdate' => $_POST['birthdate'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'languages' => $_POST['languages'] ?? [],
        'bio' => $_POST['bio'] ?? '',
        'contract' => isset($_POST['contract']) ? 1 : 0
    ];
    
    $validation = validateFormData($data);
    
    if (!$validation['valid']) {
        setcookie("form_errors", json_encode($validation['errors']), 0, "/");
        setcookie("form_error_fields", json_encode($validation['errorFields']), 0, "/");
        setcookie("form_name", $data['name'], 0, "/");
        setcookie("form_phone", $data['phone'], 0, "/");
        setcookie("form_email", $data['email'], 0, "/");
        setcookie("form_birthdate", $data['birthdate'], 0, "/");
        setcookie("form_gender", $data['gender'], 0, "/");
        setcookie("form_bio", $data['bio'], 0, "/");
        setcookie("form_languages", json_encode($data['languages']), 0, "/");
        
        header("Location: form.php");
        exit;
    }
    
    try {
        $pdo = getDB();
        $pdo->beginTransaction();
        
        // Генерируем учетные данные
        $creds = generateCredentials();
        
        // Создаем пользователя
        $userId = createUser($creds['login'], $creds['password']);
        
        // Сохраняем приложение
        $stmt = $pdo->prepare("
            INSERT INTO applications (user_id, name, phone, email, birthdate, gender, bio, contract_agreed)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['birthdate'],
            $data['gender'],
            $data['bio'],
            $data['contract']
        ]);
        
        $appId = $pdo->lastInsertId();
        
        // Сохраняем языки
        $stmt = $pdo->prepare("
            INSERT INTO application_languages (application_id, language_id)
            VALUES (?, ?)
        ");
        foreach ($data['languages'] as $langId) {
            $stmt->execute([$appId, $langId]);
        }
        
        $pdo->commit();
        
        // Сохраняем учетные данные в сессию для отображения
        $_SESSION['new_credentials'] = [
            'login' => $creds['login'],
            'password' => $creds['password']
        ];
        $_SESSION['new_app_id'] = $appId;
        
        // Сохраняем значения в Cookies на 1 год
        $oneYearExpiry = time() + (365 * 24 * 60 * 60);
        setcookie("form_name", $data['name'], $oneYearExpiry, "/");
        setcookie("form_phone", $data['phone'], $oneYearExpiry, "/");
        setcookie("form_email", $data['email'], $oneYearExpiry, "/");
        setcookie("form_birthdate", $data['birthdate'], $oneYearExpiry, "/");
        setcookie("form_gender", $data['gender'], $oneYearExpiry, "/");
        setcookie("form_bio", $data['bio'], $oneYearExpiry, "/");
        setcookie("form_languages", json_encode($data['languages']), $oneYearExpiry, "/");
        
        setcookie("form_errors", "", time() - 3600, "/");
        setcookie("form_error_fields", "", time() - 3600, "/");
        
        header("Location: form.php?show_creds=1");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        setcookie("form_errors", json_encode(["Ошибка: " . $e->getMessage()]), 0, "/");
        header("Location: form.php");
        exit;
    }
}

// =================== ЛОГИН ===================
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        setcookie("login_error", "Введите логин и пароль", 0, "/");
        header("Location: login.php");
        exit;
    }
    
    $result = login($login, $password);
    
    if (!$result['success']) {
        setcookie("login_error", $result['error'], 0, "/");
        header("Location: login.php");
        exit;
    }
    
    setcookie("login_error", "", time() - 3600, "/");
    header("Location: account.php");
    exit;
}

// =================== ВЫХОД ===================
if ($action === 'logout') {
    logout();
    header("Location: form.php");
    exit;
}

// =================== РЕДАКТИРОВАНИЕ ===================
if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST' && $isLoggedIn) {
    $appId = $_POST['app_id'] ?? null;
    
    if (!$appId) {
        header("Location: account.php");
        exit;
    }
    
    $data = [
        'name' => $_POST['name'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'email' => $_POST['email'] ?? '',
        'birthdate' => $_POST['birthdate'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'languages' => $_POST['languages'] ?? [],
        'bio' => $_POST['bio'] ?? '',
        'contract' => isset($_POST['contract']) ? 1 : 0
    ];
    
    $validation = validateFormData($data);
    
    if (!$validation['valid']) {
        $_SESSION['edit_errors'] = $validation['errors'];
        $_SESSION['edit_error_fields'] = $validation['errorFields'];
        $_SESSION['edit_app_id'] = $appId;
        header("Location: account.php?edit_app=" . $appId);
        exit;
    }
    
    require_once 'auth.php';
    $result = updateApplication($appId, $data, getCurrentUserId());
    
    if (!$result['success']) {
        $_SESSION['edit_errors'] = [$result['error']];
        $_SESSION['edit_app_id'] = $appId;
    } else {
        $_SESSION['edit_success'] = true;
    }
    
    header("Location: account.php?edit_app=" . $appId);
    exit;
}

// Безопасный выход для неожиданных действий
header("Location: form.php");
exit;
