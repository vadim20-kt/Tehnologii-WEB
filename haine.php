<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haine</title>
    <link rel="stylesheet" href="folder_cu%20_css/haine.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php
$titluPagina = "Haine";
$descrierePagina = "Cele mai noi tendințe în haine.";
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
        <li><a href="incaltaminte.php">Încălțăminte</a></li>
        <li><a href="accesorii.php">Accesorii</a></li>
    </ul>
    <a href="cosul-meu.php" class="nav-icon">
        <i class="fas fa-shopping-bag"></i>
        <span class="badge">0</span>
    </a>
</nav>

<div class="container">

    <div class="category-list">
        <div class="category-card" onclick="afiseazaProduse('tricouri')">
            <h2>Tricouri</h2>
        </div>
        <div class="category-card" onclick="afiseazaProduse('blugi')">
            <h2>Blugi</h2>
        </div>
        <div class="category-card" onclick="afiseazaProduse('geci-jachete')">
            <h2>Geci și Jachete</h2>
        </div>
        <div class="category-card" onclick="afiseazaProduse('paltoane')">
            <h2>Paltoane</h2>
        </div>
    </div>

    <?php
    function afiseazaProduse($categorie) {
        $produse = [
            'tricouri' => [
                ['nume' => 'Tricou Alb', 'pret' => 300, 'imagine' => 'imagini/Categorie Tricou Alb Barbat-771x1000.png', 'id_marime' => 'marime-tricou-alb', 'id_cantitate' => 'cantitate-tricou-alb'],
                ['nume' => 'Tricou Negru', 'pret' => 350, 'imagine' => 'imagini/800x800_VBTSBAGEP42A15999-01.jpg', 'id_marime' => 'marime-tricou-negru', 'id_cantitate' => 'cantitate-tricou-negru'],
                ['nume' => 'Tricou Roșu', 'pret' => 320, 'imagine' => 'imagini/Без названия.jpg', 'id_marime' => 'marime-tricou-rosu', 'id_cantitate' => 'cantitate-tricou-rosu']
            ],
            'blugi' => [
                ['nume' => 'Blugi Albaștri', 'pret' => 400, 'imagine' => 'imagini/1690-blugi-barbati-online-soul-republic-c_lqno-vr.jpg', 'id_marime' => 'marime-blugi-albastri', 'id_cantitate' => 'cantitate-blugi-albastri'],
                ['nume' => 'Blugi Negri', 'pret' => 450, 'imagine' => 'imagini/res_b9eb54c49f1a57f0a113a80f40d38bf5.jpg', 'id_marime' => 'marime-blugi-negri', 'id_cantitate' => 'cantitate-blugi-negri'],
                ['nume' => 'Blugi Gri', 'pret' => 420, 'imagine' => 'imagini/rum_pl_Blugi-gri-slim-fit-Bolf-KX759-C-89448_6.jpg', 'id_marime' => 'marime-blugi-gri', 'id_cantitate' => 'cantitate-blugi-gri']
            ],
            'geci-jachete' => [
                ['nume' => 'Geacă de Primăvară', 'pret' => 800, 'imagine' => 'imagini/111111111111zsw5.jpg', 'id_marime' => 'marime-geaca-primavara', 'id_cantitate' => 'cantitate-geaca-primavara'],
                ['nume' => 'Jachetă Vânt', 'pret' => 700, 'imagine' => 'imagini/e71214ae-bcc3-4d8c-963c-9870c57df15b.jpg', 'id_marime' => 'marime-jacheta-vant', 'id_cantitate' => 'cantitate-jacheta-vant'],
                ['nume' => 'Geacă de Iarnă', 'pret' => 900, 'imagine' => 'imagini/994820alaska-man-navy.webp', 'id_marime' => 'marime-geaca-iarna', 'id_cantitate' => 'cantitate-geaca-iarna']
            ],
            'paltoane' => [
                ['nume' => 'Palton Negru', 'pret' => 1200, 'imagine' => 'imagini/Без названия (1).jpg', 'id_marime' => 'marime-palton-negru', 'id_cantitate' => 'cantitate-palton-negru'],
                ['nume' => 'Palton Bej', 'pret' => 1300, 'imagine' => 'imagini/17-300x400.png', 'id_marime' => 'marime-palton-bej', 'id_cantitate' => 'cantitate-palton-bej'],
                ['nume' => 'Palton Gri', 'pret' => 1250, 'imagine' => 'imagini/Дизайн-без-названия-1-300x400.jpg', 'id_marime' => 'marime-palton-gri', 'id_cantitate' => 'cantitate-palton-gri']
            ]
        ];

        if (isset($produse[$categorie])) {
            echo '<div id="' . $categorie . '" class="product-grid">';
            foreach ($produse[$categorie] as $produs) {
                echo '
                    <div class="product">
                        <img src="' . $produs['imagine'] . '" alt="' . $produs['nume'] . '">
                        <h3>' . $produs['nume'] . '</h3>
                        <p class="price">' . number_format($produs['pret'], 2, ',', '.') . ' Lei</p>
                        <select id="' . $produs['id_marime'] . '">
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="2XL">2XL</option>
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
    afiseazaProduse('tricouri');
    afiseazaProduse('blugi');
    afiseazaProduse('geci-jachete');
    afiseazaProduse('paltoane');
    ?>
</div>

<footer>
    <p>© <?php echo date("Y"); ?> Magazin de Haine Online. Toate drepturile rezervate.</p>
</footer>

<script src="folder-cu-js/haine.js"></script>
</body>
</html>