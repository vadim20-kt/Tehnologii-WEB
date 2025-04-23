<?php
global $pdo;
session_start();
require_once 'config.php';

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Neautorizat', 401);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) throw new Exception('Date invalide', 400);

    $required = ['address_name', 'street', 'city'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Completează câmpul '{$field}'", 400);
        }
    }

    $userCheck = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $userCheck->execute([$_SESSION['user_id']]);
    if (!$userCheck->fetch()) {
        throw new Exception("Utilizator invalid", 404);
    }

    $pdo->beginTransaction();

    if (!empty($data['is_default'])) {
        $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?")
            ->execute([$_SESSION['user_id']]);
    }

    if (empty($data['id'])) {
        $stmt = $pdo->prepare("INSERT INTO user_addresses (...) VALUES (...)");
        $stmt->execute([...]);
        $addressId = $pdo->lastInsertId();
        $message = "Adresa a fost adăugată";
    } else {
        $stmt = $pdo->prepare("UPDATE user_addresses SET ... WHERE id = ? AND user_id = ?");
        $stmt->execute([...]);
        $addressId = $data['id'];
        $message = "Adresa a fost actualizată";
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => $message,
        'address_id' => $addressId
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Eroare la salvarea adresei']);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>