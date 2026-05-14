<?php

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=localhost;dbname=u67365',
                'u67365',
                'er5E$53s'
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения к БД: " . htmlspecialchars($e->getMessage()));
        }
    }
    return $pdo;
}

function initDB() {
    $pdo = getDB();
    
    // Таблица приложений (основная)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            name VARCHAR(150) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            email VARCHAR(100) NOT NULL,
            birthdate DATE NOT NULL,
            gender VARCHAR(10) NOT NULL,
            bio TEXT NOT NULL,
            contract_agreed TINYINT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        )
    ");
    
    // Таблица языков
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS languages (
            id INT PRIMARY KEY,
            name VARCHAR(100) NOT NULL
        )
    ");
    
    // Таблица связи приложений и языков
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS application_languages (
            application_id INT NOT NULL,
            language_id INT NOT NULL,
            PRIMARY KEY (application_id, language_id),
            FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
            FOREIGN KEY (language_id) REFERENCES languages(id)
        )
    ");
    
    // Таблица пользователей - НОВАЯ
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            login VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Таблица администраторов
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            login VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Инициализируем админа по умолчанию (если его ещё нет)
    initializeDefaultAdmin();
}

function initializeDefaultAdmin() {
    $pdo = getDB();
    
    // Проверяем, есть ли уже хотя бы один админ
    $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Создаём дефолтного админа: логин = admin, пароль = admin
        $passwordHash = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (login, password_hash) VALUES (?, ?)");
        $stmt->execute(['admin', $passwordHash]);
    }
}
