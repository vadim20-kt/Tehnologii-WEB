<?php
global $pdo;
session_start();
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?")
        ->execute([$_SESSION['user_id']]);

    $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 1 
                          WHERE id = ? AND user_id = ?");
    $stmt->execute([$data['address_id'], $_SESSION['user_id']]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>