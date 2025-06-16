<?php
$servername = "localhost";
$username = "root";
$password = ""; // vide si tu n’as pas mis de mot de passe
$dbname = "inscription_universitaire";
$port = 3307; // très important si ton MySQL est sur 3307 !

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
