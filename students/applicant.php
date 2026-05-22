<?php
$pdo = new PDO('mysql:host=localhost;dbname=university_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$specialties = $pdo->query("SELECT * FROM specialties")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Зачисление</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea, #764ba2); min-height: 100vh; padding: 40px 20px; }
        .container { max-width: 700px; margin: 0 auto; background: white; border-radius: 15px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); padding: 40px; }
        h1 { text-align: center; color: #333; margin-bottom: 10px; }
        .subtitle { text-align: center; color: #666; margin-bottom: 30px; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
        select { width: 100%; padding: 12px 15px; font-size: 16px; border: 2px solid #ddd; border-radius: 8px; background: white; cursor: pointer; }
        select:focus { outline: none; border-color: #667eea; }
        .btn { display: block; width: 100%; padding: 15px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(102,126,234,0.4); }
        .btn:disabled { background: #ccc; cursor: not-allowed; transform: none; }
        .info { background: #e7f3ff; padding: 12px 15px; border-radius: 8px; margin-top: 10px; color: #0066cc; font-size: 14px; }
        #groupBlock { display: none; }
        .back { display: block; text-align: center; margin-top: 20px; color: white; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 Зачисление в ВУЗ</h1>
        <p class="subtitle">Выберите специальность и группу</p>
        
        <form action="enroll.php" method="POST">
            <div class="form-group">
                <label>1. Выберите специальность:</label>
                <select name="specialty_id" id="specialty" required onchange="loadGroups()">
                    <option value="">-- Выберите из списка --</option>
                    <?php foreach ($specialties as $sp): ?>
                        <option value="<?= $sp['id'] ?>"><?= htmlspecialchars($sp['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="groupBlock">
                <label>2. Выберите группу:</label>
                <select name="group_id" id="group" required>
                    <option value="">-- Загрузка групп --</option>
                </select>
                <div id="groupInfo" class="info"></div>
            </div>

            <button type="submit" id="submitBtn" class="btn" disabled>Перейти к заполнению формы →</button>
        </form>
        <a href="index.php" class="back">← На главную</a>
    </div>

    <script>
        function loadGroups() {
            const s = document.getElementById('specialty').value;
            const gB = document.getElementById('groupBlock');
            const gS = document.getElementById('group');
            const sB = document.getElementById('submitBtn');
            const gI = document.getElementById('groupInfo');

            if (!s) { gB.style.display = 'none'; sB.disabled = true; return; }

            fetch(`get_groups.php?specialty_id=${s}`)
                .then(r => r.json())
                .then(d => {
                    gS.innerHTML = '<option value="">-- Выберите группу --</option>';
                    if (!d.groups.length) {
                        gS.innerHTML = '<option value="">Нет доступных групп</option>';
                        gI.textContent = '😔 К сожалению, нет свободных мест';
                        sB.disabled = true;
                    } else {
                        d.groups.forEach(g => {
                            const a = g.capacity - g.enrolled;
                            const o = document.createElement('option');
                            o.value = g.id;
                            o.textContent = `${g.name} (свободно мест: ${a})`;
                            gS.appendChild(o);
                        });
                        gI.textContent = `✅ Найдено групп: ${d.groups.length}`;
                        sB.disabled = false;
                    }
                    gB.style.display = 'block';
                })
                .catch(() => gI.textContent = '❌ Ошибка загрузки групп');
        }
    </script>
</body>
</html>