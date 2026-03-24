<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'youth_parliament');

// Connect to database
function getDB() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

// Redirect if no permission
function requireRole($role) {
    requireLogin();
    if (!hasRole($role) && !hasRole('admin')) {
        header('Location: dashboard.php');
        exit();
    }
}

// Calculate user rating
function calculateRating($user_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("
        SELECT SUM(ep.points_earned * e.difficulty_coefficient) as total
        FROM event_participants ep
        JOIN events e ON ep.event_id = e.id
        WHERE ep.user_id = ? AND ep.status = 'confirmed'
    ");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Update user total points
function updateUserPoints($user_id) {
    $pdo = getDB();
    $rating = calculateRating($user_id);
    $stmt = $pdo->prepare("UPDATE users SET total_points = ? WHERE id = ?");
    $stmt->execute([$rating, $user_id]);
    return $rating;
}
?>