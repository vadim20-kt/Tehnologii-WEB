<?php
global $pdo, $pdo;
session_start();
require 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <title>Dashboard</title>
</head>
<body>
<h1>Bun venit, <?php echo htmlspecialchars($user['email']); ?>!</h1>
<a href="logout.php">Deconectare</a>
</body>
</html>