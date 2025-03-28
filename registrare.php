<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Criptare parolă

    // Aici poți adăuga validări suplimentare (ex: verificare email unic)
    // Exemplu simplu (fără bază de date):
    $_SESSION['registered_email'] = $email;
    header("Location: login.php?registration=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare | Moda pentru Bărbați</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="folder_cu%20_css/login.css"> <!-- Folosește același CSS pentru consistență -->
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <h1>Înregistrează-te</h1>
        <p>Completează datele pentru a crea un cont.</p>
    </div>

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
            </div>
        </div>

        <div class="input-group">
            <label for="confirm_password">Confirmă parola</label>
            <div class="input-field">
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Reintrodu parola">
            </div>
        </div>

        <button type="submit" class="login-button">Înregistrează-te</button>
    </form>

    <div class="login-footer">
        <p>Ai deja un cont? <a href="login.php">Autentifică-te</a></p>
    </div>
</div>
</body>
</html>