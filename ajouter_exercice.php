<form action="ajouter_exercice.php" method="POST" enctype="multipart/form-data">
    <input type="file" name="exercice" required>
    <input type="hidden" name="id_cours" value="<?php echo $row['id']; ?>">
    <button type="submit">Soumettre l'exercice</button>
</form>


<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include('connexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cours = $_POST['id_cours'];
    $email = $_SESSION['email'];

    // Récupérer l'ID de l'étudiant
    $sql_fetch_user = "SELECT id FROM accounts WHERE email='$email'";
    $result_user = $conn->query($sql_fetch_user);
    $row = $result_user->fetch_assoc();
    $id_etudiant = $row['id'];

    // Téléchargement du fichier
    $target_dir = "uploads/exercices/";
    $target_file = $target_dir . basename($_FILES["exercice"]["name"]);
    if (move_uploaded_file($_FILES["exercice"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO exercices (id_etudiant, id_cours, fichier_exercice) VALUES ('$id_etudiant', '$id_cours', '$target_file')";
        if ($conn->query($sql) === TRUE) {
            echo "Exercice soumis avec succès!";
        } else {
            echo "Erreur : " . $conn->error;
        }
    } else {
        echo "Erreur de téléchargement du fichier.";
    }
}
?>
