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
// Sélection des spécialités depuis la base de données
$sql_specialites = "SELECT id, nom_specialite FROM specialite";
$result_specialites = $conn->query($sql_specialites);

$message = "";
$libelle = ""; 
$coefficient = "";
$specialite = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_matiere = $_POST['id'];
    $nouveau_libelle = $_POST['nouveau_libelle'];
    $nouveau_coefficient = $_POST['nouveau_coefficient'];
    $nouvelle_specialite = $_POST['nouvelle_specialite'];

    $sql_update = "UPDATE matière SET libelle = '$nouveau_libelle', coefficient = '$nouveau_coefficient', specialite = '$nouvelle_specialite' WHERE id = $id_matiere";
    $conn->query($sql_update);

    $message = "La matière a été modifié avec succès!";
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_matiere = $_GET['id'];


    $sql_select = "SELECT * FROM matière WHERE id = $id_matiere";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $libelle = $row['libelle'];
        $coefficient = $row['coefficient'];
        $specialite = $row['specialite'];
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
                                window.location.href = 'matières.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-edit" style="margin-right: 10px;"></i>Modifier cette matière</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="id" value="<?php echo $id_matiere; ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="libelle">Matière :</label>
                                                    <input type="text" class="form-control" name="nouveau_libelle" id="libelle" value="<?php echo htmlspecialchars($libelle, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="coefficient">Coefficient :</label>
                                                    <input type="text" class="form-control" name="nouveau_coefficient" id="coefficient" value="<?php echo htmlspecialchars($coefficient, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="specialite">Specialite :</label>
                                                    <select class="form-control" name="nouvelle_specialite" id="specialite" required>
                                                        <?php
                                                        while ($row = $result_specialites->fetch_assoc()) {
                                                            $selected = ($specialite == $row['nom_specialite']) ? "selected" : "";
                                                            echo "<option value='" . $row['nom_specialite'] . "' $selected>" . $row['nom_specialite'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="matières.php" class="btn btn-info btn-fill pull-right">Annuler</a>
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
