<?php
// Подключаемся к базе
$pdo = new PDO('mysql:host=localhost;dbname=university_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Получаем специальности
$stmt = $pdo->query("SELECT * FROM specialties");
$specialties = $stmt->fetchAll(PDO::FETCH_ASSOC);
// foreach ($specialties as $spec) {
//     foreach ($spec as $key => $value) {
//         echo "Ключ: $key, Значение: $value\n";
//     }
//     echo "Значение текущего элемента массива \$specialties: $spec.\n";
// }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Зачисление в ВУЗ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 700px;
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
            font-size: 32px;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
        }
        select {
            width: 100%;
            padding: 12px 15px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        select:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        .info {
            background: #e7f3ff;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 10px;
            color: #0066cc;
            font-size: 14px;
        }
        #groupBlock {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Зачисление в ВУЗ</h1>
        <p class="subtitle">Выберите специальность и группу</p>
        
        <form action="enroll.php" method="POST">
            <div class="form-group">
                <label for="specialty">1. Выберите специальность:</label>
                <select name="specialty_id" id="specialty" required onchange="loadGroups()">
                    <option value="">-- Выберите из списка --</option>
                    <?php foreach ($specialties as $spec): ?>
                        <option value="<?= $spec['id'] ?>"><?= htmlspecialchars($spec['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="groupBlock">
                <label for="group">2. Выберите группу:</label>
                <select name="group_id" id="group" required>
                    <option value="">-- Загрузка групп --</option>
                </select>
                <div id="groupInfo" class="info"></div>
            </div>

            <button type="submit" id="submitBtn" class="btn" disabled>
                Перейти к заполнению формы →
            </button>
        </form>
    </div>

    <script>
        function loadGroups() {
            const specialtyId = document.getElementById('specialty').value;
            const groupBlock = document.getElementById('groupBlock');
            const groupSelect = document.getElementById('group');
            const submitBtn = document.getElementById('submitBtn');
            const groupInfo = document.getElementById('groupInfo');
            
            if (!specialtyId) {
                groupBlock.style.display = 'none';
                submitBtn.disabled = true;
                return;
            }

            fetch(`get_groups.php?specialty_id=${specialtyId}`)
                .then(response => response.json())
                .then(data => {
                    groupSelect.innerHTML = '<option value="">-- Выберите группу --</option>';
                    
                    if (data.groups.length === 0) {
                        groupSelect.innerHTML = '<option value="">Нет доступных групп</option>';
                        groupInfo.textContent = 'К сожалению, нет свободных мест';
                        submitBtn.disabled = true;
                    } else {
                        data.groups.forEach(group => {
                            const available = group.capacity - group.enrolled;
                            const option = document.createElement('option');
                            option.value = group.id;
                            option.textContent = `${group.name} (свободно мест: ${available})`;
                            groupSelect.appendChild(option);
                        });
                        groupInfo.textContent = `Найдено групп: ${data.groups.length}`;
                        submitBtn.disabled = false;
                    }
                    
                    groupBlock.style.display = 'block';
                })
                .catch(err => {
                    console.error(err);
                    groupInfo.textContent = 'Ошибка загрузки групп';
                });
        }
    </script>
</body>
</html>