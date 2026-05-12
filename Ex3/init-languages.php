<?php
require_once 'db.php';

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

foreach ($languages as $id => $name) {
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO languages (id, name) VALUES (?, ?)");
        $stmt->execute([$id, $name]);
    } catch (Exception $e) {
        // Already inserted
    }
}

echo "Languages initialized successfully\n";
