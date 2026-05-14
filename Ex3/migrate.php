<?php
require_once 'db.php';

$pdo = getDB();

try {
    echo "<h1>Миграция БД</h1>";
    
    // Проверяем, есть ли уже колонка user_id
    $stmt = $pdo->query("DESCRIBE applications");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('user_id', $columns)) {
        echo "<p>Добавляем колонку user_id...</p>";
        $pdo->exec("ALTER TABLE applications ADD COLUMN user_id INT AFTER id");
        $pdo->exec("ALTER TABLE applications ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
        echo "<p>✓ Колонка user_id добавлена</p>";
    } else {
        echo "<p>Колонка user_id уже существует</p>";
    }
    
    // Проверяем timestamps
    if (!in_array('created_at', $columns)) {
        echo "<p>Добавляем timestamps...</p>";
        $pdo->exec("ALTER TABLE applications ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        $pdo->exec("ALTER TABLE applications ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "<p>✓ Timestamps добавлены</p>";
    } else {
        echo "<p>Timestamps уже существуют</p>";
    }
    
    echo "<h2>✓ Миграция завершена успешно!</h2>";
    echo "<p><a href='form.php'>← Вернуться к форме</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Ошибка миграции:</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<style>
body { font-family: Arial; margin: 20px; }
h1 { color: #667eea; }
h2 { color: #4c4; }
p { margin: 10px 0; }
a { color: #667eea; }
</style>
