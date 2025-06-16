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


    if ($role !== 'admin') {
        header("Location: unauthorized.php"); 
        exit();
    }
}


$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_admin = $_POST['id'];
    $nouveau_role = $_POST['role']; 

    $sql_update = "UPDATE accounts SET role = '$nouveau_role' WHERE id = $id_admin";
    $conn->query($sql_update);

    $message = "Le rôle a été modifié avec succès!";
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_admin = $_GET['id'];

    // Récupérer les informations du département depuis la base de données
    $sql_select = "SELECT * FROM accounts WHERE id = $id_admin";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $role = $row['role']; 
        $nom_modif = $row['nom'];
        $prenom_modif = $row['prenom'];
        $cin = $row['cin'];
        $email_modif = $row['email']; 
        $date_naissance = $row['date_naissance'];
        $telephone = $row['numero_telephone'];
        $adresse = $row['adresse'];
    } else {
        
    }
} else {

}

$role_etudiant_checked = ($role === 'etudiant') ? 'checked' : '';
$role_enseignant_checked = ($role === 'enseignant') ? 'checked' : '';

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
                                window.location.href = 'roles.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-edit" style="margin-right: 10px;"></i>Modifier le rôle de l'utilisateur</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="id" value="<?php echo $id_admin; ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="role">Rôle :</label><br>
                                                    <input type="radio" name="role" value="etudiant" <?php echo $role_etudiant_checked; ?>> Étudiant
<input type="radio" name="role" value="enseignant" <?php echo $role_enseignant_checked; ?>> Enseignant
                                             </div>

                                                <div class="form-group">
                                                    <label for="nom">Nom :</label>
                                                    <input type="text" class="form-control" name="nouveau_nom" id="nom" value="<?php echo htmlspecialchars($nom_modif, ENT_QUOTES); ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="prenom">Prénom :</label>
                                                    <input type="text" class="form-control" name="nouveau_prenom" id="prenom" value="<?php echo htmlspecialchars($prenom_modif, ENT_QUOTES); ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="email">Email :</label>
                                                    <input type="text" class="form-control" name="n_email" id="email" value="<?php echo htmlspecialchars($email_modif, ENT_QUOTES); ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="cin">CIN :</label>
                                                    <input type="text" class="form-control" name="n_cin" id="cin" value="<?php echo htmlspecialchars($cin, ENT_QUOTES); ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="date_naissance">Date de naissance :</label>
                                                    <input type="date" class="form-control" name="n_date_naissance" id="date_naissance" value="<?php echo htmlspecialchars($date_naissance, ENT_QUOTES); ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="numero_telephone">Téléphone :</label>
                                                    <input type="number" class="form-control" name="n_telephone" id="numero_telephone" value="<?php echo htmlspecialchars($telephone, ENT_QUOTES); ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="adresse">Adresse :</label>
                                                    <input type="text" class="form-control" name="n_adresse" id="adresse" value="<?php echo htmlspecialchars($adresse, ENT_QUOTES); ?>" readonly>
                                                </div>



                                            </div>
                                        </div>
                                        <div>
                                            <a href="roles.php" class="btn btn-info btn-fill pull-right">Annuler</a>
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
