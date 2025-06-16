<?php
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Supprime tout chemin suspect
    $directory = 'documents/'; // Dossier où sont stockés les fichiers
    $filepath = $directory . $file;

    // Vérifie que l'extension est bien PDF
    if (pathinfo($filepath, PATHINFO_EXTENSION) !== 'pdf') {
        echo "Fichier non autorisé.";
        exit;
    }

    // Vérifie que le fichier existe
    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        echo "Le fichier demandé n'existe pas.";
    }
} else {
    echo "Paramètre de fichier manquant.";
}
?>



