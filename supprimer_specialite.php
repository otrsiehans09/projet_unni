<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_specialite = $_GET['id'];


    $sql_delete = "DELETE FROM specialite WHERE id = $id_specialite";
    $conn->query($sql_delete);


    header("Location: spécialités.php");
    exit;
}
