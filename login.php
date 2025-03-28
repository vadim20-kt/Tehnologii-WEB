<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($email === "test@example.com" && $password === "password123") {
        $_SESSION['logged_in'] = true;
        $_SESSION['email'] = $email;
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Email sau parolă incorectă";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autentificare | Moda pentru Bărbați</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="folder_cu%20_css/login.css">
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <h1>Bine ai venit!</h1>
        <p>Păstrează-ți datele în siguranță!</p>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo $error_message; ?></span>
        </div>
    <?php endif; ?>

    <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="input-group">
            <label for="email">Email</label>
            <div class="input-field">
                <input type="email" id="email" name="email" required placeholder="Introdu adresa de email">
            </div>
        </div>

        <div class="input-group">
            <label for="password">Parolă</label>
            <div class="input-field">
                <input type="password" id="password" name="password" required placeholder="Introdu parola">
                <a href="forgot-password.php" class="forgot-password">Ai uitat parola?</a>
            </div>
        </div>

        <button type="submit" class="login-button">Autentificare</button>
    </form>

    <div class="login-footer">
        <p>Nu ai un cont? <a href="registrare.php">Înregistrează-te</a></p>
    </div>
</div>
<script src="folder-cu-js/login.js"></script>
</body>
</html>