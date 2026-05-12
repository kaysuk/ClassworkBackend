<?php
require_once 'db.php';

initDB();

$pdo = getDB();

$languages = [
    1 => 'Pascal',
    2 => 'C',
    3 => 'C++',
    4 => 'JavaScript',
    5 => 'PHP',
    6 => 'Python',
    7 => 'Java',
    8 => 'Haskel',
    9 => 'Clojure',
    10 => 'Prolog',
    11 => 'Scala',
    12 => 'Go'
];

echo "<h1>Инициализация базы данных</h1>";

try {
    foreach ($languages as $id => $name) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO languages (id, name) VALUES (?, ?)");
        $stmt->execute([$id, $name]);
        echo "<p>✓ " . htmlspecialchars($name) . "</p>";
    }
    echo "<h2>✓ База данных инициализирована</h2>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<style>
body { font-family: Arial; margin: 20px; }
h1 { color: #667eea; }
h2 { color: #4c4; }
p { margin: 5px 0; }
</style>
