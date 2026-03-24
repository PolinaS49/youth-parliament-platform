<?php
require_once 'config.php';

$event_id = $_GET['id'] ?? 0;

$pdo = getDB();
$stmt = $pdo->prepare("
    SELECT e.*, u.username as organizer_name, u.full_name as organizer_full
    FROM events e
    JOIN users u ON e.organizer_id = u.id
    WHERE e.id = ? AND e.is_verified = 1
");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if(!$event) {
    header('Location: events.php');
    exit();
}

// Check if user is already registered
$is_registered = false;
if(isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT * FROM event_participants WHERE event_id = ? AND user_id = ?");
    $stmt->execute([$event_id, $_SESSION['user_id']]);
    $is_registered = $stmt->fetch();
}

// Handle registration
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register']) && isLoggedIn()) {
    $stmt = $pdo->prepare("INSERT INTO event_participants (event_id, user_id, status) VALUES (?, ?, 'pending')");
    $stmt->execute([$event_id, $_SESSION['user_id']]);
    header('Location: event_detail.php?id=' . $event_id . '&registered=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['title']) ?> - Молодежный Парламент</title>
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
                <?php if(isLoggedIn()): ?>
                    <a href="logout.php" class="btn btn-danger">Выход</a>
                <?php else: ?>
                    <a href="login.php">Вход</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <h1><?= htmlspecialchars($event['title']) ?></h1>
            
            <?php if(isset($_GET['registered'])): ?>
                <div class="alert alert-success">Вы успешно зарегистрировались на мероприятие! Ожидайте подтверждения организатора.</div>
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
                <div>
                    <strong>📅 Дата:</strong><br>
                    <?= date('d.m.Y H:i', strtotime($event['event_date'])) ?>
                </div>
                <div>
                    <strong>📍 Место:</strong><br>
                    <?= htmlspecialchars($event['location'] ?? 'Не указано') ?>
                </div>
                <div>
                    <strong>🏷️ Категория:</strong><br>
                    <?= $event['category'] ?>
                </div>
                <div>
                    <strong>🎁 Баллы:</strong><br>
                    +<?= $event['points_awarded'] ?> × <?= $event['difficulty_coefficient'] ?> = 
                    <strong><?= $event['points_awarded'] * $event['difficulty_coefficient'] ?></strong>
                </div>
            </div>
            
            <div style="margin: 20px 0;">
                <h3>Описание мероприятия</h3>
                <p style="white-space: pre-wrap;"><?= htmlspecialchars($event['description']) ?></p>
            </div>
            
            <?php if($event['bonus_description']): ?>
                <div style="background: #f0f9ff; padding: 15px; border-radius: 10px; margin: 20px 0;">
                    <strong>✨ Бонусы и призы:</strong><br>
                    <?= htmlspecialchars($event['bonus_description']) ?>
                </div>
            <?php endif; ?>
            
            <div style="margin: 20px 0;">
                <strong>👤 Организатор:</strong>
                <a href="profile.php?id=<?= $event['organizer_id'] ?>"><?= htmlspecialchars($event['organizer_full']) ?></a>
            </div>
            
            <?php if(isLoggedIn()): ?>
                <?php if($is_registered): ?>
                    <div class="alert alert-success">
                        Вы уже зарегистрированы на это мероприятие. Статус: 
                        <?= $is_registered['status'] == 'pending' ? 'ожидает подтверждения' : 'подтверждено' ?>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <button type="submit" name="register" class="btn btn-success">Записаться на мероприятие</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert">
                    <a href="login.php">Войдите</a> в систему, чтобы записаться на мероприятие
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>