<?php
header('Content-Type: application/json');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

session_start();

session_unset();
session_destroy();

setcookie('remember_token', '', time() - 3600, '/');

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($isAjax) {
    echo json_encode([
        'success' => true,
        'message' => 'Deconectare reușită.',
        'redirect' => 'index.php'
    ]);
    exit();
}

header("Location: index.php");
exit();