<?php
require_once 'config.php';
requireLogin();

$user_id = $_GET['id'] ?? $_SESSION['user_id'];
$pdo = getDB();

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if(!$user) {
    header('Location: dashboard.php');
    exit();
}

// Get user's events
$stmt = $pdo->prepare("
    SELECT e.*, ep.status, ep.points_earned, ep.attended_at
    FROM event_participants ep
    JOIN events e ON ep.event_id = e.id
    WHERE ep.user_id = ? AND ep.status = 'confirmed'
    ORDER BY e.event_date DESC
    LIMIT 20
");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll();

// Get user's rank - ИСПРАВЛЕННЫЙ ЗАПРОС
$stmt = $pdo->prepare("
    SELECT COUNT(*) + 1 as user_rank
    FROM users
    WHERE total_points > (SELECT total_points FROM users WHERE id = ?) AND role = 'participant'
");
$stmt->execute([$user_id]);
$rank_data = $stmt->fetch();
$rank = $rank_data['user_rank'];

// If rank is null (user has 0 points or is not a participant), set to appropriate value
if(!$rank) {
    if($user['role'] == 'participant') {
        // User has 0 points, they are last
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'participant'");
        $stmt->execute();
        $rank = $stmt->fetch()['total'] + 1;
    } else {
        // User is not a participant
        $rank = '-';
    }
}

// Get achievements
$stmt = $pdo->prepare("SELECT * FROM achievements WHERE user_id = ? ORDER BY earned_date DESC");
$stmt->execute([$user_id]);
$achievements = $stmt->fetchAll();

// If user is organizer, get their events count and reviews
if($user['role'] == 'organizer') {
    $stmt = $pdo->prepare("SELECT COUNT(*) as event_count FROM events WHERE organizer_id = ? AND is_verified = 1");
    $stmt->execute([$user_id]);
    $event_count = $stmt->fetch()['event_count'];
    
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM organizer_reviews WHERE organizer_id = ?");
    $stmt->execute([$user_id]);
    $avg_rating = $stmt->fetch()['avg_rating'] ?? 5;
    
    // Get common prizes
    $stmt = $pdo->prepare("
        SELECT bonus_description, COUNT(*) as count 
        FROM events 
        WHERE organizer_id = ? AND bonus_description IS NOT NULL 
        GROUP BY bonus_description 
        ORDER BY count DESC 
        LIMIT 3
    ");
    $stmt->execute([$user_id]);
    $prizes = $stmt->fetchAll();
}

$is_own_profile = ($user_id == $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль - <?= htmlspecialchars($user['full_name']) ?></title>
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
                <?php if($is_own_profile && (hasRole('organizer') || hasRole('admin'))): ?>
                    <a href="create_event.php">Создать мероприятие</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-danger">Выход</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
            </div>
            <h1><?= htmlspecialchars($user['full_name']) ?></h1>
            <p style="color: #666;">@<?= htmlspecialchars($user['username']) ?></p>
            <p style="color: #667eea; font-size: 1.2rem;">⭐ Рейтинг: <?= $user['total_points'] ?> баллов | Место: #<?= $rank ?></p>
            
            <?php if($user['role'] == 'organizer'): ?>
                <p style="color: #48bb78;">👔 Организатор</p>
            <?php elseif($user['role'] == 'admin'): ?>
                <p style="color: #e53e3e;">🔧 Администратор</p>
            <?php elseif($user['role'] == 'hr'): ?>
                <p style="color: #764ba2;">👔 Кадровая служба</p>
            <?php else: ?>
                <p>👤 Участник</p>
            <?php endif; ?>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= count($events) ?></div>
                <div class="stat-label">Участий в мероприятиях</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $user['city'] ?? '-' ?></div>
                <div class="stat-label">Город</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $user['age'] ?? '-' ?> лет</div>
                <div class="stat-label">Возраст</div>
            </div>
        </div>
        
        <?php if($user['role'] == 'organizer'): ?>
            <div class="card">
                <h2>👔 Информация об организаторе</h2>
                <div class="stats-grid" style="margin-top: 20px;">
                    <div class="stat-card">
                        <div class="stat-value"><?= $event_count ?? 0 ?></div>
                        <div class="stat-label">Проведено мероприятий</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?= round($avg_rating, 1) ?> / 5</div>
                        <div class="stat-label">Рейтинг доверия</div>
                    </div>
                </div>
                
                <?php if(!empty($prizes)): ?>
                    <h3>🎁 Часто дарит:</h3>
                    <ul style="margin-top: 10px;">
                        <?php foreach($prizes as $prize): ?>
                            <li><?= htmlspecialchars($prize['bonus_description']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>🏆 Достижения</h2>
            <?php if(count($achievements) > 0): ?>
                <div style="display: grid; gap: 10px;">
                    <?php foreach($achievements as $achievement): ?>
                        <div style="padding: 10px; background: #f9f9f9; border-radius: 5px;">
                            <strong><?= htmlspecialchars($achievement['title']) ?></strong>
                            <p><?= htmlspecialchars($achievement['description']) ?></p>
                            <small style="color: #999;"><?= date('d.m.Y', strtotime($achievement['earned_date'])) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Пока нет достижений. Участвуйте в мероприятиях!</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>📋 Портфолио достижений</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Мероприятие</th>
                        <th>Дата</th>
                        <th>Баллы</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= date('d.m.Y', strtotime($event['event_date'])) ?></td>
                            <td><strong>+<?= $event['points_earned'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <?php if(count($events) == 0): ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">Нет подтвержденных мероприятий</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>