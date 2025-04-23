<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    error_log("Cerere primită în delete_address.php");

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Neautorizat', 401);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) {
        throw new Exception('Date JSON invalide', 400);
    }

    if (empty($data['address_id'])) {
        throw new Exception('ID adresă lipsă', 400);
    }

    if (!isset($data['csrf_token']) || !isset($_SESSION['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Token CSRF invalid', 400);
    }

    error_log('Date primite în delete_address.php: ' . json_encode($data));
    error_log('Sesiune în delete_address.php: ' . json_encode($_SESSION));

    $pdo = getPDO();
    if (!$pdo) {
        throw new Exception('Eroare la conectarea la baza de date', 500);
    }

    $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$data['address_id'], $_SESSION['user_id']]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Adresa nu există sau nu aparține utilizatorului', 404);
    }

    echo json_encode(['success' => true, 'message' => 'Adresa a fost ștearsă cu succes']);
    exit;

} catch (Exception $e) {
    error_log("Eroare în delete_address.php: " . $e->getMessage());
    error_log("Sesiune: " . json_encode($_SESSION));
    error_log("Date primite: " . file_get_contents('php://input'));
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
} catch (Throwable $t) {
    error_log("Eroare neașteptată în delete_address.php: " . $t->getMessage());
    error_log("Stivă de eroare: " . $t->getTraceAsString());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Eroare internă a serverului']);
    exit;
}