<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_actualite = $_GET['id'];

    $sql_delete = "DELETE FROM actualites WHERE id = $id_actualite";
    $conn->query($sql_delete);

    header("Location: actualités.php");
    exit;
}
