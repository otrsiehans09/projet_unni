<?php
session_start();
include('connexion.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$id_question = intval($_GET['id_question']);

// Récupération de l'ID de l'utilisateur connecté
$stmt = $conn->prepare("SELECT id FROM accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$id_etudiant = $user['id'];

// Vérification que la question appartient à l'utilisateur
$verif_stmt = $conn->prepare("SELECT * FROM questions WHERE id = ? AND id_etudiant = ?");
$verif_stmt->bind_param("ii", $id_question, $id_etudiant);
$verif_stmt->execute();
$verif_result = $verif_stmt->get_result();

if ($verif_result->num_rows > 0) {
    $delete_stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
    $delete_stmt->bind_param("i", $id_question);
    $delete_stmt->execute();
    header("Location: questions.php?success=1");
} else {
    echo "Vous ne pouvez supprimer que vos propres questions.";
}
?>
