<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
include('connexion.php');

// Vérification du rôle (enseignant)
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);
$row = $result_user->fetch_assoc();
$role = $row['role'];

if ($role !== 'enseignant') {
    header("Location: unauthorized.php");
    exit();
}

// Récupérer les questions
$sql_questions = "SELECT * FROM questions WHERE statut='en attente' ORDER BY date_creation DESC";
$res_questions = $conn->query($sql_questions);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_question = $_POST['id_question'];
    $reponse = $_POST['reponse'];

    $sql_update = "UPDATE questions SET statut='répondu', date_respondue=NOW(), reponse='$reponse' WHERE id=$id_question";
    if ($conn->query($sql_update) === TRUE) {
        header("Location: repondre_questions.php");  // Rediriger vers la page de réponse
    } else {
        echo "Erreur : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Répondre aux questions</title>
</head>
<body>
    <h2>Questions en attente de réponse</h2>

    <?php while ($question = $res_questions->fetch_assoc()) { ?>
        <div>
            <p><strong>Question :</strong> <?php echo $question['question']; ?></p>
            <form action="repondre_questions.php" method="post">
                <input type="hidden" name="id_question" value="<?php echo $question['id']; ?>">
                <textarea name="reponse" required></textarea>
                <br>
                <button type="submit">Répondre</button>
            </form>
        </div>
        <hr>
    <?php } ?>
</body>
</html>
