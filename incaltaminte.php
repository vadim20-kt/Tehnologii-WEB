<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Încălțăminte</title>
    <link rel="stylesheet" href="folder_cu%20_css/incaltaminte.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php
$titluPagina = "Încălțăminte";
$descrierePagina = "Cele mai noi tendințe în încălțăminte.";
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
        <li><a href="accesorii.php">Accesorii</a></li>
    </ul>
    <a href="cosul-meu.php" class="nav-icon">
        <i class="fas fa-shopping-bag"></i>
        <span class="badge">0</span>
    </a>
</nav>

<div class="container">

    <div class="category-list">
        <div class="category-card" onclick="afiseazaProduse('sport')">
            <h2>Încălțăminte Sport</h2>
        </div>
        <div class="category-card" onclick="afiseazaProduse('casual')">
            <h2>Încălțăminte Casual</h2>
        </div>
        <div class="category-card" onclick="afiseazaProduse('eleganta')">
            <h2>Încălțăminte Elegantă</h2>
        </div>
        <div class="category-card" onclick="afiseazaProduse('sandale')">
            <h2>Sandale</h2>
        </div>
    </div>

    <?php
    function afiseazaProduse($categorie) {
        $produse = [
            'sport' => [
                ['nume' => 'Buțe', 'pret' => 1600, 'imagine' => 'imagini/bute.jpg', 'id_marime' => 'marime-bute', 'id_cantitate' => 'cantitate-bute'],
                ['nume' => 'Bampuri', 'pret' => 800, 'imagine' => 'imagini/bampuri.jpg', 'id_marime' => 'marime-adidasi-sport', 'id_cantitate' => 'cantitate-adidasi-sport']
            ],
            'casual' => [
                ['nume' => 'Bocani', 'pret' => 1600, 'imagine' => 'imagini/0001262_ghete-barbati-piele-naturala-900-blue_465.jpeg', 'id_marime' => 'marime-bocani', 'id_cantitate' => 'cantitate-bocani'],
                ['nume' => 'Ghete', 'pret' => 700, 'imagine' => 'imagini/ghete.jpg', 'id_marime' => 'marime-pantofi-casual', 'id_cantitate' => 'cantitate-pantofi-casual']
            ],
            'eleganta' => [
                ['nume' => 'Pantofi', 'pret' => 1200, 'imagine' => 'imagini/27-600x800.webp', 'id_marime' => 'marime-pantofii-eleganti', 'id_cantitate' => 'cantitate-pantofii-eleganti']
            ],
            'sandale' => [
                ['nume' => 'Sandale', 'pret' => 600, 'imagine' => 'imagini/SANDALEBARBATIMELS95017NEGRE_D.webp', 'id_marime' => 'marime-sandale', 'id_cantitate' => 'cantitate-sandale'],
                ['nume' => 'Sandale Puma Softride', 'pret' => 800, 'imagine' => 'imagini/san.jpg', 'id_marime' => 'marime-pantoffi-eleganti', 'id_cantitate' => 'cantitate-pantoffi-eleganti'],
                ['nume' => 'Sandale Puma Shibui Mule', 'pret' => 700, 'imagine' => 'imagini/sandaleee.jpg', 'id_marime' => 'marime-pantofi-eleganti', 'id_cantitate' => 'cantitate-pantofi-eleganti']
            ]
        ];

        if (isset($produse[$categorie])) {
            echo '<div id="' . $categorie . '" class="product-grid">';
            foreach ($produse[$categorie] as $produs) {
                echo '
                    <div class="product">
                        <img src="' . $produs['imagine'] . '" alt="' . $produs['nume'] . '">
                        <h3>' . $produs['nume'] . '</h3>
                        <p class="price">' . number_format($produs['pret'], 2, ',', '.') . ' MDL</p>
                        <select id="' . $produs['id_marime'] . '">
                            <option value="36">Mărime 36</option>
                            <option value="37">Mărime 37</option>
                            <option value="38">Mărime 38</option>
                            <option value="39">Mărime 39</option>
                            <option value="40">Mărime 40</option>
                            <option value="41">Mărime 41</option>
                            <option value="42">Mărime 42</option>
                            <option value="43">Mărime 43</option>
                            <option value="44">Mărime 44</option>
                            <option value="45">Mărime 45</option>
                        </select>
                        <input type="number" id="' . $produs['id_cantitate'] . '" min="1" value="1" placeholder="Cantitate">
                        <button onclick="adaugaInCos(\'' . $produs['nume'] . '\', ' . $produs['pret'] . ', \'' . $produs['id_marime'] . '\', \'' . $produs['id_cantitate'] . '\')">Adaugă în Coș</button>
                    </div>';
            }
            echo '</div>';
        }
    }
    ?>

    <?php
    afiseazaProduse('sport');
    afiseazaProduse('casual');
    afiseazaProduse('eleganta');
    afiseazaProduse('sandale');
    ?>
</div>

<footer>
    <p>© <?php echo date("Y"); ?> Magazin de Haine Online. Toate drepturile rezervate.</p>
</footer>

<script src="folder-cu-js/incaltaminte.js"></script>
</body>
</html>