<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo " Проверяю связь с MySQL...<br>";

try {
    // Явно указываем 127.0.0.1 и добавляем таймаут 5 сек
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "✅ Всё ок! Связь с базой есть.";
} catch (PDOException $e) {
    echo "❌ Ошибка подключения: " . $e->getMessage() . "<br>";
    echo "💡 Совет: Проверь, запущен ли MySQL в XAMPP (должен быть зелёным).";
}
?>