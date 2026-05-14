<?php
require_once 'db.php';
require_once 'auth.php';

try {
    $pdo = getDB();
    
    // Проверим, есть ли админ
    $stmt = $pdo->query("SELECT login FROM admins");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Статус админа:</h2>";
    if (empty($admins)) {
        echo "<p style='color: red;'>❌ Админов не найдено в базе!</p>";
        
        // Создаём
        $passwordHash = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (login, password_hash) VALUES (?, ?)");
        $stmt->execute(['admin', $passwordHash]);
        echo "<p style='color: green;'>✓ Админ создан: login=<strong>admin</strong>, password=<strong>admin</strong></p>";
    } else {
        echo "<p style='color: green;'>✓ Найдены админы:</p>";
        foreach ($admins as $admin) {
            echo "<p>- " . htmlspecialchars($admin['login']) . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>Тест авторизации:</h3>";
    
    // Тест с правильным паролем
    $result = loginAdmin('admin', 'admin');
    if ($result['success']) {
        echo "<p style='color: green;'>✓ Авторизация admin/admin: УСПЕХ</p>";
    } else {
        echo "<p style='color: red;'>❌ Авторизация admin/admin: ОШИБКА - " . htmlspecialchars($result['error']) . "</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='admin.php'>← Вернуться в админ-панель</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
}
