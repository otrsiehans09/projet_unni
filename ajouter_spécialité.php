<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
include('connexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['nom_specialite']) && !empty($_POST['description'])) {
        $nom_specialite = $conn->real_escape_string($_POST['nom_specialite']);
        $description = $conn->real_escape_string($_POST['description']);
        $date_creation = date('Y-m-d H:i:s');

        $sql_insert = "INSERT INTO specialite (nom_specialite, description, date_creation) VALUES ('$nom_specialite', '$description', '$date_creation')";

        if ($conn->query($sql_insert) === TRUE) {
            header("Location: specialites.php"); // Rediriger après l'ajout
        } else {
            echo "Erreur : " . $conn->error;
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('index.css'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('sidenav.php'); ?>

        <div class="main-panel">
            <?php include('navtop.php'); ?>

            <div class="content">
                <div class="container-fluid">
                    <h3>Ajouter une Spécialité</h3>
                    <form method="POST" action="ajouter_specialite.php">
                        <div class="form-group">
                            <label for="nom_specialite">Nom de la spécialité</label>
                            <input type="text" class="form-control" name="nom_specialite" id="nom_specialite" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-fill">Ajouter</button>
                    </form>
                </div>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>
</body>
<?php include('index.js'); ?>

</html>
