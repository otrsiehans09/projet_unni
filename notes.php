<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include('connexion.php');

// Récupération des informations de l'utilisateur connecté
$email = $_SESSION['email'];
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];

    if ($role !== 'etudiant') {
        header("Location: unauthorized.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

$id_etudiant = $row['id']; // ID de l'étudiant connecté

// Récupérer les notes avec les spécialités
$sql_notes = "SELECT notes.note, notes.commentaire, specialite.nom_specialite 
              FROM notes 
              JOIN specialite ON notes.id_specialite = specialite.id 
              WHERE notes.id_etudiant = '$id_etudiant'";
$result_notes = $conn->query($sql_notes);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Mes Notes</title>
    <?php include('index.css'); ?>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead th {
            background-color:rgb(47, 16, 201);
            color: white;
            padding: 12px;
        }

        .table tbody td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 25px;
            text-align: center;
            color: #333;
        }
    </style>
</head>

<body>

<div class="wrapper">
    <?php include('sidenav.php'); ?>

    <div class="main-panel">
        <?php include('navtop.php'); ?>

        <div class="container">
            <h1>Mes Notes</h1>
            <div class="content table-responsive table-full-width">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Spécialité</th>
                            <th>Note</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_notes && $result_notes->num_rows > 0) {
                            while ($row = $result_notes->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['nom_specialite']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['note']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['commentaire'] ?? '') . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Aucune note disponible</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php include('footer.php'); ?>
    </div>
</div>

<?php include('index.js'); ?>
</body>
</html>
