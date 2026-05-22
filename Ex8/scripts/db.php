<?php

function getDB() {
    if (!class_exists('PDO')) {
        die('PDO extension is not installed. Установите расширение php-pdo для работы с базой данных.');
    }

    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=u67365',
                'u67365',
                'er5E$53s'
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec('SET NAMES utf8');
        } catch (PDOException $e) {
            die('Ошибка подключения к БД: ' . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}

function initDB() {
    $pdo = getDB();

    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(80) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT,
        agreed TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    // Если нужны таблицы для админов, то создаем их рядом с контактами.
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        login VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $stmt = $pdo->query('SELECT COUNT(*) FROM admins');
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        $passwordHash = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO admins (login, password_hash) VALUES (?, ?)');
        $stmt->execute(['admin', $passwordHash]);
    }
}
