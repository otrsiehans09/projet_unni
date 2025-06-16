<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_cours = $_GET['id'];

    // Récupérer le chemin du fichier PDF à supprimer
    $sql_select = "SELECT cours FROM cours WHERE id = $id_cours";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        // Récupérer le chemin du fichier PDF
        $row = $result->fetch_assoc();
        $pdf_path = $row['cours'];

        // Supprimer le fichier PDF du dossier "pdf"
        if (file_exists($pdf_path)) {
            unlink($pdf_path);
        }


        $sql_delete = "DELETE FROM cours WHERE id = $id_cours";
        $conn->query($sql_delete);
    }


    header("Location: liste_cours.php");
    exit;
}
