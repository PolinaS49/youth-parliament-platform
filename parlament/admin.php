<?php
require_once 'config.php';
requireRole('admin');

$pdo = getDB();

// Handle event verification
if(isset($_POST['verify_event'])) {
    $event_id = $_POST['event_id'];
    $stmt = $pdo->prepare("UPDATE events SET is_verified = 1 WHERE id = ?");
    $stmt->execute([$event_id]);
    header('Location: admin.php');
    exit();
}

// Handle user role change
if(isset($_POST['change_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);
    header('Location: admin.php');
    exit();
}

// Get pending events
$pending_events = $pdo->query("
    SELECT e.*, u.username as organizer_name
    FROM events e
    JOIN users u ON e.organizer_id = u.id
    WHERE e.is_verified = 0
    ORDER BY e.created_at DESC
")->fetchAll();

// Get all users
$users = $pdo->query("
    SELECT id, username, full_name, email, role, total_points, is_active
    FROM users
    ORDER BY role, total_points DESC
")->fetchAll();

// Get event types and weights
$weights = $pdo->query("
    SELECT category, AVG(difficulty_coefficient) as avg_weight
    FROM events
    GROUP BY category
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора - Молодежный Парламент</title>
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
                <a href="admin.php">Админка</a>
                <a href="logout.php" class="btn btn-danger">Выход</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>🔧 Панель администратора</h1>
        
        <div class="card">
            <h2>⏳ Модерация мероприятий (<?= count($pending_events) ?>)</h2>
            <?php if(count($pending_events) == 0): ?>
                <p>Нет мероприятий на модерацию</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Организатор</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pending_events as $event): ?>
                            <tr>
                                <td><?= htmlspecialchars($event['title']) ?></td>
                                <td><?= htmlspecialchars($event['organizer_name']) ?></td>
                                <td><?= date('d.m.Y', strtotime($event['event_date'])) ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                        <button type="submit" name="verify_event" class="btn btn-success">Подтвердить</button>
                                    </form>
                                 </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>👥 Управление пользователями</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ФИО</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Баллы</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <form method="POST" style="display: flex; gap: 5px;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <select name="new_role">
                                        <option value="participant" <?= $user['role'] == 'participant' ? 'selected' : '' ?>>Участник</option>
                                        <option value="organizer" <?= $user['role'] == 'organizer' ? 'selected' : '' ?>>Организатор</option>
                                        <option value="hr" <?= $user['role'] == 'hr' ? 'selected' : '' ?>>Кадровая служба</option>
                                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Администратор</option>
                                    </select>
                                    <button type="submit" name="change_role" class="btn">Изменить</button>
                                </form>
                            </td>
                            <td><?= $user['total_points'] ?></td>
                            <td>
                                <a href="profile.php?id=<?= $user['id'] ?>" class="btn">Просмотр</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card">
            <h2>⚙️ Настройка весов баллов для типов мероприятий</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Категория</th>
                        <th>Средний коэффициент сложности</th>
                        <th>Рекомендуемый диапазон</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($weights as $weight): ?>
                        <tr>
                            <td><?= $weight['category'] ?></td>
                            <td><?= round($weight['avg_weight'], 2) ?></td>
                            <td>1.0 - 2.0</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="margin-top: 10px; color: #666;">При создании мероприятия организаторы могут указывать коэффициент сложности от 1 до 2.</p>
        </div>
    </div>
</body>
</html>