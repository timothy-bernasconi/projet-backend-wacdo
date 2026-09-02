<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"]["ip"] !== $_SERVER["REMOTE_ADDR"]) {
    header("Location: login.php");
    exit;
}

// Connexion BDD
try {
    $dsn = "mysql:host=localhost;dbname=dataTeam;charset=utf8mb4";
    $db  = new PDO($dsn, "root", "root", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}

// Supprimer une commande et actualiser stock
if (isset($_GET['delete'])) {
    $orderId = (int)$_GET['delete'];

    try {
        // la commande avant suppression
        $stmtOrder = $db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmtOrder->execute([$orderId]);
        $order = $stmtOrder->fetch();

        if ($order) {
            $db->beginTransaction();

            // Mappage des colonnes ID et Quantité correspondantes
            $itemsToRestore = [
                $order['burger_id']  => $order['burger_qty'],
                $order['wrap_id']    => $order['wrap_qty'],
                $order['salade_id']  => $order['salade_qty'],
                $order['side_id']    => $order['side_qty'],
                $order['drink_id']   => $order['drink_qty'],
                $order['sauce_id']   => $order['sauce_qty'],
                $order['encas_id']   => $order['encas_qty'],
                $order['dessert_id'] => $order['dessert_qty'],
            ];

            // Réaugmenter le stock pour chaque produit
            $updateStock = $db->prepare("UPDATE stock SET quantity = quantity + ? WHERE id = ?");
            foreach ($itemsToRestore as $productId => $qty) {
                if ($productId && $qty > 0) {
                    $updateStock->execute([$qty, $productId]);
                }
            }

            // Supprimer la commande
            $deleteOrder = $db->prepare("DELETE FROM orders WHERE id = ?");
            $deleteOrder->execute([$orderId]);

            $db->commit();
            $_SESSION['msg'] = "Commande n°$orderId supprimée et stock récrédité avec succès !";
        }
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $_SESSION['msg'] = "Erreur lors de la suppression : " . $e->getMessage();
    }

    header("Location: orders_list.php");
    exit();
}

$msg = $_SESSION['msg'] ?? "";
unset($_SESSION['msg']);

// Récupération des commandes
$sql = "SELECT 
            o.id, o.is_menu, o.created_at,
            b.productName AS burger, o.burger_qty,
            w.productName AS wrap, o.wrap_qty,
            sl.productName AS salade, o.salade_qty,
            s.productName AS side, o.side_qty,
            d.productName AS drink, o.drink_qty,
            sc.productName AS sauce, o.sauce_qty,
            e.productName AS encas, o.encas_qty,
            des.productName AS dessert, o.dessert_qty
        FROM orders o
        LEFT JOIN stock b ON o.burger_id = b.id
        LEFT JOIN stock w ON o.wrap_id = w.id
        LEFT JOIN stock sl ON o.salade_id = sl.id
        LEFT JOIN stock s ON o.side_id = s.id
        LEFT JOIN stock d ON o.drink_id = d.id
        LEFT JOIN stock sc ON o.sauce_id = sc.id
        LEFT JOIN stock e ON o.encas_id = e.id
        LEFT JOIN stock des ON o.dessert_id = des.id
        ORDER BY o.id DESC";

$orders = $db->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Commandes</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: sans-serif; margin: 30px; }
        .btn-new { background: #28a745; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; }
        .msg { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; margin: 15px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f8f9fa; }
        .btn-delete { color: #dc3545; font-weight: bold; text-decoration: none; }
        .btn-delete:hover { text-decoration: underline; }
        .badge { background: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; font-size: 12px; }
        .menu { background: #007bff; }
    </style>
</head>
<body>

<p><a href="my_account.php">← Retour à mon compte</a></p>
<a href="order.php" class="btn-new">+ Passer une nouvelle commande</a>

<h1>Commandes (<?= count($orders) ?>)</h1>

<?php if ($msg): ?>
    <div class="msg"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <p>Aucune commande enregistrée.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Produits commandés</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td><strong>#<?= $o['id'] ?></strong></td>
                    <td><?= date('d/m H:i', strtotime($o['created_at'])) ?></td>
                    <td>
                        <span class="badge <?= $o['is_menu'] ? 'menu' : '' ?>">
                            <?= $o['is_menu'] ? 'Menu' : 'Carte' ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $details = [];
                        if ($o['burger'])  $details[] = $o['burger']  . " (x" . $o['burger_qty'] . ")";
                        if ($o['wrap'])    $details[] = $o['wrap']    . " (x" . $o['wrap_qty'] . ")";
                        if ($o['salade'])  $details[] = $o['salade']  . " (x" . $o['salade_qty'] . ")";
                        if ($o['side'])    $details[] = $o['side']    . " (x" . $o['side_qty'] . ")";
                        if ($o['drink'])   $details[] = $o['drink']   . " (x" . $o['drink_qty'] . ")";
                        if ($o['sauce'])   $details[] = $o['sauce']   . " (x" . $o['sauce_qty'] . ")";
                        if ($o['encas'])   $details[] = $o['encas']   . " (x" . $o['encas_qty'] . ")";
                        if ($o['dessert']) $details[] = $o['dessert'] . " (x" . $o['dessert_qty'] . ")";
                        
                        echo implode(', ', $details);
                        ?>
                    </td>
                    <td>
                        <a href="orders_list.php?delete=<?= $o['id'] ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Supprimer la commande #<?= $o['id'] ?> et restituer les stocks ?');">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>