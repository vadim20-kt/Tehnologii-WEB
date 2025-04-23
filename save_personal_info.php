<?php
global $pdo;
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Neautorizat']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET 
                         full_name = ?,
                         email = ?,
                         phone = ?
                         WHERE id = ?");

    $stmt->execute([
        $_POST['full_name'],
        $_POST['email'],
        $_POST['phone'],
        $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Eroare la actualizare: ' . $e->getMessage()]);
}
?>