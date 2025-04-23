<?php
session_start();

require_once 'config.php';

$userIsLoggedIn = isset($_SESSION['user_id']);
$userName = '';

if ($userIsLoggedIn && isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && isset($user['full_name'])) {
            $userName = $user['full_name'];
        }
    } catch (PDOException $e) {
        error_log("Eroare DB: " . $e->getMessage());
        $userName = "Utilizator";
    }
}

$titluPagina = "Haine";
$descrierePagina = "Cele mai noi tendințe în haine.";
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haine</title>
    <link rel="stylesheet" href="folder_cu%20_css/haine.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<header>
    <h1><?php echo $titluPagina; ?></h1>
    <p><?php echo $descrierePagina; ?></p>
</header>

<nav>
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <div class="search-container">
        <button class="search-btn">
            <i class="fas fa-search"></i>
        </button>
        <div class="search-content">
            <input type="text" id="search-input" placeholder="Caută în tot magazinul...">
        </div>
    </div>
    <div class="nav-center-container">
        <ul class="nav-links">
            <li><a href="index.php">Acasă</a></li>
            <li><a href="incaltaminte.php">Încălțăminte</a></li>
            <li><a href="accesorii.php">Accesorii</a></li>
        </ul>
    </div>
    <?php if ($userIsLoggedIn): ?>
        <div class="user-dropdown">
            <button class="user-btn">
                <i class="fas fa-user"></i>
                <span><?php echo htmlspecialchars(explode(' ', $userName)[0]); ?></span>
                <span class="nav-icon">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="badge">0</span>
                </span>
            </button>
            <div class="dropdown-content">
                <a href="contul-meu.php"><i class="fas fa-user-circle"></i> Contul meu</a>
                <a href="cosul-meu.php"><i class="fas fa-shopping-bag"></i> Coș de cumpărături</a>
                <a href="logout.php" id="logout-btn"><i class="fas fa-sign-out-alt"></i> Deconectare</a>
            </div>
        </div>
    <?php else: ?>
        <a href="login.php" class="user-btn">
            <i class="fas fa-user"></i>
            <span>Autentificare</span>
        </a>
    <?php endif; ?>
</nav>

<div class="container">
    <div class="category-list">
        <?php
        $categorii = [
            'tricouri' => ['nume' => 'Tricouri'],
            'blugi' => ['nume' => 'Blugi'],
            'geci-jachete' => ['nume' => 'Geci și Jachete'],
            'paltoane' => ['nume' => 'Paltoane']
        ];

        foreach ($categorii as $id => $categorie) {
            echo '<div class="category-card" onclick="afiseazaProduse(\'' . $id . '\')" data-category="' . $id . '">
                      <h2>' . $categorie['nume'] . '</h2>
                  </div>';
        }
        ?>
    </div>
    <div id="search-results" style="display: none;"></div>
    <div id="product-container"></div>
</div>

<footer>
    <p>© <?php echo date("Y"); ?> Magazin de Haine Online. Toate drepturile rezervate.</p>
</footer>

<script src="folder-cu-js/haine.js"></script>
<script>
    window.currentCategory = 'haine';
</script>
</body>
</html>