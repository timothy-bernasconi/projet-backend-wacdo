<?php
session_start();

if(!isset($_SESSION["user"]) || $_SESSION["user"]["ip"] != $_SERVER["REMOTE_ADDR"]) {
    header("Location:login.php");
}




?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte employé</title>
</head>
<body>

<h1>Le compte de <?= $_SESSION["user"]["firstname"] ?></h1>
    
</body>
</html>