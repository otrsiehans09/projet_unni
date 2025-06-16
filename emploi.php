<?php
include('connexion.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// On récupère la classe de l'étudiant
$sql = "SELECT classe FROM accounts WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $classe = $result->fetch_assoc()['classe'];

    // Récupère les cours liés à sa classe
    $sql_emploi = "SELECT * FROM emplois_du_temps WHERE id_classe = $classe ORDER BY FIELD(jour, 'Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'), heure_debut";
    $emploi_result = $conn->query($sql_emploi);
} else {
    echo "Erreur de récupération de la classe.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du Temps</title>
    <?php include('index.css'); ?>
</head>
<body>
    <div class="wrapper">
        <?php include('sidenav.php'); ?>
        <div class="main-panel">
            <?php include('navtop.php'); ?>

            <div class="content">
                <div class="container-fluid">
                    <h3>Mon Emploi du Temps</h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Jour</th>
                                <th>Heure Début</th>
                                <th>Heure Fin</th>
                                <th>Matière</th>
                                <th>Salle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($emploi_result->num_rows > 0) {
                                while ($row = $emploi_result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['jour']}</td>
                                        <td>{$row['heure_debut']}</td>
                                        <td>{$row['heure_fin']}</td>
                                        <td>{$row['matiere']}</td>
                                        <td>{$row['salle']}</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Aucun cours prévu.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>
</body>
<?php include('index.js'); ?>
</html>
