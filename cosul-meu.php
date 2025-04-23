<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];

    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product_name,
        'price' => $product_price
    ];
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'];
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coșul meu</title>
    <link rel="stylesheet" href="folder_cu%20_css/cosul-meu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<header>
    <h1>Coșul meu</h1>
    <p>Verifică și finalizează comanda.</p>
</header>

<nav>
    <div class="menu-toggle">
        <i class="fas fa-bars"></i>
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Acasă</a></li>
        <li><a href="haine.php">Haine</a></li>
        <li><a href="incaltaminte.php">Încălțăminte</a></li>
        <li><a href="accesorii.php">Accesorii</a></li>
    </ul>
    <a href="cosul-meu.php" class="nav-icon">
        <i class="fas fa-shopping-bag"></i>
        <span class="badge"><?php echo count($_SESSION['cart']); ?></span>
    </a>
</nav>

<div class="container">
    <div id="cart-items">
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Coșul tău este gol.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <li>
                        <?php echo htmlspecialchars($item['name']); ?> - <?php echo $item['price']; ?> Lei
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <p>Total: <span id="cart-total"><?php echo $total; ?></span> Lei</p>
    <button onclick="finalizeazaComanda()">Finalizează Comanda</button>
</div>

<footer>
    <p>&copy; 2025 Magazin de Haine Online. Toate drepturile rezervate.</p>
</footer>

<script src="folder-cu-js/cosul-meu.js"></script>
</body>
</html>