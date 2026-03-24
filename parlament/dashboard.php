<?php
require_once 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$pdo = getDB();

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Update and get current rating
$current_rating = updateUserPoints($user_id);

// Get user's event participation history
$stmt = $pdo->prepare("
    SELECT e.*, ep.status, ep.points_earned, ep.attended_at
    FROM event_participants ep
    JOIN events e ON ep.event_id = e.id
    WHERE ep.user_id = ?
    ORDER BY e.event_date DESC
    LIMIT 10
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
$rank = $stmt->fetch()['user_rank'];

// If rank is null (user has 0 points), set to total users + 1
if(!$rank) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'participant'");
    $stmt->execute();
    $rank = $stmt->fetch()['total'] + 1;
}

// Calculate next level (every 100 points)
$next_level_points = ceil($current_rating / 100) * 100;
$points_needed = $next_level_points - $current_rating;

// Get activity chart data (last 6 months)
$stmt = $pdo->prepare("
    SELECT DATE_FORMAT(attended_at, '%Y-%m') as month, SUM(points_earned) as points
    FROM event_participants
    WHERE user_id = ? AND attended_at IS NOT NULL
    GROUP BY DATE_FORMAT(attended_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6
");
$stmt->execute([$user_id]);
$chart_data = $stmt->fetchAll();
$chart_data = array_reverse($chart_data);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд активности - Молодежный Парламент</title>
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
                <?php if(hasRole('organizer') || hasRole('admin')): ?>
                    <a href="create_event.php">Создать мероприятие</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-danger">Выход</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Дашборд активности</h1>
        <p>Добро пожаловать, <?= htmlspecialchars($_SESSION['full_name']) ?>!</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $current_rating ?></div>
                <div class="stat-label">Всего баллов</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">#<?= $rank ?></div>
                <div class="stat-label">Место в рейтинге</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= count($events) ?></div>
                <div class="stat-label">Участий в мероприятиях</div>
            </div>
        </div>
        
        <div class="card">
            <h2>📈 Прогноз попадания в кадровый резерв</h2>
            <?php if($points_needed > 0): ?>
                <div style="margin: 20px 0;">
                    <div style="background: #f0f0f0; border-radius: 10px; overflow: hidden;">
                        <div style="background: #667eea; width: <?= min(100, ($current_rating / $next_level_points) * 100) ?>%; height: 30px; display: flex; align-items: center; justify-content: center; color: white;">
                            <?= $current_rating ?> / <?= $next_level_points ?>
                        </div>
                    </div>
                    <p style="margin-top: 10px;">До следующего уровня осталось <?= $points_needed ?> баллов</p>
                </div>
            <?php else: ?>
                <p>🎉 Поздравляем! Вы достигли высокого уровня!</p>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>📊 График активности</h2>
            <canvas id="activityChart" style="max-height: 300px; width: 100%;"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('activityChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [<?php foreach($chart_data as $data): ?>'<?= $data['month'] ?>',<?php endforeach; ?>],
                        datasets: [{
                            label: 'Начислено баллов',
                            data: [<?php foreach($chart_data as $data): ?><?= $data['points'] ?>,<?php endforeach; ?>],
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true
                    }
                });
            </script>
        </div>
        
        <div class="card">
            <h2>📝 Последние мероприятия</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Мероприятие</th>
                        <th>Дата</th>
                        <th>Статус</th>
                        <th>Баллы</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= date('d.m.Y', strtotime($event['event_date'])) ?></td>
                            <td>
                                <?php if($event['status'] == 'confirmed'): ?>
                                    <span style="color: #48bb78;">✓ Подтверждено</span>
                                <?php elseif($event['status'] == 'pending'): ?>
                                    <span style="color: #ecc94b;">⏳ Ожидает</span>
                                <?php else: ?>
                                    <span style="color: #e53e3e;">✗ Отменено</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= $event['points_earned'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card">
            <h2>🏷️ Облако тегов популярных направлений</h2>
            <div style="padding: 20px; text-align: center;">
                <?php
                $tags = $pdo->query("SELECT category, COUNT(*) as count FROM events WHERE is_verified=1 GROUP BY category")->fetchAll();
                foreach($tags as $tag):
                    $size = 16 + min(20, $tag['count'] * 2);
                ?>
                    <span style="display: inline-block; margin: 10px; font-size: <?= $size ?>px; color: #667eea;">
                        #<?= $tag['category'] ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>