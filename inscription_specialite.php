<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

include('connexion.php');

// Récupérer l'ID de l'étudiant connecté
$sql_fetch_user = "SELECT * FROM accounts WHERE email = ?";
$stmt = $conn->prepare($sql_fetch_user);
$stmt->bind_param("s", $email);
$stmt->execute();
$result_user = $stmt->get_result();

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $id_etudiant = $row['id']; // L'ID de l'étudiant connecté
} else {
    header("Location: login.php");
    exit();
}

// Vérification de l'ID de la spécialité passée en paramètre
if (isset($_GET['id_specialite'])) {
    $id_specialite = $_GET['id_specialite'];

    // Vérifier si la spécialité existe dans la table 'specialites'
    $check_specialite = "SELECT id FROM specialite WHERE id = ?";
    $stmt_check_specialite = $conn->prepare($check_specialite);
    $stmt_check_specialite->bind_param("i", $id_specialite);
    $stmt_check_specialite->execute();
    $result_specialite = $stmt_check_specialite->get_result();

    // Si la spécialité n'existe pas
    if ($result_specialite->num_rows == 0) {
        header("Location: inscription_specialite.php?msg=invalid_specialite");
        exit();
    }

    // Vérifier si l'étudiant est déjà inscrit à cette spécialité
    $check_inscription = "SELECT * FROM inscriptions WHERE id_etudiant = ? AND id_specialite = ?";
    $stmt_check = $conn->prepare($check_inscription);
    $stmt_check->bind_param("ii", $id_etudiant, $id_specialite);
    $stmt_check->execute();
    $inscription_res = $stmt_check->get_result();

    if ($inscription_res->num_rows > 0) {
        // L'étudiant est déjà inscrit à cette spécialité
        header("Location: inscription_specialite.php?msg=already_registered");
        exit();
    } else {
        // Inscrire l'étudiant à cette spécialité
        $sql_insert = "INSERT INTO inscriptions (id_etudiant, id_specialite, date_inscription) VALUES (?, ?, NOW())";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $id_etudiant, $id_specialite);

        if ($stmt_insert->execute()) {
            header("Location: inscription_specialite.php?msg=success");
            exit();
        } else {
            header("Location: inscription_specialite.php?msg=error");
            exit();
        }
    }
} else {
    header("Location: cours.php");
    exit();
}
$conn->close();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription à une spécialité</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .alert-success {
            background-color: #28a745;
            color: white;
        }

        .alert-warning {
            background-color: #ffc107;
            color: black;
        }

        .alert-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        if (isset($_GET['msg'])) {
            $msg = $_GET['msg'];
            if ($msg == 'success') {
                echo "<div class='alert alert-success'>Vous êtes inscrit avec succès à la spécialité.</div>";
            } elseif ($msg == 'already_registered') {
                echo "<div class='alert alert-warning'>Vous êtes déjà inscrit à cette spécialité.</div>";
            } elseif ($msg == 'invalid_specialite') {
                echo "<div class='alert alert-danger'>La spécialité demandée n'existe pas.</div>";
            } else {
                echo "<div class='alert alert-danger'>Une erreur est survenue. Veuillez réessayer.</div>";
            }
        }
        ?>
    </div>
</body>
</html>
