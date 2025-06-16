<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_rapport = $_GET['id'];

    // Récupérer le chemin du fichier PDF à supprimer
    $sql_select = "SELECT rapport FROM rapport WHERE id = $id_rapport";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        // Récupérer le chemin du fichier PDF
        $row = $result->fetch_assoc();
        $pdf_path = $row['rapport'];

        // Supprimer le fichier PDF du dossier "pdf"
        if (file_exists($pdf_path)) {
            unlink($pdf_path);
        }

        // Supprimer le rapport de la base de données
        $sql_delete = "DELETE FROM rapport WHERE id = $id_rapport";
        $conn->query($sql_delete);
    }

    // Redirection vers la page rapport.php après la suppression
    header("Location: rapport.php");
    exit;
}
