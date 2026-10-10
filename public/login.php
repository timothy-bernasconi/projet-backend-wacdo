<?php

$message = "";

if(!empty($_POST)) {

    $email = trim(strip_tags($_POST["email"]));
    $password = trim(strip_tags($_POST["password"]));
    require __DIR__ . '/config.php';

    // on récupère l'utilisateur
    $query = $db->prepare("SELECT * FROM employees WHERE email LIKE :email ");
    $query->bindParam(":email", $email);
    $query->execute();
    $result = $query->fetch();

    if(!empty($result) && password_verify($password, $result["password"])) {
        session_start();

        $_SESSION["user"] = [
            "firstname" => $result["firstname"],
            "position" => $result["position"],
            "ip" => $_SERVER["REMOTE_ADDR"]
        ];
        header("Location:index.php");
        exit;
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Connexion à la plateforme wacdo</h1>

    <?php if ($message): ?>
        <p class="errors"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <div class="form-group">
            <label for="InputEmail">Email</label>
            <input type="text" name="email" id="InputEmail">

            <label for="InputPassword">Mot de passe</label>
            <input type="password" name="password" id="InputPassword">

            <input type="submit" value="Se connecter">
        </div>
    </form>
</body>
</html>