<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_parametre = $_GET['id'];

    $sql_delete = "DELETE FROM parametres WHERE id = $id_parametre";
    $conn->query($sql_delete);

    header("Location: paramètres.php");
    exit;
}
