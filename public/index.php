<?php 
session_start();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
</head>
<body>

<h1> Wacdo </h1>

<h2>Bienvenue sur votre logiciel de gestion interne</h2>

<?php 
if(isset($_SESSION["user"])) {
    ?>
    <h3> <a href="my_account.php">Mon compte</a></h3>
    <?php if (($_SESSION["user"]["position"] ?? '') === 'Admin'): ?>
        <h3> <a href="account.php">Créer un compte collaborateur</a></h3>
    <?php endif; ?>
    <h3> <a href="logout.php">Se déconnecter</a></h3>
    <?php
    } else {
        ?>
        <h3> <a href="login.php">Se connecter</a></h3>
        <?php
    }
    ?>

    
</body>
</html>