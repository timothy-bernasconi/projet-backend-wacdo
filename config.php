<?php
ini_set('display_errors', '0');

try {
    $db = new PDO(
        "mysql:host=mysql-wacdo.alwaysdata.net;dbname=wacdo_bdd;charset=utf8mb4",
        "wacdo_user",
        "Triple74140",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données.");
}