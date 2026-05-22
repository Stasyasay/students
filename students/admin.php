<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=university_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tab = $_GET['tab'] ?? 'students';
$msg = $_GET['msg'] ?? '';
$search = $_GET['search'] ?? '';

// Получаем данные для вкладок
if ($tab == 'students') {
    if ($search) {
        $stmt = $pdo->prepare("
            SELECT s.id, s.full_name, s.email, s.phone, g.name as group_name, sp.name as spec_name 
            FROM students s 
            JOIN groups g ON s.group_id = g.id 
            JOIN specialties sp ON g.specialty_id = sp.id 
            WHERE s.full_name LIKE ? OR s.email LIKE ?
            ORDER BY s.created_at DESC
        ");
        $stmt->execute(["%$search%", "%$search%"]);
        $students = $stmt->fetchAll();
    } else {
        $students = $pdo->query("
            SELECT s.id, s.full_name, s.email, s.phone, g.name as group_name, sp.name as spec_name 
            FROM students s 
            JOIN groups g ON s.group_id = g.id 
            JOIN specialties sp ON g.specialty_id = sp.id 
            ORDER BY s.created_at DESC
        ")->fetchAll();
    }
} elseif ($tab == 'specialties') {
    $specialties = $pdo->query("SELECT * FROM specialties ORDER BY id")->fetchAll();
} elseif ($tab == 'groups') {
    $groups = $pdo->query("
        SELECT g.id, g.name, g.capacity, sp.name as spec_name 
        FROM groups g 
        JOIN specialties sp ON g.specialty_id = sp.id 
        ORDER BY g.id
    ")->fetchAll();
    $specs_for_select = $pdo->query("SELECT id, name FROM specialties")->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #2c3e50; margin-bottom: 20px; }
        .header-info { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #eee; }
        .user-info { color: #666; font-size: 14px; }
        .logout-btn { background: #e74c3c; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-size: 13px; }
        .logout-btn:hover { background: #c0392b; }
        .tabs { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid #eee; padding-bottom: 10px; flex-wrap: wrap; }
        .tab { padding: 10px 20px; text-decoration: none; color: #666; font-weight: 600; border-radius: 6px; transition: 0.2s; }
        .tab:hover { background: #f0f0f0; }
        .tab.active { background: #3498db; color: white; }
        .msg { padding: 12px; background: #d4edda; color: #155724; border-radius: 6px; margin-bottom: 20px; text-align: center; }
        .search-box { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .search-box input { flex: 1; min-width: 200px; padding: 10px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .search-box button { padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .search-box button:hover { background: #2980b9; }
        .search-box a { padding: 10px 20px; background: #95a5a6; color: white; text-decoration: none; border-radius: 6px; }
        .search-box a:hover { background: #7f8c8d; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #495057; }
        .btn { padding: 8px 14px; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; color: white; }
        .btn-del { background: #e74c3c; }
        .btn-add { background: #27ae60; margin-bottom: 15px; }
        .form-row { display: flex; gap: 10px; margin-bottom: 15px; align-items: center; flex-wrap: wrap; }
        input, select { padding: 8px; border: 1px solid #ddd; border-radius: 5px; }
        .back { display: inline-block; margin-top: 20px; color: #3498db; text-decoration: none; }
        .count { color: #666; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-info">
            <h1 style="margin:0; font-size:24px;">🔐 Панель администратора</h1>
            <div class="user-info">
                👤 <?= htmlspecialchars($_SESSION['admin_username']) ?> | 
                <a href="logout.php" class="logout-btn">🚪 Выйти</a>
            </div>
        </div>

        <?php if ($msg == 'success'): ?>
            <div class="msg">✅ Действие выполнено успешно!</div>
        <?php endif; ?>

        <div class="tabs">
            <a href="?tab=students" class="tab <?= $tab == 'students' ? 'active' : '' ?>">👥 Студенты</a>
            <a href="?tab=specialties" class="tab <?= $tab == 'specialties' ? 'active' : '' ?>">📚 Специальности</a>
            <a href="?tab=groups" class="tab <?= $tab == 'groups' ? 'active' : '' ?>">🏫 Группы</a>
        </div>

        <?php if ($tab == 'students'): ?>
            <h3>Зачисленные абитуриенты</h3>
            
            <!-- Поиск -->
            <form method="GET" class="search-box">
                <input type="hidden" name="tab" value="students">
                <input type="text" name="search" placeholder="🔍 Поиск по фамилии или email..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Найти</button>
                <?php if ($search): ?>
                    <a href="?tab=students">Сбросить</a>
                <?php endif; ?>
            </form>
            
            <p class="count">Всего найдено: <strong><?= count($students) ?></strong></p>
            
            <?php if (empty($students)): ?>
                <p>Пока нет зачисленных студентов<?= $search ? ' по вашему запросу' : '' ?>.</p>
            <?php else: ?>
            <table>
                <tr><th>ID</th><th>ФИО</th><th>Группа</th><th>Специальность</th><th>Email</th><th>Телефон</th><th>Действие</th></tr>
                <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><strong><?= htmlspecialchars($s['full_name']) ?></strong></td>
                    <td><?= htmlspecialchars($s['group_name']) ?></td>
                    <td><?= htmlspecialchars($s['spec_name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars($s['phone'] ?: '—') ?></td>
                    <td>
                        <form method="POST" action="admin_actions.php" style="display:inline;">
                            <input type="hidden" name="action" value="delete_student">
                            <input type="hidden" name="tab" value="students">
                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                            <button type="submit" class="btn btn-del" onclick="return confirm('Удалить студента?')">🗑 Удалить</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>

        <?php elseif ($tab == 'specialties'): ?>
            <h3>Управление специальностями</h3>
            <form method="POST" action="admin_actions.php" class="form-row">
                <input type="hidden" name="action" value="add_specialty">
                <input type="hidden" name="tab" value="specialties">
                <input type="text" name="name" placeholder="Название специальности" required>
                <button type="submit" class="btn btn-add">➕ Добавить</button>
            </form>
            <table>
                <tr><th>ID</th><th>Название</th><th>Действие</th></tr>
                <?php foreach ($specialties as $sp): ?>
                <tr>
                    <td><?= $sp['id'] ?></td>
                    <td><?= htmlspecialchars($sp['name']) ?></td>
                    <td>
                        <form method="POST" action="admin_actions.php" style="display:inline;">
                            <input type="hidden" name="action" value="delete_specialty">
                            <input type="hidden" name="tab" value="specialties">
                            <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                            <button type="submit" class="btn btn-del" onclick="return confirm('Удалить специальность? Группы привязанные к ней тоже удалятся.')">🗑 Удалить</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($tab == 'groups'): ?>
            <h3>Управление группами</h3>
            <form method="POST" action="admin_actions.php" class="form-row">
                <input type="hidden" name="action" value="add_group">
                <input type="hidden" name="tab" value="groups">
                <select name="specialty_id" required>
                    <option value="">Выбери специальность</option>
                    <?php foreach ($specs_for_select as $sp): ?>
                        <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="name" placeholder="Название группы (напр. ПР-103)" required>
                <input type="number" name="capacity" placeholder="Мест" value="25" required>
                <button type="submit" class="btn btn-add">➕ Добавить</button>
            </form>
            <table>
                <tr><th>ID</th><th>Специальность</th><th>Группа</th><th>Мест</th><th>Действие</th></tr>
                <?php foreach ($groups as $g): ?>
                <tr>
                    <td><?= $g['id'] ?></td>
                    <td><?= htmlspecialchars($g['spec_name']) ?></td>
                    <td><?= htmlspecialchars($g['name']) ?></td>
                    <td><?= $g['capacity'] ?></td>
                    <td>
                        <form method="POST" action="admin_actions.php" style="display:inline;">
                            <input type="hidden" name="action" value="delete_group">
                            <input type="hidden" name="tab" value="groups">
                            <input type="hidden" name="id" value="<?= $g['id'] ?>">
                            <button type="submit" class="btn btn-del" onclick="return confirm('Удалить группу?')">🗑 Удалить</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <a href="index.php" class="back">← Вернуться на главную</a>
    </div>
</body>
</html>