<?php
session_start();

// l'utilisateur doit être connecté et avoir un rôle autorisé 
if (!isset($_SESSION["user"]) || $_SESSION["user"]["ip"] !== $_SERVER["REMOTE_ADDR"]) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION["user"]["position"] ?? '';
if (!in_array($role, ['Admin', 'Préparateur'])) {
    header("Location: my_account.php");
    exit;
}


$dsn = "mysql:host=localhost;dbname=dataTeam;charset=utf8mb4";
$db  = new PDO($dsn, "root", "root");

// 3. Récupération des produits
$stmt = $db->query("SELECT * FROM stock ORDER BY productName ASC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock - Wacdo</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>

<h1>Gestion du Stock</h1>
<p>Connecté en tant que : <strong><?= htmlspecialchars($_SESSION["user"]["firstname"]) ?></strong> (<?= htmlspecialchars($role) ?>)</p>

<p><a href="my_account.php">← Retour à mon compte</a></p>

<?php if (empty($products)): ?>
    <p>Aucun produit en stock pour le moment.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom du produit</th>
                <th>Catégorie</th>
                <th>Prix (€)</th>
                <th>Quantité</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr class="<?= $product['quantity'] <= 5 ? 'low-stock' : '' ?>">
                    <td><?= htmlspecialchars($product['id']) ?></td>
                    <td><?= htmlspecialchars($product['productName']) ?></td>
                    <td><?= htmlspecialchars($product['productCategory']) ?></td>
                    <td><?= number_format($product['price'], 2, ',', ' ') ?> €</td>
                    <td><?= htmlspecialchars($product['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>