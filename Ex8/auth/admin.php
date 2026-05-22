<?php
require_once __DIR__ . '/../scripts/db.php';

function getAdminHttpAuth() {
    // Проверяем HTTP Basic Auth из $_SERVER переменных
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        return [
            'login' => $_SERVER['PHP_AUTH_USER'],
            'password' => $_SERVER['PHP_AUTH_PW']
        ];
    }
    
    // Альтернативный способ для других конфигураций (nginx, некоторые Apache)
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        if (strpos($_SERVER['HTTP_AUTHORIZATION'], 'Basic') === 0) {
            $credentials = base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6));
            if (strpos($credentials, ':') !== false) {
                list($login, $password) = explode(':', $credentials, 2);
                return [
                    'login' => $login,
                    'password' => $password
                ];
            }
        }
    }
    
    return null;
}

function loginAdmin($login, $password) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, login, password_hash FROM admins WHERE login = ?");
    $stmt->execute([$login]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        return ['success' => false, 'error' => 'Неверный логин или пароль'];
    }
    
    if (!password_verify($password, $admin['password_hash'])) {
        return ['success' => false, 'error' => 'Неверный логин или пароль'];
    }
    
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_login'] = $admin['login'];
    
    return ['success' => true, 'admin_id' => $admin['id']];
}

function auth($request, $route) {
    // Проверка HTTP Basic Auth.
    $credentials = getAdminHttpAuth();

    if (!$credentials) {
        return array(
            'headers' => array(
                'WWW-Authenticate: Basic realm="Admin Area"',
                'HTTP/1.0 401 Unauthorized'
            ),
            'entity' => 'Требуется авторизация для доступа к административной части.'
        );
    }

    $result = loginAdmin($credentials['login'], $credentials['password']);
    if (!$result['success']) {
        return array(
            'headers' => array(
                'WWW-Authenticate: Basic realm="Admin Area"',
                'HTTP/1.0 401 Unauthorized'
            ),
            'entity' => 'Неверные учетные данные администратора.'
        );
    }

    return false;
}
