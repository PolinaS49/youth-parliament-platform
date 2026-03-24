<?php
require_once 'config.php';

$pdo = getDB();

// Get filter parameters
$category = $_GET['category'] ?? 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build query based on category filter
if($category == 'all') {
    $stmt = $pdo->prepare("
        SELECT id, username, full_name, total_points, city, education_org
        FROM users 
        WHERE role = 'participant' 
        ORDER BY total_points DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $participants = $stmt->fetchAll();
    
    $total = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'participant'")->fetchColumn();
} else {
    // For category filter, we need to calculate points per category
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.full_name, u.city, u.education_org,
               SUM(ep.points_earned * e.difficulty_coefficient) as total_points
        FROM users u
        JOIN event_participants ep ON u.id = ep.user_id
        JOIN events e ON ep.event_id = e.id
        WHERE u.role = 'participant' AND e.category = :category AND ep.status = 'confirmed'
        GROUP BY u.id
        ORDER BY total_points DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $participants = $stmt->fetchAll();
    
    $stmt_count = $pdo->prepare("
        SELECT COUNT(DISTINCT u.id) 
        FROM users u
        JOIN event_participants ep ON u.id = ep.user_id
        JOIN events e ON ep.event_id = e.id
        WHERE u.role = 'participant' AND e.category = :category AND ep.status = 'confirmed'
    ");
    $stmt_count->bindValue(':category', $category, PDO::PARAM_STR);
    $stmt_count->execute();
    $total = $stmt_count->fetchColumn();
}

$total_pages = ceil($total / $per_page);

// Get categories for filter
$categories = $pdo->query("SELECT DISTINCT category FROM events")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Таблица лидеров - Молодежный Парламент</title>
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
        <h1>🏆 Глобальная таблица лидеров</h1>
        
        <div class="card">
            <h3>Фильтр по направлениям</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;">
                <a href="?category=all&page=1" class="btn <?= $category == 'all' ? 'btn-success' : '' ?>">Все</a>
                <?php foreach($categories as $cat): ?>
                    <a href="?category=<?= urlencode($cat['category']) ?>&page=1" class="btn <?= $category == $cat['category'] ? 'btn-success' : '' ?>">
                        <?= htmlspecialchars($cat['category']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Место</th>
                        <th>Участник</th>
                        <th>Город</th>
                        <th>Организация</th>
                        <th>Баллы</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($participants) == 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Нет участников</td>
                        </tr>
                    <?php endif; ?>
                    
                    <?php foreach($participants as $index => $participant): ?>
                        <tr>
                            <td>
                                <strong>#<?= $offset + $index + 1 ?></strong>
                                <?php if($offset + $index + 1 == 1): ?> 🥇
                                <?php elseif($offset + $index + 1 == 2): ?> 🥈
                                <?php elseif($offset + $index + 1 == 3): ?> 🥉
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="profile.php?id=<?= $participant['id'] ?>" style="text-decoration: none; color: #333;">
                                    <strong><?= htmlspecialchars($participant['full_name']) ?></strong>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($participant['city'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($participant['education_org'] ?? '-') ?></td>
                            <td><strong style="color: #667eea;"><?= $participant['total_points'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if($total_pages > 1): ?>
                <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px; flex-wrap: wrap;">
                    <?php if($page > 1): ?>
                        <a href="?page=<?= $page-1 ?>&category=<?= urlencode($category) ?>" class="btn">← Предыдущая</a>
                    <?php endif; ?>
                    
                    <?php
                    // Show page numbers
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    if($start_page > 1): ?>
                        <a href="?page=1&category=<?= urlencode($category) ?>" class="btn">1</a>
                        <?php if($start_page > 2): ?>
                            <span style="padding: 10px;">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for($i = $start_page; $i <= $end_page; $i++): ?>
                        <a href="?page=<?= $i ?>&category=<?= urlencode($category) ?>" class="btn <?= $i == $page ? 'btn-success' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if($end_page < $total_pages): ?>
                        <?php if($end_page < $total_pages - 1): ?>
                            <span style="padding: 10px;">...</span>
                        <?php endif; ?>
                        <a href="?page=<?= $total_pages ?>&category=<?= urlencode($category) ?>" class="btn"><?= $total_pages ?></a>
                    <?php endif; ?>
                    
                    <?php if($page < $total_pages): ?>
                        <a href="?page=<?= $page+1 ?>&category=<?= urlencode($category) ?>" class="btn">Следующая →</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>