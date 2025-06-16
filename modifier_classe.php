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
// Sélection des spécialités depuis la base de données
$sql_specialites = "SELECT id, nom_specialite FROM specialite";
$result_specialites = $conn->query($sql_specialites);


$message = "";
$nom_modif = "";
$niveau = "";
$departement = "";
$specialite = ""; 
$nombre_etudiants = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_classe = $_POST['id'];
    $nouveau_nom = $_POST['nouveau_nom']; 
    $nouveau_niveau = $_POST['nouveau_niveau'];
    $nouveau_departement = $_POST['nouveau_departement'];
    $nouveau_specialite = $_POST['nouveau_specialite'];
    $n_nombre_etudiants = $_POST['n_nombre_etudiants'];

    $sql_update = "UPDATE classe SET nom = '$nouveau_nom', niveau = '$nouveau_niveau', departement = '$nouveau_departement', specialite = '$nouveau_specialite', nombre_etudiants = '$n_nombre_etudiants' WHERE id = $id_classe";
    $conn->query($sql_update);

    $message = "La classe a été modifié avec succès!";
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_classe = $_GET['id'];


    $sql_select = "SELECT * FROM classe WHERE id = $id_classe";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nom_modif = $row['nom'];
        $niveau = $row['niveau'];
        $departement = $row['departement'];
        $specialite = $row['specialite'];
        $nombre_etudiants = $row['nombre_etudiants'];
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
                                window.location.href = 'classes.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-edit" style="margin-right: 10px;"></i>Modifier cette classe</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="id" value="<?php echo $id_classe; ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="nom_classe">Nom :</label>
                                                    <input type="text" class="form-control" name="nouveau_nom" id="nom_classe" value="<?php echo htmlspecialchars($nom_modif, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="niveau">Niveau :</label>
                                                    <input type="text" class="form-control" name="nouveau_niveau" id="niveau" value="<?php echo htmlspecialchars($niveau, ENT_QUOTES); ?>" required>
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
                                                <div class="form-group">
                                                    <label for="specialite">Specialite :</label>
                                                    <select class="form-control" name="nouveau_specialite" id="specialite" required>
                                                        <?php

                                                        while ($row = $result_specialites->fetch_assoc()) {
                                                            $selected = ($specialite == $row['nom_specialite']) ? "selected" : "";
                                                            echo "<option value='" . $row['nom_specialite'] . "' $selected>" . $row['nom_specialite'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>


                                                <div class="form-group">
                                                    <label for="nombre_etudiants">Nombre d'étudiants :</label>
                                                    <input type="number" class="form-control" name="n_nombre_etudiants" id="nombre_etudiants" value="<?php echo htmlspecialchars($nombre_etudiants, ENT_QUOTES); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="classes.php" class="btn btn-info btn-fill pull-right">Annuler</a>
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
