<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_departement = $_GET['id'];

    // Supprimer le département de la base de données
    $sql_delete = "DELETE FROM departement WHERE id = $id_departement";
    $conn->query($sql_delete);

    // Redirection vers la page departements.php après la suppression
    header("Location: départements.php");
    exit;
}
