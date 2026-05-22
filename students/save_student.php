<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$group_id = intval($_POST['group_id'] ?? 0);
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (!$group_id || !$full_name || !$email) {
    die("Заполните все обязательные поля");
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=university_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Проверяем наличие мест
    $stmt = $pdo->prepare("
        SELECT g.name, g.capacity, COUNT(s.id) as enrolled
        FROM groups g
        LEFT JOIN students s ON g.id = s.group_id
        WHERE g.id = ?
        GROUP BY g.id
    ");
    $stmt->execute([$group_id]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$group || ($group['capacity'] - $group['enrolled']) <= 0) {
        die("К сожалению, мест в группе больше нет!");
    }
    
    // Сохраняем студента
    $stmt = $pdo->prepare("
        INSERT INTO students (full_name, email, phone, group_id)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$full_name, $email, $phone, $group_id]);
    
} catch(PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Успешно!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .success-box {
            background: white;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        h1 {
            color: #4CAF50;
            font-size: 48px;
            margin-bottom: 20px;
        }
        p {
            font-size: 20px;
            color: #666;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
        }
        .btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="success-box">
        <h1>Зачислен!</h1>
        <p>Студент <strong><?= htmlspecialchars($full_name) ?></strong> успешно зачислен в группу <strong><?= htmlspecialchars($group['name']) ?></strong></p>
        
        <div style="margin-top: 20px;">
        <a href="index.php" class="btn">🎓 Вернуться на главную</a>
        <a href="students_list.php" class="btn" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); margin-left: 10px;">📋 Посмотреть список зачисленных</a>
    </div>
    </div>
</body>
</html>