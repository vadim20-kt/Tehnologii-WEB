<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestionează-ți contul pe Magazin de Haine Online - actualizează informațiile personale, adresele și preferințele.">
    <meta name="keywords" content="magazin haine online, cont utilizator, gestionare adrese">
    <title>Contul meu - Magazin de Haine Online</title>
    <link rel="stylesheet" href="folder_cu%20_css/index.css">
    <link rel="stylesheet" href="folder_cu%20_css/contul-meu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>

<?php
session_start();

error_log("Sesiune inițială în contul-meu.php: " . json_encode($_SESSION));

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

error_log("Token CSRF generat/setat în contul-meu.php: " . $csrf_token);

$numeMagazin = "Magazin de Haine Online";

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
        } else {
            error_log("Utilizatorul nu a fost găsit.");
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Eroare DB: " . $e->getMessage());
        $userName = "Utilizator";
    }
}

$user = [];
$addresses = [];

if ($userIsLoggedIn) {
    try {
        $pdo = getPDO();

        $stmt = $pdo->query("SHOW COLUMNS FROM users");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $selectFields = ['id'];

        if (in_array('full_name', $columns)) {
            $selectFields[] = 'full_name';
        } elseif (in_array('nume_complet', $columns)) {
            $selectFields[] = 'nume_complet AS full_name';
        } elseif (in_array('name', $columns)) {
            $selectFields[] = 'name AS full_name';
        } elseif (in_array('username', $columns)) {
            $selectFields[] = 'username AS full_name';
        }

        $optionalFields = ['email', 'phone', 'newsletter', 'promo_notifications', 'sms_notifications'];
        foreach ($optionalFields as $field) {
            if (in_array($field, $columns)) {
                $selectFields[] = $field;
            }
        }

        $query = "SELECT " . implode(', ', $selectFields) . " FROM users WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            error_log("Utilizatorul nu a fost găsit în baza de date.");
            header("Location: login.php");
            exit;
        }

        $user['full_name'] = $user['full_name'] ?? 'Nespecificat';
        $user['phone'] = $user['phone'] ?? '';
        $user['newsletter'] = $user['newsletter'] ?? 0;
        $user['promo_notifications'] = $user['promo_notifications'] ?? 0;
        $user['sms_notifications'] = $user['sms_notifications'] ?? 0;

        try {
            $addressStmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = ?");
            $addressStmt->execute([$_SESSION['user_id']]);
            $addresses = $addressStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching addresses: " . $e->getMessage());
            $addresses = [];
        }

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        die("Eroare la accesarea datelor. Încercați mai târziu.");
    }
}
?>

<header>
    <h1><?php echo $numeMagazin; ?></h1>
    <p>Gestionați-vă contul și preferințele</p>
</header>

<nav>
    <button class="menu-toggle" aria-label="Deschide meniul">
        <i class="fas fa-bars"></i>
    </button>

    <div class="nav-center-container">
        <ul class="nav-links">
            <li><a href="index.php">Acasă</a></li>
            <li><a href="haine.php">Haine</a></li>
            <li><a href="incaltaminte.php">Încălțăminte</a></li>
            <li><a href="accesorii.php">Accesorii</a></li>
        </ul>
    </div>

    <?php if ($userIsLoggedIn): ?>
        <div class="user-dropdown">
            <button class="user-btn">
                <i class="fas fa-user"></i>
                <span><?php echo explode(' ', $userName)[0]; ?></span>
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

