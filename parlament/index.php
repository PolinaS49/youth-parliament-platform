<?php
require_once 'config.php';

$pdo = getDB();

// Get top 5 participants
$topParticipants = $pdo->query("
    SELECT username, full_name, total_points 
    FROM users 
    WHERE role = 'participant' 
    ORDER BY total_points DESC 
    LIMIT 5
")->fetchAll();

// Get upcoming events
$upcomingEvents = $pdo->query("
    SELECT * FROM events 
    WHERE event_date > NOW() AND is_verified = 1 
    ORDER BY event_date ASC 
    LIMIT 6
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Молодежный Парламент - Платформа Рейтинга Активности</title>
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
                <?php if(isLoggedIn()): ?>
                    <a href="dashboard.php">Личный кабинет</a>
                    <a href="profile.php">Профиль</a>
                    <?php if(hasRole('organizer') || hasRole('admin')): ?>
                        <a href="create_event.php">Создать мероприятие</a>
                    <?php endif; ?>
                    <?php if(hasRole('hr') || hasRole('admin')): ?>
                        <a href="hr_panel.php">Кадровый резерв</a>
                    <?php endif; ?>
                    <?php if(hasRole('admin')): ?>
                        <a href="admin.php">Админка</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-danger">Выход</a>
                <?php else: ?>
                    <a href="login.php">Вход</a>
                    <a href="register.php">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 10px; margin-bottom: 30px;">
            <h1 style="font-size: 2.5rem; color: #667eea;">Платформа Рейтинга Активности</h1>
            <p style="font-size: 1.2rem; color: #666; margin-top: 20px;">Участвуйте в мероприятиях, копите баллы и попадите в кадровый резерв!</p>
            <?php if(!isLoggedIn()): ?>
                <div style="margin-top: 30px;">
                    <a href="register.php" class="btn" style="margin-right: 10px;">Присоединиться</a>
                    <a href="login.php" class="btn">Войти</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $pdo->query("SELECT COUNT(*) FROM users WHERE role='participant'")->fetchColumn() ?></div>
                <div class="stat-label">Активных участников</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $pdo->query("SELECT COUNT(*) FROM events WHERE is_verified=1")->fetchColumn() ?></div>
                <div class="stat-label">Проведено мероприятий</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $pdo->query("SELECT SUM(total_points) FROM users")->fetchColumn() ?></div>
                <div class="stat-label">Всего начислено баллов</div>
            </div>
        </div>

        <div class="card">
            <h2>🏆 Топ участников</h2>
            <div style="margin-top: 20px;">
                <?php foreach($topParticipants as $index => $participant): ?>
                    <div class="leaderboard-item">
                        <div class="rank">#<?= $index + 1 ?></div>
                        <div class="avatar"><?= strtoupper(substr($participant['full_name'], 0, 1)) ?></div>
                        <div class="user-info">
                            <div class="user-name"><?= htmlspecialchars($participant['full_name']) ?></div>
                            <div class="user-points"><?= $participant['total_points'] ?> баллов</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="leaderboard.php" class="btn" style="margin-top: 20px;">Посмотреть полный рейтинг</a>
        </div>

        <div class="card">
            <h2>📅 Ближайшие мероприятия</h2>
            <div class="events-grid" style="margin-top: 20px;">
                <?php foreach($upcomingEvents as $event): ?>
                    <div class="event-card">
                        <div class="event-header">
                            <div class="event-title"><?= htmlspecialchars($event['title']) ?></div>
                            <div class="event-date">📅 <?= date('d.m.Y H:i', strtotime($event['event_date'])) ?></div>
                        </div>
                        <div class="event-body">
                            <p><?= htmlspecialchars(substr($event['description'], 0, 100)) ?>...</p>
                            <div class="event-points">🎁 +<?= $event['points_awarded'] ?> баллов</div>
                            <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn" style="margin-top: 15px;">Подробнее</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="events.php" class="btn" style="margin-top: 20px;">Все мероприятия</a>
        </div>
    </div>
</body>
</html>