<?php
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_enseignant = $_GET['id'];
    // Sélectionner l'image de l'étudiant
    $sql_select_image = "SELECT image FROM enseignant WHERE id = $id_enseignant";
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


    $sql_delete = "DELETE FROM enseignant WHERE id = $id_enseignant";
    $conn->query($sql_delete);


    header("Location: enseignants.php");
    exit;
}
