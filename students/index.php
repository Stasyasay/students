<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Система зачисления</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', sans-serif; 
            background: #f0f2f5; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }
        .container { text-align: center; }
        h1 { margin-bottom: 40px; color: #333; font-size: 32px; }
        .cards { display: flex; gap: 30px; justify-content: center; flex-wrap: wrap; }
        .card { 
            background: white; 
            padding: 40px 30px; 
            border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            width: 260px; 
            text-decoration: none; 
            color: inherit; 
            transition: all 0.3s;
        }
        .card:hover { transform: translateY(-7px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .icon { font-size: 48px; margin-bottom: 15px; }
        .card h2 { margin: 0 0 10px; color: #2c3e50; }
        .card p { color: #7f8c8d; font-size: 14px; margin: 0; }
        .btn-applicant { border-bottom: 4px solid #3498db; }
        .btn-admin { border-bottom: 4px solid #e74c3c; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 Система зачисления в ВУЗ</h1>
        <div class="cards">
            <a href="applicant.php" class="card btn-applicant">
                <div class="icon">‍🎓</div>
                <h2>Абитуриент</h2>
                <p>Выбрать специальность, группу и подать заявление</p>
            </a>
            <a href="login.php" class="card btn-admin">
                <div class="icon">🔐</div>
                <h2>Администратор</h2>
                <p>Управление студентами, группами и специальностями</p>
            </a>
        </div>
    </div>
</body>
</html>