<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include('connexion.php');

$email = $_SESSION['email'];
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];
    $enseignant_id = $row['id']; // L'ID de l'enseignant connecté
} else {
    header("Location: login.php");
    exit();
}

if ($role !== 'enseignant') {
    header("Location: unauthorized.php");
    exit();
}

// Inscrire un étudiant au cours
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_etudiant = $_POST['id_etudiant'];
    $id_cours = $_POST['id_cours'];

    // Vérifier si l'étudiant est déjà inscrit
    $check_inscription = "SELECT * FROM inscriptions WHERE id_etudiant='$id_etudiant' AND id_cours='$id_cours'";
    $check_result = $conn->query($check_inscription);

    if ($check_result->num_rows == 0) {
        // Inscrire l'étudiant au cours
        $sql_inscription = "INSERT INTO inscriptions (id_etudiant, id_cours) VALUES ('$id_etudiant', '$id_cours')";
        if ($conn->query($sql_inscription) === TRUE) {
            header("Location: ajouter_cours.php?message=Inscription réussie!");
        } else {
            echo "Erreur : " . $conn->error;
        }
    } else {
        echo "L'étudiant est déjà inscrit à ce cours.";
    }
}

$conn->close();
?>
