<?php
global $pdo;
require 'config.php';

$error = '';
$success = '';
$valid_token = false;

if (isset($_GET['token'])) {
    $token = sanitizeInput($_GET['token']);

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token]);

        if ($stmt->rowCount() > 0) {
            $valid_token = true;
            $user_id = $stmt->fetch()['id'];

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $new_password = sanitizeInput($_POST['new_password']);
                $confirm_password = sanitizeInput($_POST['confirm_password']);

                if ($new_password !== $confirm_password) {
                    $error = "Parolele nu coincid!";
                } elseif (strlen($new_password) < 8) {
                    $error = "Parola trebuie să conțină minim 8 caractere!";
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                    $stmt->execute([$hashed_password, $user_id]);

                    $success = "Parola a fost resetată cu succes!";
                    header("Refresh: 2; URL=login.php");
                }
            }
        }
    } catch (PDOException $e) {
        $error = "Eroare la procesare: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetare Parolă - Fashion Men</title>
    <link rel="stylesheet" href="folder_cu%20_css/login.css">
</head>
<body>

<img src="imagini/fundal.jpg" alt="Fundal fashion" class="background-image">

<div class="auth-container">
    <h1>Resetare Parolă</h1>

    <?php if (!$valid_token): ?>
        <div class="alert error">Link invalid sau expirat!</div>
        <div class="auth-footer">
            <a href="forgot-password.php">Solicită alt link</a>
        </div>
    <?php else: ?>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?token=' . $token ?>">
            <div class="form-group">
                <label>Parolă Nouă:</label>
                <input type="password" name="new_password" required minlength="8">
            </div>

            <div class="form-group">
                <label>Confirmă Parola:</label>
                <input type="password" name="confirm_password" required minlength="8">
            </div>

            <button type="submit" class="btn">Resetează Parola</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>