<div class="container account-page">
    <h2>Contul meu</h2>

    <div class="account-sections">
        <section class="account-info">
            <h3>Informații personale</h3>
            <form id="personalInfoForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <div class="form-group">
                    <label>Nume complet</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Telefon</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>">
                </div>
                <div class="error-message" id="personalInfoError"></div>
                <button type="submit" class="save-btn">Salvează modificările</button>
            </form>

            <div class="password-change">
                <h4>Schimbă parola</h4>
                <form id="passwordChangeForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <div class="form-group">
                        <label>Parola curentă</label>
                        <div class="password-wrapper">
                            <input type="password" name="current_password" class="password-field" autocomplete="current-password" required>
                            <span class="password-toggle"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Parola nouă</label>
                        <div class="password-wrapper">
                            <input type="password" name="new_password" class="password-field" autocomplete="new-password" required>
                            <span class="password-toggle"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirmă parola nouă</label>
                        <div class="password-wrapper">
                            <input type="password" name="confirm_password" class="password-field" autocomplete="new-password" required>
                            <span class="password-toggle"><i class="fas fa-eye"></i></span>
                        </div>
                    </div>
                    <div class="error-message" id="passwordChangeError"></div>
                    <button type="submit" class="save-btn">Schimbă parola</button>
                </form>
            </div>
        </section>

        <section class="address-info">
            <h3>Adrese salvate</h3>
            <div class="addresses-container">
                <?php if (!empty($addresses)): ?>
                    <?php foreach ($addresses as $address): ?>
                        <div class="address-card <?= $address['is_default'] ? 'default-address' : '' ?>">
                            <div class="address-header">
                                <h4><?= htmlspecialchars($address['address_name'] ?? 'Nespecificat') ?></h4>
                                <?php if ($address['is_default'] ?? false): ?>
                                    <span class="default-badge">Implicită</span>
                                <?php endif; ?>
                            </div>
                            <p><?= htmlspecialchars($address['street'] ?? '') ?></p>
                            <p><?= htmlspecialchars($address['city'] ?? '') ?>, <?= htmlspecialchars($address['postal_code'] ?? '') ?></p>
                            <p><?= htmlspecialchars($address['country'] ?? '') ?></p>
                            <div class="address-actions">
                                <button class="edit-btn" data-address-id="<?= $address['id'] ?? '' ?>">Modifică</button>
                                <?php if (!($address['is_default'] ?? false)): ?>
                                    <button class="set-default-btn" data-address-id="<?= $address['id'] ?? '' ?>">Setează ca implicită</button>
                                    <button class="delete-btn" data-address-id="<?= $address['id'] ?? '' ?>">Șterge</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-addresses">Nu ai adrese salvate încă.</p>
                <?php endif; ?>
            </div>
            <button class="add-btn" id="addAddressBtn">Adaugă o nouă adresă</button>
        </section>
    </div>

    <section class="preferences">
        <h3>Preferințe</h3>
        <form id="preferencesForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div class="pref-options">
                <label class="checkbox-container">
                    <input type="checkbox" name="newsletter" <?= $user['newsletter'] ? 'checked' : '' ?>>
                    <span class="checkmark"></span>
                    Primește newsletter
                </label>
                <label class="checkbox-container">
                    <input type="checkbox" name="promo_notifications" <?= $user['promo_notifications'] ? 'checked' : '' ?>>
                    <span class="checkmark"></span>
                    Notificări despre promoții
                </label>
                <label class="checkbox-container">
                    <input type="checkbox" name="sms_notifications" <?= $user['sms_notifications'] ? 'checked' : '' ?>>
                    <span class="checkmark"></span>
                    Notificări SMS
                </label>
            </div>
            <div class="error-message" id="preferencesError"></div>
            <button type="submit" class="save-btn">Salvează preferințe</button>
        </form>
    </section>
</div>

<div id="addressModal">
    <div class="modal-content">
        <span class="close-btn">×</span>
        <h3>Adaugă/Editează Adresa</h3>
        <form id="addressForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="address_id" id="address_id">
            <div class="form-group">
                <label for="address_name">Nume adresă</label>
                <input type="text" id="address_name" name="address_name" required>
            </div>
            <div class="form-group">
                <label for="street">Stradă</label>
                <input type="text" id="street" name="street" required>
            </div>
            <div class="form-group">
                <label for="city">Oraș</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="postal_code">Cod poștal</label>
                <input type="text" id="postal_code" name="postal_code" required>
            </div>
            <div class="form-group">
                <label for="country">Țară</label>
                <input type="text" id="country" name="country" required>
            </div>
            <div class="error-message" id="addressError"></div>
            <button type="submit" class="save-btn">Salvează</button>
        </form>
    </div>
</div>

<footer>
    <p>© <?php echo date("Y"); ?> <?php echo $numeMagazin; ?>. Toate drepturile rezervate.</p>
</footer>

<script src="folder-cu-js/contul-meu.js"></script>
</body>
</html>