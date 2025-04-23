<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metoda nepermisă']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Neautorizat']);
    exit;
}

try {
    $pdo = getPDO();

    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $newsletter = isset($input['newsletter']) && $input['newsletter'] ? 1 : 0;
    $promoNotifications = isset($input['promo_notifications']) && $input['promo_notifications'] ? 1 : 0;
    $smsNotifications = isset($input['sms_notifications']) && $input['sms_notifications'] ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE users SET 
                          newsletter = ?,
                          promo_notifications = ?,
                          sms_notifications = ?
                          WHERE id = ?");

    $stmt->execute([$newsletter, $promoNotifications, $smsNotifications, $_SESSION['user_id']]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log('Eroare preferințe: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Eroare la salvarea preferințelor']);
} catch (Exception $e) {
    error_log('Eroare generală: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Eroare internă']);
}
?>