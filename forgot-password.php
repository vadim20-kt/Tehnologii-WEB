<?php
global $pdo;
require 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($_POST['email']);

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $stmt->execute([$token, $expires, $email]);

            $reset_link = "http://localhost/reset-password.php?token=$token";
            $success = "Link de resetare trimis pe email!";
        } else {
            $error = "Acest email nu există în sistem!";
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
    <title>Recuperare Parolă - Fashion Men</title>
    <link rel="stylesheet" href="folder_cu%20_css/login.css">
</head>
<body>

<img src="imagini/fundal.jpg" alt="Fundal fashion" class="background-image">

<div class="auth-container">
    <h1>Recuperare Parolă</h1>

    <?php if ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>

        <button type="submit" class="btn">Trimite Link de Resetare</button>
    </form>

    <div class="auth-footer">
        <a href="login.php">Înapoi la autentificare</a>
    </div>
</div>
</body>
</html>