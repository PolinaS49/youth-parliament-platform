<?php
require_once 'config.php';

$pdo = getDB();

// Get filters
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT e.*, u.username as organizer_name 
          FROM events e 
          JOIN users u ON e.organizer_id = u.id 
          WHERE e.is_verified = 1";
$params = [];

if($category && $category != 'all') {
    $query .= " AND e.category = ?";
    $params[] = $category;
}

if($search) {
    $query .= " AND (e.title LIKE ? OR e.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY e.event_date ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll();

// Get categories for filter
$categories = $pdo->query("SELECT DISTINCT category FROM events")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мероприятия - Молодежный Парламент</title>
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
                <?php if(isLoggedIn() && (hasRole('organizer') || hasRole('admin'))): ?>
                    <a href="create_event.php">Создать мероприятие</a>
                <?php endif; ?>
                <?php if(isLoggedIn()): ?>
                    <a href="logout.php" class="btn btn-danger">Выход</a>
                <?php else: ?>
                    <a href="login.php">Вход</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>📅 Мероприятия</h1>
        
        <div class="card">
            <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <select name="category" style="padding: 10px;">
                    <option value="all">Все категории</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['category'] ?>" <?= $category == $cat['category'] ? 'selected' : '' ?>>
                            <?= $cat['category'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="search" placeholder="Поиск мероприятий..." value="<?= htmlspecialchars($search) ?>" style="flex: 1;">
                <button type="submit" class="btn">Найти</button>
            </form>
        </div>
        
        <div class="events-grid">
            <?php if(count($events) == 0): ?>
                <div class="card" style="grid-column: 1/-1; text-align: center;">
                    <p>Мероприятия не найдены</p>
                </div>
            <?php endif; ?>
            
            <?php foreach($events as $event): ?>
                <div class="event-card">
                    <div class="event-header">
                        <div class="event-title"><?= htmlspecialchars($event['title']) ?></div>
                        <div class="event-date">📅 <?= date('d.m.Y H:i', strtotime($event['event_date'])) ?></div>
                        <div style="font-size: 0.8rem; margin-top: 5px;">🏷️ <?= $event['category'] ?></div>
                    </div>
                    <div class="event-body">
                        <p><?= htmlspecialchars(substr($event['description'], 0, 150)) ?>...</p>
                        <div class="event-points">🎁 +<?= $event['points_awarded'] ?> баллов</div>
                        <?php if($event['bonus_description']): ?>
                            <div style="margin: 10px 0; color: #764ba2;">✨ Бонус: <?= $event['bonus_description'] ?></div>
                        <?php endif; ?>
                        <div style="margin: 10px 0; font-size: 0.9rem; color: #666;">
                            Организатор: <?= htmlspecialchars($event['organizer_name']) ?>
                        </div>
                        <?php if(isLoggedIn()): ?>
                            <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn" style="margin-top: 15px;">Записаться</a>
                        <?php else: ?>
                            <a href="login.php" class="btn" style="margin-top: 15px;">Войдите чтобы записаться</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>