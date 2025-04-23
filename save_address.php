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

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Neautorizat', 401);
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if ($data === null) {
        throw new Exception('Date JSON invalide', 400);
    }

    error_log('Date primite în save_address.php: ' . json_encode($data));

    if (!isset($data['csrf_token']) || !isset($_SESSION['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('Token CSRF invalid', 400);
    }

    $required = ['address_name', 'street', 'city'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception("Câmpul {$field} este obligatoriu", 400);
        }
    }

    $pdo = getPDO();
    if (!$pdo) {
        throw new Exception('Eroare la conectarea la baza de date', 500);
    }

    $pdo->beginTransaction();

    if (!empty($data['is_default'])) {
        $stmt = $pdo->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }

    if (empty($data['id'])) {
        $stmt = $pdo->prepare("
            INSERT INTO user_addresses 
            (user_id, address_name, street, city, postal_code, country, is_default) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            $data['address_name'],
            $data['street'],
            $data['city'],
            $data['postal_code'] ?? null,
            $data['country'] ?? null,
            !empty($data['is_default']) ? 1 : 0
        ]);
        $addressId = $pdo->lastInsertId();
        $message = 'Adresă adăugată';
    } else {
        $stmt = $pdo->prepare("
            UPDATE user_addresses 
            SET address_name = ?, street = ?, city = ?, postal_code = ?, country = ?, is_default = ?
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([
            $data['address_name'],
            $data['street'],
            $data['city'],
            $data['postal_code'] ?? null,
            $data['country'] ?? null,
            !empty($data['is_default']) ? 1 : 0,
            $data['id'],
            $_SESSION['user_id']
        ]);
        if ($stmt->rowCount() === 0) {
            throw new Exception('Adresa nu există sau nu aparține utilizatorului', 404);
        }
        $addressId = $data['id'];
        $message = 'Adresă actualizată';
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => $message,
        'address_id' => $addressId
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Eroare în save_address.php: " . $e->getMessage());
    error_log("Sesiune: " . json_encode($_SESSION));
    error_log("Date primite: " . file_get_contents('php://input'));
    http_response_code($e->getCode() ?: 400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
} catch (Throwable $t) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Eroare neașteptată în save_address.php: " . $t->getMessage());
    error_log("Stivă de eroare: " . $t->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Eroare internă a serverului: ' . $t->getMessage(),
        'error_line' => $t->getLine()
    ]);
}