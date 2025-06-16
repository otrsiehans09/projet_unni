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
$date_debut = ""; 
$date_fin = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_parametres = $_POST['id'];
    $nouveau_date_debut = $_POST['nouveau_date_debut']; 

    $nouveau_date_fin = $_POST['nouveau_date_fin'];

    $sql_update = "UPDATE parametres SET date_debut = '$nouveau_date_debut', date_fin = '$nouveau_date_fin' WHERE id = $id_parametres";
    $conn->query($sql_update);

    $message = "La date a été modifié avec succès!";
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_parametres = $_GET['id'];


    $sql_select = "SELECT * FROM parametres WHERE id = $id_parametres";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $date_debut = $row['date_debut'];
        $date_fin = $row['date_fin'];
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

        @media (max-width: 768px) {
    .profile-image {
        width: 100px;
        height: 100px;
    }
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
                                window.location.href = 'paramètres.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-edit" style="margin-right: 10px;"></i>Modifier la date de dépôt de rapport</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="id" value="<?php echo $id_parametres; ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="date_debut">Date début : :</label>
                                                    <input type="date" class="form-control" name="nouveau_date_debut" id="date_debut" value="<?php echo htmlspecialchars($date_debut, ENT_QUOTES); ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="date_fin">Date fin : :</label>
                                                    <input type="date" class="form-control" name="nouveau_date_fin" id="date_fin" value="<?php echo htmlspecialchars($date_fin, ENT_QUOTES); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="paramètres.php" class="btn btn-info btn-fill pull-right">Annuler</a>
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
