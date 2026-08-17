<?php

if(!empty($_POST)) {

$email = trim(strip_tags($_POST["email"]));
$password = trim(strip_tags($_POST["password"]));
$dsn = "mysql:host=localhost;dbname=dataTeam";
$db  = new PDO ($dsn, "root", "root");

// on récupère l'utilisateur

$query = $db->prepare("SELECT * FROM employees WHERE email LIKE :email ");
$query->bindParam(":email", $email);
$query->execute();
$result = $query->fetch();

if(!empty($result) && password_verify($password, $result["password"])) {
    // si true, on démarre la session

    session_start();

    // on met l'user dans un tableau

    $_SESSION["user"] = [
        "firstname" => $result["firstname"],
        "ip" => $_SERVER["REMOTE_ADDR"]
    ];
    header("Location:index.php");
} else {
    $message = " Impossible de vous connecter avec les informations saisies";
}
}

?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion plateforme wacdo</title>
</head>
<body>
    <h1>Connexion à la plateforme wacdo</h1>

    <?= $message ?>

    <form action="" method="post">

    <div class="form-group">

        <label for="InputEmail">Email</label>
        <input type="text" name="email" id="InputEmail">

        <label for="InputPassword">Mot de passe</label>
        <input type="text" name="password" id="InputPassword">

        <input type="submit" value="Se connecter">


</body>
</html>