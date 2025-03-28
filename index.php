<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazin de Haine Online</title>
    <link rel="stylesheet" href="folder_cu%20_css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php
$numeMagazin = "Magazin de Haine Online";
?>

<header>
    <h1><?php echo $numeMagazin; ?></h1>
    <p>Descoperă cele mai noi tendințe în modă pentru bărbați!</p>
</header>

<nav>
    <div class="menu-toggle">
        <i class="fas fa-bars"></i>
    </div>

    <div class="nav-center-container">
        <ul class="nav-links">
            <li><a href="haine.php">Haine</a></li>
            <li><a href="incaltaminte.php">Încălțăminte</a></li>
            <li><a href="accesorii.php">Accesorii</a></li>
        </ul>
    </div>

    <div class="nav-icons">
        <a href="login.php" class="nav-icon" aria-label="Logare">
            <i class="fas fa-user"></i>
        </a>
        <a href="cosul-meu.php" class="nav-icon">
            <i class="fas fa-shopping-bag"></i>
            <span class="badge">0</span>
        </a>
    </div>
</nav>

<div class="banner">
    <h2 class="banner-text">Descoperă stilul tău unic!</h2>
    <img src="imagini/oVbz2kVJgG1GO6B3-generated_image.jpg" alt="Colecție de modă bărbătească minimalistă și modernă" class="banner-image">
</div>

<div class="container">
    <section class="about-us">
        <h2>Despre Noi</h2>
        <p>
            Bun venit la <strong><?php echo $numeMagazin; ?></strong>! Suntem dedicați să oferim cele mai bune produse de modă pentru bărbați direct la tine acasă.
            Cu o gamă largă de haine, încălțăminte și accesorii, ne propunem să satisfacem toate nevoile tale vestimentare.
            Indiferent de stilul tău, avem ceva special pentru tine!
        </p>
    </section>

    <section class="contact">
        <h2>Contacte</h2>
        <div class="contact-info">
            <div class="contact-card">
                <i class="fas fa-phone"></i>
                <h3>Telefon</h3>
                <p><a href="tel:+373682345678">+373 682 345 678</a></p>
                <p>Luni - Vineri: 09:00 - 18:00</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p><a href="mailto:contact@magazinhaine.md">contact@magazinhaine.md</a></p>
                <p>Răspundem în maxim 24 de ore.</p>
            </div>
            <div class="contact-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Adresă</h3>
                <p>Strada Studenților, Nr. 1</p>
                <p>Chișinău, Moldova</p>
            </div>
        </div>
        <div class="social-links">
            <h3>Urmărește-ne pe:</h3>
            <a href="https://facebook.com" target="_blank" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
            <a href="https://instagram.com" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://twitter.com" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
        </div>
    </section>
</div>

<footer>
    <p>© <?php echo date("Y"); ?> <?php echo $numeMagazin; ?>. Toate drepturile rezervate.</p>
</footer>

<script src="folder-cu-js/index.js"></script>
</body>
</html>
