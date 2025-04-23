<?php
global $pdo;
session_start();
require 'config.php';

$error = '';
$success = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);

    if (empty($name)) {
        $error = "Numele este obligatoriu!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresă email invalidă!";
    } elseif ($password !== $confirm_password) {
        $error = "Parolele nu coincid!";
    } elseif (strlen($password) < 8) {
        $error = "Parola trebuie să conțină minim 8 caractere!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $error = "Acest email este deja înregistrat!";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $hashed_password]);

                $success = "Cont creat cu succes! Vei fi redirecționat către pagina de autentificare...";
                $_SESSION['registration_email'] = $email;
                header("Refresh: 3; URL=login.php");
            }
        } catch (PDOException $e) {
            $error = "Eroare la înregistrare: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare - Fashion Men</title>
    <link rel="stylesheet" href="folder_cu%20_css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<img src="imagini/fundal.jpg" alt="Fundal fashion" class="background-image">

<div class="auth-container">
    <h1>Înregistrează-te</h1>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" id="registerForm">
        <div class="form-group">
            <label for="name">Nume:</label>
            <input type="text" name="name" id="name" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>

        <div class="form-group">
            <label for="password">Parolă:</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password">
                <button type="button" class="toggle-password" aria-label="Arată/ascunde parola">
                    <i class="fas fa-eye"></i>
                    <i class="fas fa-eye-slash" style="display:none;"></i>
                </button>
            </div>
            <div class="password-strength" id="password-strength"></div>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirmă Parola:</label>
            <div class="password-wrapper">
                <input type="password" name="confirm_password" id="confirm_password" required minlength="8" autocomplete="new-password">
                <button type="button" class="toggle-password" aria-label="Arată/ascunde parola">
                    <i class="fas fa-eye"></i>
                    <i class="fas fa-eye-slash" style="display:none;"></i>
                </button>
            </div>
            <div class="password-match" id="password-match"></div>
        </div>

        <button type="submit" class="btn" id="submitBtn">Înregistrează-te</button>
    </form>

    <div class="auth-footer">
        Ai deja cont? <a href="login.php">Autentifică-te</a>
    </div>
</div>

<script src="folder-cu-js/register.js"></script>
</body>
</html>