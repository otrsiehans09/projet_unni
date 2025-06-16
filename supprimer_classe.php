<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_classe = $_GET['id'];

    $sql_delete = "DELETE FROM classe WHERE id = $id_classe";
    $conn->query($sql_delete);

    header("Location: classes.php");
    exit;
}
