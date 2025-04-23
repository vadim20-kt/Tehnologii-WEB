<?php
global $pdo;
require 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['logged_in'] = true;
            header("Location: index.php");
            exit();
        } else {
            $error = "Email sau parolă incorectă!";
        }
    } catch (PDOException $e) {
        $error = "Eroare la autentificare!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare - Fashion Men</title>
    <link rel="stylesheet" href="folder_cu%20_css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<img src="imagini/fundal.jpg" alt="Fundal fashion" class="background-image">

<div class="auth-container">
    <h1>Autentificare</h1>
    <p class="subtitle">Accesează-ți contul pentru a descoperi stilul tău!</p>

    <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
        <div class="alert success">Înregistrare reușită! Te poți autentifica.</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required value="<?= isset($_SESSION['registration_email']) ? $_SESSION['registration_email'] : '' ?>">
            <?php unset($_SESSION['registration_email']); ?>
        </div>

        <div class="form-group">
            <label>Parolă:</label>


            <div class="password-wrapper">
                <input type="password" name="password" id="password" required
                       autocomplete="new-password"
                       readonly
                       onfocus="this.removeAttribute('readonly')"
                       style="background-image: none !important;">
                <button type="button" class="toggle-password" aria-label="Arată parola">
                    <i class="fas fa-eye"></i>
                    <i class="fas fa-eye-slash" style="display:none;"></i>
                </button>
            </div>


            <a href="forgot-password.php" class="forgot-password">Ai uitat parola?</a>
        </div>

        <button type="submit" class="btn">Autentifică-te</button>
    </form>

    <div class="auth-footer">
        Nu ai cont? <a href="register.php">Înregistrează-te</a>
    </div>
</div>
<script src="folder-cu-js/login.js"></script>
</body>
</html>