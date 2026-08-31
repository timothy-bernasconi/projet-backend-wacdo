<?php
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["user"]["ip"] !== $_SERVER["REMOTE_ADDR"]) {
    header("Location: login.php");
    exit;
}

if (($_SESSION["user"]["position"] ?? '') !== 'Admin') {
    header("Location: my_account.php");
    exit;
}

$errors = array(); // Initialiser le tableau pour éviter toute erreur dans le HTML au premier chargement

if(!empty($_POST)) { // Si pas vide, le form est soumis

    // Nettoyage XSS + sécurité si une clé est absente
    $firstname = trim(strip_tags($_POST["firstname"] ?? ''));
    $lastname  = trim(strip_tags($_POST["lastname"] ?? ''));
    $position  = trim(strip_tags($_POST["position"] ?? ''));
    $email     = trim(strip_tags($_POST["email"] ?? ''));
    $password  = trim(strip_tags($_POST["password"] ?? ''));

    // Validation email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "L'email saisi n'est pas valide";
    }

    // Validation mdp
    $uppercase = preg_match("/[A-Z]/", $password);
    $lowercase = preg_match("/[a-z]/", $password);
    $number    = preg_match("/[0-9]/", $password);

    if (!$uppercase || !$lowercase || !$number || strlen($password) < 15) {
        $errors["password"] = "Le mdp doit contenir une majuscule, une minuscule, un chiffre et minimum 15 caractères";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $dsn = "mysql:host=localhost;dbname=dataTeam";
        $db  = new PDO ($dsn, "root", "root");
        
        $query = $db->prepare("INSERT INTO employees(firstname, lastname, position, email, password) VALUES(:firstname, :lastname, :position, :email, :password)");
        $query->bindParam(":firstname", $firstname);
        $query->bindParam(":lastname", $lastname);
        $query->bindParam(":position", $position);
        $query->bindParam(":email", $email);
        $query->bindParam(":password", $hash);
        
        if ($query->execute()) {
            header("Location: my_account.php");
            exit; 
        } else {
            $errors["execute"] = "Il y a un problème, veuillez réessayer";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
</head>
<body>

<h1>Ajouter un collaborateur</h1>

<form action="" method="post">

    <div class="form-group">

        <label for="InputFirstName">Prénom</label>
        <input type="text" name="firstname" id="InputFirstName" value ="<?= $firstname ?? "" ?>">
        <?php if (isset($errors["firstname"])): ?>
            <p class="errors"><?= $errors["firstname"] ?></p>
        <?php endif; ?>

        <br>

        <label for="InputLastName">Nom</label>
        <input type="text" name="lastname" id="InputLastName" value ="<?= $lastname ?? "" ?>">>
        <?php if (isset($errors["lastname"])): ?>
            <p class="errors"><?= $errors["lastname"] ?></p>
        <?php endif; ?>

        <br>

        <label for="InputPosition">Métier</label>
        <input type="text" name="position" id="InputPosition" value ="<?= $position ?? "" ?>">>
        <?php if (isset($errors["position"])): ?>
            <p class="errors"><?= $errors["position"] ?></p>
        <?php endif; ?>

        <br>

        <label for="InputEmail">E-mail</label>
        <input type="email" name="email" id="InputEmail">
        <?php if (isset($errors["email"])): ?>
            <p class="errors"><?= $errors["email"] ?></p>
        <?php endif; ?>

        <br>

        <label for="InputPassword">Mot de Passe</label>
        <input type="password" name="password" id="InputPassword">
        <?php if (isset($errors["password"])): ?>
            <p class="errors"><?= $errors["password"] ?></p>
        <?php endif; ?>

        <br><br>
        <button type="submit">Enregistrer</button>

    </div>

</form>
    
</body>
</html>