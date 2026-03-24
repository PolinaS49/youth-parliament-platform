<?php
require_once 'config.php';
requireRole('hr');

$pdo = getDB();

// Get filters
$age_from = $_GET['age_from'] ?? '';
$age_to = $_GET['age_to'] ?? '';
$city = $_GET['city'] ?? '';
$min_events = $_GET['min_events'] ?? '';
$min_points = $_GET['min_points'] ?? '';

$query = "
    SELECT u.*, 
           COUNT(DISTINCT ep.id) as events_count,
           AVG(ep.points_earned) as avg_points_per_event
    FROM users u
    LEFT JOIN event_participants ep ON u.id = ep.user_id AND ep.status = 'confirmed'
    WHERE u.role = 'participant'
";

$params = [];

if($age_from) {
    $query .= " AND u.age >= ?";
    $params[] = $age_from;
}
if($age_to) {
    $query .= " AND u.age <= ?";
    $params[] = $age_to;
}
if($city) {
    $query .= " AND u.city = ?";
    $params[] = $city;
}
if($min_events) {
    $query .= " HAVING events_count >= ?";
    $params[] = $min_events;
}
if($min_points) {
    $query .= " AND u.total_points >= ?";
    $params[] = $min_points;
}

$query .= " GROUP BY u.id ORDER BY u.total_points DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$candidates = $stmt->fetchAll();

// Get cities for filter
$cities = $pdo->query("SELECT DISTINCT city FROM users WHERE city IS NOT NULL")->fetchAll();

// Handle PDF export
if(isset($_GET['export_pdf']) && isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    // Simple PDF output (in production, use a proper PDF library)
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="candidate_' . $user_id . '.pdf"');
    echo "Отчет по кандидату:\n\n";
    echo "ФИО: " . $user['full_name'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Город: " . ($user['city'] ?? '-') . "\n";
    echo "Возраст: " . ($user['age'] ?? '-') . "\n";
    echo "Баллы: " . $user['total_points'] . "\n";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кадровый резерв - Молодежный Парламент</title>
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
                <a href="hr_panel.php">Кадровый резерв</a>
                <a href="logout.php" class="btn btn-danger">Выход</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>👔 Инспектор кадрового резерва</h1>
        
        <div class="card">
            <h2>🔍 Фильтры поиска кандидатов</h2>
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <label>Возраст от:</label>
                    <input type="number" name="age_from" value="<?= htmlspecialchars($age_from) ?>">
                </div>
                <div>
                    <label>Возраст до:</label>
                    <input type="number" name="age_to" value="<?= htmlspecialchars($age_to) ?>">
                </div>
                <div>
                    <label>Город:</label>
                    <select name="city">
                        <option value="">Все города</option>
                        <?php foreach($cities as $c): ?>
                            <option value="<?= htmlspecialchars($c['city']) ?>" <?= $city == $c['city'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['city']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Мин. кол-во мероприятий:</label>
                    <input type="number" name="min_events" value="<?= htmlspecialchars($min_events) ?>">
                </div>
                <div>
                    <label>Мин. сумма баллов:</label>
                    <input type="number" name="min_points" value="<?= htmlspecialchars($min_points) ?>">
                </div>
                <div style="display: flex; align-items: end;">
                    <button type="submit" class="btn" style="width: 100%;">Применить фильтры</button>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h2>📊 Список кандидатов (<?= count($candidates) ?>)</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ФИО</th>
                        <th>Возраст</th>
                        <th>Город</th>
                        <th>Мероприятий</th>
                        <th>Ср. балл за мероприятие</th>
                        <th>Общий рейтинг</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($candidates as $candidate): ?>
                        <tr>
                            <td><?= htmlspecialchars($candidate['full_name']) ?></td>
                            <td><?= $candidate['age'] ?? '-' ?></td>
                            <td><?= htmlspecialchars($candidate['city'] ?? '-') ?></td>
                            <td><?= $candidate['events_count'] ?></td>
                            <td><?= round($candidate['avg_points_per_event'], 1) ?></td>
                            <td><strong style="color: #667eea;"><?= $candidate['total_points'] ?></strong></td>
                            <td>
                                <a href="profile.php?id=<?= $candidate['id'] ?>" class="btn">Профиль</a>
                                <a href="?export_pdf=1&user_id=<?= $candidate['id'] ?>" class="btn">PDF</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>