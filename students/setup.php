<?php

$host = 'localhost';
$dbname = 'university_db';
$user = 'root';
$pass = '';

try {

    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
    
    echo "База данных '$dbname' создана!<br><br>";
    
    // Таблицы
    $pdo->exec("CREATE TABLE IF NOT EXISTS specialties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        specialty_id INT NOT NULL,
        name VARCHAR(50) NOT NULL,
        capacity INT NOT NULL DEFAULT 25,
        FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        group_id INT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    echo "Таблицы созданы!<br>";
    
    // Проверяем, есть ли данные
    $stmt = $pdo->query("SELECT COUNT(*) FROM specialties");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {

        $pdo->exec("INSERT INTO specialties (name) VALUES 
            ('Программирование'),
            ('Экономика'),
            ('Дизайн'),
            ('Юриспруденция')
        ");
        
        $pdo->exec("INSERT INTO groups (specialty_id, name, capacity) VALUES 
            (1, 'ПР-101', 25),
            (1, 'ПР-102', 25),
            (2, 'ЭК-201', 30),
            (2, 'ЭК-202', 30),
            (3, 'ДЗ-301', 20),
            (4, 'ЮР-401', 25)
        ");
        
        echo "Тестовые данные добавлены!<br><br>";
        echo "<a href='index.php' style='display:inline-block;padding:12px 24px;background:#4CAF50;color:white;text-decoration:none;border-radius:5px;font-size:16px;'>🎓 Перейти на главную</a>";
    } else {
        echo "Данные уже существуют<br><br>";
        echo "<a href='index.php' style='display:inline-block;padding:12px 24px;background:#4CAF50;color:white;text-decoration:none;border-radius:5px;font-size:16px;'>🎓 Перейти на главную</a>";
    }
    
} catch(PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}
?>
?>