<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"]["ip"] !== $_SERVER["REMOTE_ADDR"]) {
    header("Location: login.php");
    exit;
}

// 1. Connexion BDD
try {
    $dsn = "mysql:host=localhost;dbname=dataTeam;charset=utf8mb4";
    $db  = new PDO($dsn, "root", "root", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion BDD : " . $e->getMessage());
}

// Récupération des messages stockés en session puis nettoyage
$message = $_SESSION['message'] ?? "";
unset($_SESSION['message']);

// 2. Traitement à la soumission du formulaire (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $isMenu = isset($_POST["is_menu"]) ? 1 : 0;

        $items = [
            'burger'  => ['id' => !empty($_POST["burger_id"])  ? (int)$_POST["burger_id"]  : null, 'qty' => (int)($_POST["burger_qty"] ?? 1)],
            'wrap'    => ['id' => !empty($_POST["wrap_id"])    ? (int)$_POST["wrap_id"]    : null, 'qty' => (int)($_POST["wrap_qty"] ?? 1)],
            'salade'  => ['id' => !empty($_POST["salade_id"])  ? (int)$_POST["salade_id"]  : null, 'qty' => (int)($_POST["salade_qty"] ?? 1)],
            'side'    => ['id' => !empty($_POST["side_id"])    ? (int)$_POST["side_id"]    : null, 'qty' => (int)($_POST["side_qty"] ?? 1)],
            'drink'   => ['id' => !empty($_POST["drink_id"])   ? (int)$_POST["drink_id"]   : null, 'qty' => (int)($_POST["drink_qty"] ?? 1)],
            'sauce'   => ['id' => !empty($_POST["sauce_id"])   ? (int)$_POST["sauce_id"]   : null, 'qty' => (int)($_POST["sauce_qty"] ?? 1)],
            'encas'   => ['id' => !empty($_POST["encas_id"])   ? (int)$_POST["encas_id"]   : null, 'qty' => (int)($_POST["encas_qty"] ?? 1)],
            'dessert' => ['id' => !empty($_POST["dessert_id"]) ? (int)$_POST["dessert_id"] : null, 'qty' => (int)($_POST["dessert_qty"] ?? 1)],
        ];

        // Vérification qu'au moins un produit est sélectionné
        $hasProduct = false;
        foreach ($items as $item) {
            if ($item['id'] && $item['qty'] > 0) {
                $hasProduct = true;
                break;
            }
        }

        if (!$hasProduct) {
            throw new Exception("Veuillez choisir au moins un produit.");
        }

        // Transaction SQL
        $db->beginTransaction();

        // 1. Décrémentation du stock
        foreach ($items as $item) {
            if ($item['id'] && $item['qty'] > 0) {
                $update = $db->prepare("UPDATE stock SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
                $update->execute([$item['qty'], $item['id'], $item['qty']]);

                if ($update->rowCount() === 0) {
                    $stmtInfo = $db->prepare("SELECT productName, quantity FROM stock WHERE id = ?");
                    $stmtInfo->execute([$item['id']]);
                    $pInfo = $stmtInfo->fetch();
                    $pName = $pInfo ? $pInfo['productName'] : "sélectionné";
                    $pStock = $pInfo ? $pInfo['quantity'] : 0;

                    throw new Exception("Stock insuffisant pour '$pName' (demandé: {$item['qty']}, restant: $pStock).");
                }
            }
        }

        // 2. Enregistrement de la commande
        $sql = "INSERT INTO orders (
                    is_menu, 
                    burger_id, burger_qty, 
                    wrap_id, wrap_qty, 
                    salade_id, salade_qty, 
                    side_id, side_qty, 
                    drink_id, drink_qty, 
                    sauce_id, sauce_qty, 
                    encas_id, encas_qty, 
                    dessert_id, dessert_qty
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $isMenu,
            $items['burger']['id'],  $items['burger']['id']  ? $items['burger']['qty']  : 0,
            $items['wrap']['id'],    $items['wrap']['id']    ? $items['wrap']['qty']    : 0,
            $items['salade']['id'],  $items['salade']['id']  ? $items['salade']['qty']  : 0,
            $items['side']['id'],    $items['side']['id']    ? $items['side']['qty']    : 0,
            $items['drink']['id'],   $items['drink']['id']   ? $items['drink']['qty']   : 0,
            $items['sauce']['id'],   $items['sauce']['id']   ? $items['sauce']['qty']   : 0,
            $items['encas']['id'],   $items['encas']['id']   ? $items['encas']['qty']   : 0,
            $items['dessert']['id'], $items['dessert']['id'] ? $items['dessert']['qty'] : 0,
        ]);

        $orderId = $db->lastInsertId();
        $db->commit();

        $_SESSION['message'] = "Commande n°" . $orderId . " enregistrée et stock mis à jour avec succès !";

    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $_SESSION['message'] = "Erreur : " . $e->getMessage();
    }

    // Redirection vers la même page pour "nettoyer" la requête POST
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// 3. Récupération des produits du stock pour affichage du formulaire
$burgers  = $db->query("SELECT id, productName, quantity FROM stock WHERE productCategory = 'burgers'")->fetchAll();
$wraps    = $db->query("SELECT id, productName, quantity FROM stock WHERE productCategory = 'wraps'")->fetchAll();
$salades  = $db->query("SELECT id, productName, quantity FROM stock WHERE productCategory = 'salades'")->fetchAll();
$frites   = $db->query("SELECT id, productName, quantity FROM stock WHERE productCategory = 'frites'")->fetchAll();
$boissons = $db->query("SELECT id, productName, quantity FROM stock WHERE productCategory = 'boissons'")->fetchAll();
$sauces   = $db->query("SELECT id, productName, quantity FROM stock WHERE productCategory = 'sauces'")->fetchAll();
$encas    = $db->query("SELECT id, productName, quantity FROM stock WHERE productCategory = 'encas'")->fetchAll();
$desserts = $db->query("SELECT id, productName, quantity FROM stock WHERE productCategory = 'desserts'")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prise de Commande</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-row { display: flex; align-items: center; margin-bottom: 12px; gap: 10px; }
        .form-row label { width: 140px; font-weight: bold; }
        select { width: 280px; padding: 6px; }
        input[type="number"] { width: 60px; padding: 6px; }
        .msg { padding: 12px; font-weight: bold; margin-bottom: 15px; border-radius: 4px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        button { padding: 10px 20px; font-size: 16px; cursor: pointer; }
    </style>
</head>
<body>

<p><a href="my_account.php">← Retour à mon compte</a></p>
<h1>Prise de Commande</h1>

<?php if ($message): ?>
    <div class="msg <?= strpos($message, 'Erreur') !== false ? 'error' : 'success' ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>

<form action="" method="POST">

    <div class="form-row" style="margin-bottom: 20px;">
        <label style="width: auto;">
            <input type="checkbox" name="is_menu" value="1"> Formule Menu ?
        </label>
    </div>

    <!-- BURGER -->
    <div class="form-row">
        <label for="burger">Burger :</label>
        <select name="burger_id" id="burger">
            <option value="">-- Aucun --</option>
            <?php foreach ($burgers as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['productName']) ?> (Stock: <?= $b['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <label style="width: auto;" for="burger_qty">Qté :</label>
        <input type="number" name="burger_qty" id="burger_qty" value="1" min="1">
    </div>

    <!-- WRAP -->
    <div class="form-row">
        <label for="wrap">Wrap :</label>
        <select name="wrap_id" id="wrap">
            <option value="">-- Aucun --</option>
            <?php foreach ($wraps as $w): ?>
                <option value="<?= $w['id'] ?>"><?= htmlspecialchars($w['productName']) ?> (Stock: <?= $w['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <label style="width: auto;" for="wrap_qty">Qté :</label>
        <input type="number" name="wrap_qty" id="wrap_qty" value="1" min="1">
    </div>

    <!-- SALADE -->
    <div class="form-row">
        <label for="salade">Salade :</label>
        <select name="salade_id" id="salade">
            <option value="">-- Aucune --</option>
            <?php foreach ($salades as $s): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['productName']) ?> (Stock: <?= $s['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <label style="width: auto;" for="salade_qty">Qté :</label>
        <input type="number" name="salade_qty" id="salade_qty" value="1" min="1">
    </div>

    <!-- FRITE -->
    <div class="form-row">
        <label for="side">Frite / Potatoes :</label>
        <select name="side_id" id="side">
            <option value="">-- Aucun --</option>
            <?php foreach ($frites as $f): ?>
                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['productName']) ?> (Stock: <?= $f['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <label style="width: auto;" for="side_qty">Qté :</label>
        <input type="number" name="side_qty" id="side_qty" value="1" min="1">
    </div>

    <!-- BOISSON -->
    <div class="form-row">
        <label for="drink">Boisson :</label>
        <select name="drink_id" id="drink">
            <option value="">-- Aucune --</option>
            <?php foreach ($boissons as $d): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['productName']) ?> (Stock: <?= $d['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <label style="width: auto;" for="drink_qty">Qté :</label>
        <input type="number" name="drink_qty" id="drink_qty" value="1" min="1">
    </div>

    <!-- SAUCE -->
    <div class="form-row">
        <label for="sauce">Sauce :</label>
        <select name="sauce_id" id="sauce">
            <option value="">-- Aucune --</option>
            <?php foreach ($sauces as $sauce): ?>
                <option value="<?= $sauce['id'] ?>"><?= htmlspecialchars($sauce['productName']) ?> (Stock: <?= $sauce['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <label style="width: auto;" for="sauce_qty">Qté :</label>
        <input type="number" name="sauce_qty" id="sauce_qty" value="1" min="1">
    </div>

    <!-- ENCAS -->
    <div class="form-row">
        <label for="encas">Encas :</label>
        <select name="encas_id" id="encas">
            <option value="">-- Aucun --</option>
            <?php foreach ($encas as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['productName']) ?> (Stock: <?= $e['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <label style="width: auto;" for="encas_qty">Qté :</label>
        <input type="number" name="encas_qty" id="encas_qty" value="1" min="1">
    </div>

    <!-- DESSERT -->
    <div class="form-row">
        <label for="dessert">Dessert :</label>
        <select name="dessert_id" id="dessert">
            <option value="">-- Aucun --</option>
            <?php foreach ($desserts as $des): ?>
                <option value="<?= $des['id'] ?>"><?= htmlspecialchars($des['productName']) ?> (Stock: <?= $des['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
        <label style="width: auto;" for="dessert_qty">Qté :</label>
        <input type="number" name="dessert_qty" id="dessert_qty" value="1" min="1">
    </div>

    <br>
    <button type="submit">Valider la commande</button>

</form>
<br>
<a href="orders_list.php"><button>Liste des commandes</button></a>

</body>
</html>