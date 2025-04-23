<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazin de Haine Online</title>
    <link rel="stylesheet" href="folder_cu%20_css/contul-meu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<header class="site-header">
    <div class="header-container">
        <div class="logo">
            <h1>Magazin de Haine Online</h1>
        </div>

        <nav class="main-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Acasă</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="comenzile-mele.php"><i class="fas fa-list-alt"></i> Comenzile mele</a></li>
                    <li><a href="cosul-meu.php"><i class="fas fa-shopping-cart"></i> Coșul meu</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Deconectare</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Autentificare</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Înregistrare</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<main class="main-content">