<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_matiere = $_GET['id'];


    $sql_delete = "DELETE FROM matière WHERE id = $id_matiere";
    $conn->query($sql_delete);


    header("Location: matières.php");
    exit;
}
