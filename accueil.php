<?php
include('connexion.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
}
$email_connecte = $_SESSION['email']; 
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email_connecte'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];

    if ($role !== 'etudiant') {
        header("Location: unauthorized.php"); 
        exit();
    }
}
// Récupérer les données de l'étudiant depuis la base de données
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_etudiant = $_GET['id'];

    $sql = "SELECT * FROM accounts WHERE id = $id_etudiant";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image = $row['image'];
        $nom_modif = $row['nom'];
        $prenom_modif = $row['prenom'];
        $email_modif = $row['email'];
        $adresse = $row['adresse'];
        $classe = $row['classe'];
        $date_naissance = $row['date_naissance'];
        $cin = $row['cin'];
        $numero_telephone = $row['numero_telephone'];
    } else {
      
    }
}

// Récupérer les données des actualités depuis la base de données
$sql_actualites = "SELECT * FROM actualites";
$result_actualites = $conn->query($sql_actualites);
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

            <div class="card card-user">

                <div class="content">
                    <h4><strong>Liste d'Actualités</strong></h4>
                    <div class="row">
                        <?php
                        if ($result_actualites->num_rows > 0) {
                            while ($row_actualite = $result_actualites->fetch_assoc()) {
                                $nom_actualite = $row_actualite['nom'];
                                $description_actualite = $row_actualite['description'];
                        ?>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title" style="text-align: center;"><strong><?php echo $nom_actualite; ?></strong></h5>
                                            <p class="card-text"><?php echo $description_actualite; ?></p>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<div class='col-md-12'><p>Aucune actualité trouvée.</p></div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>

</body>
<?php include('index.js'); ?>

</html>


