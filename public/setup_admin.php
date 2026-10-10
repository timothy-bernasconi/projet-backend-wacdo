<?php
require __DIR__ . '/config.php';

$count = (int)$db->query("SELECT COUNT(*) FROM employees")->fetchColumn();
if ($count > 0) {
    die("Un compte existe déjà. Supprime ce fichier.");
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = trim(strip_tags($_POST["firstname"] ?? ''));
    $lastname  = trim(strip_tags($_POST["lastname"] ?? ''));
    $email     = trim($_POST["email"] ?? '');
    $password  = trim($_POST["password"] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Email invalide.";
    } elseif (strlen($password) < 15) {
        $msg = "Mot de passe : 15 caractères minimum.";
    } else {
        $q = $db->prepare("INSERT INTO employees(firstname, lastname, position, email, password) VALUES(:f, :l, 'Admin', :e, :p)");
        $q->execute([":f"=>$firstname, ":l"=>$lastname, ":e"=>$email, ":p"=>password_hash($password, PASSWORD_DEFAULT)]);
        die("Admin créé. Supprime maintenant setup_admin.php du serveur. <a href='login.php'>Se connecter</a>");
    }
}
?>
<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><title>Créer l'admin</title></head>
<body>
<h1>Créer le premier administrateur</h1>
<p><?= htmlspecialchars($msg) ?></p>
<form method="post">
  Prénom <input name="firstname" required><br><br>
  Nom <input name="lastname" required><br><br>
  Email <input type="email" name="email" required><br><br>
  Mot de passe (15 car. min.) <input type="password" name="password" required><br><br>
  <button type="submit">Créer</button>
</form>
</body></html>