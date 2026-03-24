<?php
require_once 'config.php';
requireRole('organizer');

$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $event_date = $_POST['event_date'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $points_awarded = $_POST['points_awarded'];
    $difficulty_coefficient = $_POST['difficulty_coefficient'];
    $bonus_description = $_POST['bonus_description'];
    
    $stmt = $pdo->prepare("
        INSERT INTO events (title, description, event_date, location, category, points_awarded, difficulty_coefficient, bonus_description, organizer_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    if($stmt->execute([$title, $description, $event_date, $location, $category, $points_awarded, $difficulty_coefficient, $bonus_description, $_SESSION['user_id']])) {
        $success = "Мероприятие успешно создано! Ожидайте модерации.";
    } else {
        $error = "Ошибка при создании мероприятия";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать мероприятие - Молодежный Парламент</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Молодежный Парламент</a>
            <div class="nav-links">
                <a href="index.php">Главная</a>
                <a href="dashboard.php">Дашборд</a>
                <a href="events.php">Мероприятия</a>
                <a href="leaderboard.php">Рейтинг</a>
                <a href="profile.php">Профиль</a>
                <a href="create_event.php">Создать мероприятие</a>
                <a href="logout.php" class="btn btn-danger">Выход</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="card">
                <h2>📝 Создать мероприятие</h2>
                
                <?php if(isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Название мероприятия *</label>
                        <input type="text" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Описание *</label>
                        <textarea name="description" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Дата и время *</label>
                        <input type="datetime-local" name="event_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Место проведения</label>
                        <input type="text" name="location">
                    </div>
                    
                    <div class="form-group">
                        <label>Категория *</label>
                        <select name="category" required>
                            <option value="IT">IT</option>
                            <option value="Социальное проектирование">Социальное проектирование</option>
                            <option value="Медиа">Медиа</option>
                            <option value="Другое">Другое</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Баллов за участие *</label>
                        <input type="number" name="points_awarded" value="10" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Коэффициент сложности (1-2)</label>
                        <input type="number" name="difficulty_coefficient" step="0.1" value="1" min="1" max="2">
                    </div>
                    
                    <div class="form-group">
                        <label>Бонусы/Призы (мерч, билеты, встречи)</label>
                        <textarea name="bonus_description" rows="3" placeholder="Например: приглашение на форум, мерч, стажировка..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn" style="width: 100%;">Создать мероприятие</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>