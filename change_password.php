<?php

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

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fashion_db";

try {
    if (!session_start()) {
        throw new Exception("Eroare la inițializarea sesiunii.");
    }

    error_log("Sesiune inițială în change_password.php: " . json_encode($_SESSION));
    error_log("Token CSRF primit: " . ($_POST['csrf_token'] ?? 'N/A'));
    error_log("Token CSRF în sesiune: " . ($_SESSION['csrf_token'] ?? 'N/A'));
    error_log("Session ID: " . session_id());
    error_log("Session data before CSRF check: " . json_encode($_SESSION));
    error_log("Received CSRF token: " . ($_POST['csrf_token'] ?? 'N/A'));

    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Utilizatorul nu este autentificat.");
    }

    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception("Token CSRF invalid.");
    }

    error_log("Date POST primite: " . json_encode($_POST));

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Conexiune eșuată: " . $conn->connect_error);
    }

    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Eroare la setarea codificării UTF-8: " . $conn->error);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Metodă nepermisă. Folosește POST.");
    }

    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        throw new Exception("Toate câmpurile sunt obligatorii.");
    }

    if ($new_password !== $confirm_password) {
        throw new Exception("Parolele noi nu coincid.");
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
        throw new Exception("Noua parolă trebuie să aibă minim 8 caractere, incluzând o literă mare, o literă mică, un număr și un caracter special.");
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Eroare la pregătirea interogării: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Eroare la executarea interogării: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Utilizatorul nu a fost găsit.");
    }

    $user = $result->fetch_assoc();

    if (!password_verify($current_password, $user['password'])) {
        throw new Exception("Parola curentă este incorectă.");
    }

    $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
    if ($new_password_hashed === false) {
        throw new Exception("Eroare la generarea hash-ului pentru parolă.");
    }

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Eroare la pregătirea interogării de actualizare: " . $conn->error);
    }

    $stmt->bind_param("si", $new_password_hashed, $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Eroare la actualizarea parolei: " . $stmt->error);
    }

    $new_csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $new_csrf_token;

    error_log("Nou token CSRF generat în change_password.php: " . $new_csrf_token);

    echo json_encode([
        'success' => true,
        'message' => 'Parola a fost schimbată cu succes!',
        'new_csrf_token' => $new_csrf_token
    ]);

    $stmt->close();
    $conn->close();

    exit;

} catch (Exception $e) {
    error_log("Eroare în change_password.php: " . $e->getMessage());
    error_log("Metoda: " . $_SERVER['REQUEST_METHOD']);
    error_log("Date POST: " . json_encode($_POST));
    error_log("Sesiune: " . json_encode($_SESSION));
    error_log("URL cerere: " . $_SERVER['REQUEST_URI']);
    error_log("Stivă de eroare: " . $e->getTraceAsString());

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage() ?: 'Eroare necunoscută'
    ]);
    exit;
} catch (Throwable $t) {
    error_log("Eroare neașteptată în change_password.php: " . $t->getMessage());
    error_log("Stivă de eroare: " . $t->getTraceAsString());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Eroare internă a serverului'
    ]);
    exit;
}