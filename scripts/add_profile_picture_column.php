<?php
$db = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $db);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN profile_picture VARCHAR;");
    echo "Added profile_picture column.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
