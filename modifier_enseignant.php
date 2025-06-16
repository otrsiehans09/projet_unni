<?php
include('connexion.php');
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
}

$email = $_SESSION['email'];
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];


    if ($role !== 'admin') {
        header("Location: unauthorized.php"); 
        exit();
    }
}

// Sélection des départements depuis la base de données
$sql_departements = "SELECT id, nom_departement FROM departement";
$result_departements = $conn->query($sql_departements);


$message = "";
$nom_modif = ""; 
$prenom_modif = "";
$cin = "";
$image = "";
$date_de_naissance = "";
$email_modif = "";
$telephone = "";
$adresse = "";
$departement = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_enseignant = $_POST['id'];
    $nouveau_nom = $_POST['nouveau_nom'];
    $nouveau_prenom = $_POST['nouveau_prenom'];
    $n_cin = $_POST['n_cin'];
    $n_date_naissance = $_POST['n_date_naissance'];
    $n_email = $_POST['n_email'];
    $n_telephone = $_POST['n_telephone'];
    $n_adresse = $_POST['n_adresse'];
    $nouveau_departement = $_POST['nouveau_departement'];



    $sql_select_image = "SELECT image FROM enseignant WHERE id = $id_enseignant";
    $resultat_de_la_requete = $conn->query($sql_select_image);
    $row = $resultat_de_la_requete->fetch_assoc();
    $ancienne_image = $row['image'];

    // Vérifier si un nouveau fichier image a été téléchargé
    if (isset($_FILES['nouvelle_image']) && $_FILES['nouvelle_image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp = $_FILES['nouvelle_image']['tmp_name'];
        $image_nom = $_FILES['nouvelle_image']['name'];
        $destination = "uploads/" . $image_nom;

        // Si un nouveau fichier est téléchargé, le déplacer et mettre à jour l'image
        if (move_uploaded_file($image_tmp, $destination)) {
            // Supprimer l'ancienne image si elle existe
            if (!empty($ancienne_image) && file_exists($ancienne_image)) {
                unlink($ancienne_image);
            }

            $sql_update_image = "UPDATE enseignant SET image = '$destination' WHERE id = $id_enseignant";
            $conn->query($sql_update_image);
        } else {

        }
    }

    $sql_update = "UPDATE enseignant SET nom = '$nouveau_nom', prenom = '$nouveau_prenom', cin = '$n_cin', date_de_naissance = '$n_date_naissance', email = '$n_email', numero_telephone = '$n_telephone', adresse = '$n_adresse', departement = '$nouveau_departement' WHERE id = $id_enseignant";
    $conn->query($sql_update);

    $message = "Enseignant a été modifié avec succès!";
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_enseignant = $_GET['id'];


    $sql_select = "SELECT * FROM enseignant WHERE id = $id_enseignant";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nom_modif = $row['nom'];
        $prenom_modif = $row['prenom'];
        $cin = $row['cin'];
        $image = $row['image'];
        $date_de_naissance = $row['date_de_naissance'];
        $numero_telephone = $row['numero_telephone'];
        $email_modif = $row['email'];
        $adresse = $row['adresse'];
        $departement = $row['departement'];
    } else {
      

    }
} else {

}
?>
<!doctype html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('index.css'); ?>
    <style>
        .swal2-popup {
            font-size: 14px !important;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include('sidenav.php'); ?>

        <div class="main-panel">
            <?php include('navtop.php'); ?>

            <div class="content">
                <div class="container-fluid">
                    <?php if (!empty($message)) { ?>
                        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
                        <script>
                            Swal.fire({
                                title: 'Succès!',
                                text: '<?php echo $message; ?>',
                                icon: 'success',
                                showConfirmButton: true,
                                showCancelButton: false,
                                confirmButtonText: 'OK'
                            }).then(function() {
                                window.location.href = 'enseignants.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-edit" style="margin-right: 10px;"></i>Modifier cet enseignant</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $id_enseignant; ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="nom">Nom :</label>
                                                    <input type="text" class="form-control" name="nouveau_nom" id="nom" value="<?php echo htmlspecialchars($nom_modif, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="prenom">Prénom :</label>
                                                    <input type="text" class="form-control" name="nouveau_prenom" id="prenom" value="<?php echo htmlspecialchars($prenom_modif, ENT_QUOTES); ?>" required>
                                                </div>




                                                <div class="form-group">
                                                    <label for="cin">CIN :</label>
                                                    <input type="number" class="form-control" name="n_cin" id="cin" value="<?php echo htmlspecialchars($cin, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="image">Image :</label>
                                                    <input type="file" class="form-control" name="nouvelle_image" id="image">
                                                </div>
                                                <div class="form-group">
                                                    <label for="date_de_naissance">Date de naissance :</label>
                                                    <input type="date" class="form-control" name="n_date_naissance" id="date_de_naissance" value="<?php echo htmlspecialchars($date_de_naissance, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email :</label>
                                                    <input type="email" class="form-control" name="n_email" id="email" value="<?php echo htmlspecialchars($email_modif, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="numero_telephone">Téléphone :</label>
                                                    <input type="number" class="form-control" name="n_telephone" id="numero_telephone" value="<?php echo htmlspecialchars($numero_telephone, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="adresse">Adresse :</label>
                                                    <input type="text" class="form-control" name="n_adresse" id="adresse" value="<?php echo htmlspecialchars($adresse, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="departement">Département :</label>
                                                    <select class="form-control" name="nouveau_departement" id="departement" required>
                                                        <?php

                                                        while ($row = $result_departements->fetch_assoc()) {
                                                            $selected = ($departement == $row['nom_departement']) ? "selected" : "";
                                                            echo "<option value='" . $row['nom_departement'] . "' $selected>" . $row['nom_departement'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>


                                            </div>
                                        </div>
                                        <div>
                                            <a href="enseignants.php" class="btn btn-info btn-fill pull-right">Annuler</a>
                                            <button type="submit" class="btn btn-success btn-fill" style="margin-left: 82%;">Modifier</button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>

</body>
<?php include('index.js'); ?>

</html>
