<?php

require_once 'db.php';

function generateCredentials() {
    // Генерируем уникальный login (login_XXXXXX)
    $login = 'user_' . bin2hex(random_bytes(8));
    
    // Генерируем пароль (16 символов с буквами, цифрами, спецсимволами)
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    $password = '';
    for ($i = 0; $i < 16; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    
    return ['login' => $login, 'password' => $password];
}

function createUser($login, $password) {
    $pdo = getDB();
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (login, password_hash) VALUES (?, ?)");
    $stmt->execute([$login, $passwordHash]);
    
    return $pdo->lastInsertId();
}

function findUserByLogin($login) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, login, password_hash FROM users WHERE login = ?");
    $stmt->execute([$login]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function login($login, $password) {
    $user = findUserByLogin($login);
    
    if (!$user) {
        return ['success' => false, 'error' => 'Неверный логин или пароль'];
    }
    
    if (!verifyPassword($password, $user['password_hash'])) {
        return ['success' => false, 'error' => 'Неверный логин или пароль'];
    }
    
    // Устанавливаем сессию
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_login'] = $user['login'];
    
    return ['success' => true, 'user_id' => $user['id']];
}

function logout() {
    $_SESSION = [];
    session_destroy();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserApplications($userId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getApplicationWithLanguages($appId) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
    $stmt->execute([$appId]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$app) return null;
    
    $stmt = $pdo->prepare("
        SELECT language_id FROM application_languages WHERE application_id = ?
    ");
    $stmt->execute([$appId]);
    $languages = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $app['languages'] = $languages;
    return $app;
}

function updateApplication($appId, $data, $userId) {
    $pdo = getDB();
    
    // Проверяем, что приложение принадлежит пользователю
    $stmt = $pdo->prepare("SELECT user_id FROM applications WHERE id = ?");
    $stmt->execute([$appId]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$app || $app['user_id'] != $userId) {
        return ['success' => false, 'error' => 'Нет доступа'];
    }
    
    try {
        $pdo->beginTransaction();
        
        // Обновляем основные данные
        $stmt = $pdo->prepare("
            UPDATE applications 
            SET name = ?, phone = ?, email = ?, birthdate = ?, gender = ?, bio = ?, contract_agreed = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['birthdate'],
            $data['gender'],
            $data['bio'],
            $data['contract'] ? 1 : 0,
            $appId
        ]);
        
        // Удаляем старые языки
        $stmt = $pdo->prepare("DELETE FROM application_languages WHERE application_id = ?");
        $stmt->execute([$appId]);
        
        // Добавляем новые языки
        $stmt = $pdo->prepare("
            INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)
        ");
        
        foreach ($data['languages'] as $langId) {
            $stmt->execute([$appId, $langId]);
        }
        
        $pdo->commit();
        return ['success' => true];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
