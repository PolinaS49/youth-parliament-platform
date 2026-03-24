<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Сохраняем пароль как есть
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $city = $_POST['city'];
    $age = $_POST['age'];
    $education_org = $_POST['education_org'];
    $team_name = $_POST['team_name'] ?? '';
    
    $pdo = getDB();
    
    try {
        // Сохраняем пароль в открытом виде
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role, city, age, education_org, team_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $full_name, $role, $city, $age, $education_org, $team_name]);
        
        header('Location: login.php?registered=1');
        exit();
    } catch(PDOException $e) {
        $error = "Ошибка регистрации: пользователь или email уже существует";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Молодежный Парламент</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Молодежный Парламент</a>
            <div class="nav-links">
                <a href="index.php">Главная</a>
                <a href="events.php">Мероприятия</a>
                <a href="leaderboard.php">Рейтинг</a>
                <a href="login.php">Вход</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div style="max-width: 500px; margin: 50px auto;">
            <div class="card">
                <h2 style="text-align: center; margin-bottom: 30px;">Регистрация</h2>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Имя пользователя *</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Пароль *</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>ФИО *</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Роль *</label>
                        <select name="role" required>
                            <option value="participant">Участник</option>
                            <option value="organizer">Организатор</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Город</label>
                        <input type="text" name="city">
                    </div>
                    <div class="form-group">
                        <label>Возраст</label>
                        <input type="number" name="age">
                    </div>
                    <div class="form-group">
                        <label>Образовательная организация</label>
                        <input type="text" name="education_org">
                    </div>
                    <div class="form-group">
                        <label>Название команды</label>
                        <input type="text" name="team_name">
                    </div>
                    <button type="submit" class="btn" style="width: 100%;">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>