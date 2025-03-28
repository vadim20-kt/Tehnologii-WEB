<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accesorii</title>
    <link rel="stylesheet" href="folder_cu%20_css/accesorii.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php
$titluPagina = "Accesorii";
$descrierePagina = "Cele mai noi tendințe în accesorii.";
?>

<header>
    <h1><?php echo $titluPagina; ?></h1>
    <p><?php echo $descrierePagina; ?></p>
</header>

<nav>
    <div class="menu-toggle">
        <i class="fas fa-bars"></i>
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Acasă</a></li>
        <li><a href="haine.php">Haine</a></li>
        <li><a href="incaltaminte.php">Încălțăminte</a></li>
    </ul>
    <a href="cosul-meu.php" class="nav-icon">
        <i class="fas fa-shopping-bag"></i>
        <span class="badge">0</span>
    </a>
</nav>

<div class="container">
    <div class="category-list">
        <?php
        $categorii = [
            'centuri' => 'Curele',
            'ceasuri' => 'Ceasuri',
            'ochelari' => 'Ochelari',
            'geanti' => 'Geanți'
        ];

        foreach ($categorii as $id => $nume) {
            echo '<div class="category-card" onclick="afiseazaProduse(\'' . $id . '\')">
                          <h2>' . $nume . '</h2>
                      </div>';
        }
        ?>
    </div>

    <?php
    $produse = [
        'centuri' => [
            ['nume' => 'Curea din Piele', 'pret' => 300, 'imagine' => 'imagini/curea piele.jpg'],
            ['nume' => 'Curea Casual', 'pret' => 250, 'imagine' => 'imagini/casual.jpg'],
            ['nume' => 'Curea Elastică', 'pret' => 280, 'imagine' => 'imagini/curea elastica.jpg']
        ],
        'ceasuri' => [
            ['nume' => 'Ceas Clasic', 'pret' => 20000, 'imagine' => 'imagini/ceas clasic.jpg'],
            ['nume' => 'Smartwatch', 'pret' => 500, 'imagine' => 'imagini/smartwatch.jpg'],
            ['nume' => 'Ceas Sport', 'pret' => 350, 'imagine' => 'imagini/ceas sport.jpg']
        ],
        'ochelari' => [
            ['nume' => 'Ochelari Thom Richard', 'pret' => 300, 'imagine' => 'imagini/thom.jpg'],
            ['nume' => 'Ochelari Marc Jhon', 'pret' => 400, 'imagine' => 'imagini/marc.jpg'],
            ['nume' => 'Ochelari Matrix', 'pret' => 350, 'imagine' => 'imagini/matrix.jpg']
        ],
        'geanti' => [
            ['nume' => 'Geantă de Umar', 'pret' => 400, 'imagine' => 'imagini/umar.webp'],
            ['nume' => 'Geanta Smart Business', 'pret' => 450, 'imagine' => 'imagini/smart bissnes.jpg'],
            ['nume' => 'Geantă Sport', 'pret' => 380, 'imagine' => 'imagini/beanta sport.jpg']
        ]
    ];

    foreach ($produse as $categorie => $listaProduse) {
        echo '<div id="' . $categorie . '" class="product-grid" style="display: ' . ($categorie === 'centuri' ? 'block' : 'none') . ';">';
        foreach ($listaProduse as $index => $produs) {
            echo '<div class="product">
                          <img src="' . $produs['imagine'] . '" alt="' . $produs['nume'] . '">
                          <h3>' . $produs['nume'] . '</h3>
                          <p class="price">' . number_format($produs['pret'], 2, ',', '.') . ' Lei</p>
                          <select id="marime-' . $categorie . '-' . ($index + 1) . '">
                              <option value="S">S</option>
                              <option value="M">M</option>
                              <option value="L">L</option>
                          </select>
                          <input type="number" id="cantitate-' . $categorie . '-' . ($index + 1) . '" min="1" value="1" placeholder="Cantitate">
                          <button onclick="adaugaInCos(\'' . $produs['nume'] . '\', ' . $produs['pret'] . ', \'marime-' . $categorie . '-' . ($index + 1) . '\', \'cantitate-' . $categorie . '-' . ($index + 1) . '\')">Adaugă în Coș</button>
                      </div>';
        }
        echo '</div>';
    }
    ?>
</div>

<footer>
    <p>© <?php echo date("Y"); ?> Magazin de Haine Online. Toate drepturile rezervate.</p>
</footer>

<script src="folder-cu-js/accesorii.js"></script>
</body>
</html>

