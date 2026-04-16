<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$group_id = intval($_POST['group_id'] ?? 0);

if ($group_id == 0) {
    die(" Ошибка: не выбрана группа");
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
    
    if (!$group) {
        die(" Группа не найдена");
    }
    
    if (($group['capacity'] - $group['enrolled']) <= 0) {
        die(" Мест в группе нет!");
    }
    
} catch(PDOException $e) {
    die(" Ошибка: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма зачисления</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }
        .group-info {
            background: #f0f7ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }
        input {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1> Форма зачисления</h1>
        
        <div class="group-info">
            <strong>Группа:</strong> <?= htmlspecialchars($group['name']) ?><br>
            <strong>Свободно мест:</strong> <?= ($group['capacity'] - $group['enrolled']) ?>
        </div>
        
        <form action="save_student.php" method="POST">
            <input type="hidden" name="group_id" value="<?= $group_id ?>">
            
            <div class="form-group">
                <label for="full_name">ФИО *</label>
                <input type="text" name="full_name" id="full_name" required placeholder="Иванов Иван Иванович">
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" name="email" id="email" required placeholder="ivan@example.com">
            </div>
            
            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="tel" name="phone" id="phone" placeholder="+7 (999) 999-99-99">
            </div>
            
            <button type="submit" class="btn"> Зачислить</button>
        </form>
        
        <a href="index.php" class="back-link">← Вернуться к выбору</a>
    </div>
</body>
</html>