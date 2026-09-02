<?php
session_start();

if(!isset($_SESSION["user"]) || $_SESSION["user"]["ip"] != $_SERVER["REMOTE_ADDR"]) {
    header("Location:login.php");
    exit;
}

$role = $_SESSION["user"]["position"] ?? '';




?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte employé</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Le compte de <?= $_SESSION["user"]["firstname"] ?></h1>
<p>Poste : <strong><?= htmlspecialchars($role) ?></strong></p>

<h3><a href="order.php">Passer une commande</a></h3>
<h3><a href="orders_list.php">Liste des commandes</a></h3>

<?php if (in_array($role, ['Admin', 'Préparateur'])): ?>
    <h3><a href="stock.php">Gestion du stock</a></h3>
<?php endif; ?>

<?php if (($_SESSION["user"]["position"] ?? '') === 'Admin'): ?>
        <h3> <a href="account.php">Créer un compte collaborateur</a></h3>
    <?php endif; ?>

<h3><a href="logout.php">Se déconnecter</a></h3>

</body>
</html>