<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Aici poți adăuga logica de trimitere a unui link de resetare (ex: prin email)
    // Exemplu simplu (fără integrare reală):
    $_SESSION['reset_email'] = $email;
    header("Location: login.php?reset=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperare Parolă | Moda pentru Bărbați</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="folder_cu%20_css/login.css"> <!-- Folosește același CSS -->
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <h1>Ai uitat parola?</h1>
        <p>Introdu email-ul tău pentru a primi un link de resetare.</p>
    </div>

    <form class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <div class="input-group">
            <label for="email">Email</label>
            <div class="input-field">
                <input type="email" id="email" name="email" required placeholder="Introdu adresa de email">
            </div>
        </div>

        <button type="submit" class="login-button">Trimite Link</button>
    </form>

    <div class="login-footer">
        <p>Îți amintești parola? <a href="login.php">Autentifică-te</a></p>
    </div>
</div>
</body>
</html>