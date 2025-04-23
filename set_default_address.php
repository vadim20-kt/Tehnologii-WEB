<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Neautorizat', 401);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['address_id'])) {
        throw new Exception('ID adresă lipsă', 400);
    }

    if (!isset($data['csrf_token']) || !isset($_SESSION['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Token CSRF invalid', 400);
    }

    error_log('Date primite în set_default_address.php: ' . json_encode($data));

    $pdo = getPDO();
    if (!$pdo) {
        throw new Exception('Eroare la conectarea la baza de date', 500);
    }

    $pdo->beginTransaction();

    $checkStmt = $pdo->prepare("SELECT id FROM user_addresses WHERE id = ? AND user_id = ?");
    $checkStmt->execute([$data['address_id'], $_SESSION['user_id']]);
    if (!$checkStmt->fetch()) {
        throw new Exception('Adresa nu există sau nu aparține utilizatorului', 404);
    }

    $resetStmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
    $resetStmt->execute([$_SESSION['user_id']]);

    $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 1 WHERE id = ? AND user_id = ?");
    $stmt->execute([$data['address_id'], $_SESSION['user_id']]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Adresa implicită actualizată']);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Eroare în set_default_address.php: " . $e->getMessage());
    error_log("Sesiune: " . json_encode($_SESSION));
    error_log("Date primite: " . file_get_contents('php://input'));
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Throwable $t) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Eroare neașteptată în set_default_address.php: " . $t->getMessage());
    error_log("Stivă de eroare: " . $t->getTraceAsString());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Eroare internă a serverului']);
}