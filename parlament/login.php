<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Пароль в открытом виде
    
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    // Убираем проверку хеша - сравниваем пароли как есть
    if ($user && $password === $user['password']) { // Прямое сравнение
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Неверное имя пользователя или пароль";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Молодежный Парламент</title>
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
                <a href="register.php">Регистрация</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div style="max-width: 400px; margin: 50px auto;">
            <div class="card">
                <h2 style="text-align: center; margin-bottom: 30px;">Вход в систему</h2>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Имя пользователя или Email</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Пароль</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" class="btn" style="width: 100%;">Войти</button>
                </form>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="register.php">Нет аккаунта? Зарегистрируйтесь</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>