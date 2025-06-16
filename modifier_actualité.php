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

$message = "";
$nom_modif = ""; 
$description = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_actualite = $_POST['id'];
    $nouveau_nom = $_POST['nouveau_nom']; 
    $nouvelle_description = $_POST['nouvelle_description'];

    $sql_update = "UPDATE actualites SET nom = '$nouveau_nom', description = '$nouvelle_description' WHERE id = $id_actualite";
    $conn->query($sql_update);

    $message = "Actualité a été modifié avec succès!";
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_actualite = $_GET['id'];


    $sql_select = "SELECT * FROM actualites WHERE id = $id_actualite";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nom_modif = $row['nom'];
        $description = $row['description'];
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
                                window.location.href = 'actualités.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-edit" style="margin-right: 10px;"></i>Modifier cette actualité</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="id" value="<?php echo $id_actualite; ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="nom">Nom :</label>
                                                    <input type="text" class="form-control" name="nouveau_nom" id="nom" value="<?php echo htmlspecialchars($nom_modif, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description :</label>
                                                    <textarea class="form-control" name="nouvelle_description" id="description" rows="5" required><?php echo htmlspecialchars($description, ENT_QUOTES); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="actualités.php" class="btn btn-info btn-fill pull-right">Annuler</a>
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
