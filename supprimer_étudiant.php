<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_etudiant = $_GET['id'];

    // Sélectionner l'image de l'étudiant
    $sql_select_image = "SELECT image FROM accounts WHERE id = $id_etudiant";
    $result = $conn->query($sql_select_image);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_path = $row['image'];

        // Vérifier si le fichier image existe
        if (file_exists($image_path)) {
            // Supprimer le fichier image
            unlink($image_path);
        }
    }

    // Supprimer l'étudiant de la base de données
    $sql_delete = "DELETE FROM accounts WHERE id = $id_etudiant";
    $conn->query($sql_delete);

    // Redirection vers la page étudiants.php après la suppression
    header("Location: étudiants.php");
    exit;
}
