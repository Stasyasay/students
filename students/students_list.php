<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=university_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("
        SELECT 
            s.id,
            s.full_name,
            s.email,
            s.phone,
            s.created_at,
            g.name as group_name,
            sp.name as specialty_name
        FROM students s
        JOIN groups g ON s.group_id = g.id
        JOIN specialties sp ON g.specialty_id = sp.id
        ORDER BY s.created_at DESC
    ");
    
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список зачисленных</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .empty-message {
            text-align: center;
            color: #999;
            padding: 40px;
            font-size: 18px;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }
        .back-btn:hover {
            opacity: 0.9;
        }
        .count {
            text-align: right;
            color: #666;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Список зачисленных студентов</h1>
        
        <?php if (empty($students)): ?>
            <p class="empty-message">Пока никто не зачислен</p>
        <?php else: ?>
            <p class="count">Всего зачислено: <strong><?= count($students) ?></strong></p>
            
            <table>
                <thead>
                    <tr>
                        <th>№</th>
                        <th>ФИО</th>
                        <th>Группа</th>
                        <th>Специальность</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Дата зачисления</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><strong><?= htmlspecialchars($student['full_name']) ?></strong></td>
                            <td><?= htmlspecialchars($student['group_name']) ?></td>
                            <td><?= htmlspecialchars($student['specialty_name']) ?></td>
                            <td><?= htmlspecialchars($student['email']) ?></td>
                            <td><?= htmlspecialchars($student['phone'] ?: '—') ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($student['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <a href="index.php" class="back-btn">← Вернуться на главную</a>
    </div>
</body>
</html>