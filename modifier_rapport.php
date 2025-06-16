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


    if ($role !== 'etudiant') {
        header("Location: unauthorized.php"); 
        exit();
    }
}

$message = "";
$type = "";
$rapport = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_rapport = $_POST['id'];
    $n_type = $_POST['n_type'];


    if (!empty($id_rapport)) {

        if ($_FILES['n_rapport']['size'] > 0) {
            $n_rapport = $_FILES['n_rapport'];

            // Récupérer le chemin du rapport actuel
            $sql_select_path = "SELECT rapport FROM rapport WHERE id = $id_rapport";
            $result_path = $conn->query($sql_select_path);

            if ($result_path->num_rows > 0) {
                $row_path = $result_path->fetch_assoc();
                $current_pdf_path = $row_path['rapport'];

                // Supprimer l'ancien fichier PDF
                if (file_exists($current_pdf_path)) {
                    unlink($current_pdf_path);
                }

                // Sauvegarder le nouveau fichier PDF dans le dossier "pdf"
                $pdf_name = $_FILES['n_rapport']['name'];
                $pdf_tmp = $_FILES['n_rapport']['tmp_name'];
                $pdf_path = "pdf/" . $pdf_name; // À remplacer par le chemin correct sur votre serveur
                move_uploaded_file($pdf_tmp, $pdf_path);

                // Mettre à jour le chemin du nouveau PDF dans la base de données
                $sql_update = $conn->prepare("UPDATE rapport SET type = ?, rapport = ? WHERE id = ?");
                $sql_update->bind_param("ssi", $n_type, $pdf_path, $id_rapport);
                $sql_update->execute();

                $message = "Le rapport et le PDF ont été modifiés avec succès!";
            }
        } else {

            $sql_update = $conn->prepare("UPDATE rapport SET type = ? WHERE id = ?");
            $sql_update->bind_param("si", $n_type, $id_rapport);
            $sql_update->execute();

            $message = "Le rapport a été modifié avec succès!";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_rapport = $_GET['id'];


    $sql_select = "SELECT * FROM rapport WHERE id = $id_rapport";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $type = $row['type'];
        $rapport = $row['rapport'];
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
                                window.location.href = 'rapport.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-edit" style="margin-right: 10px;"></i>Modifier ce rapport</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $id_rapport; ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <select class="form-control" name="n_type" id="type" required>
                                                    <option value="Stage d'initiation" <?php if ($type == 'Stage d\'initiation') echo 'selected'; ?>>Stage d'initiation</option>
                                                    <option value="Stage de perfectionnement" <?php if ($type == 'Stage de perfectionnement') echo 'selected'; ?>>Stage de perfectionnement</option>
                                                    <option value="Stage d'été" <?php if ($type == 'Stage d\'été') echo 'selected'; ?>>Stage d'été</option>
                                                    <option value="Stage de projet de fin d'études" <?php if ($type == 'Stage de projet de fin d\'études') echo 'selected'; ?>>Stage de projet de fin d'études</option>
                                                </select>
                                                <div class="form-group">
                                                    <label for="rapport">Rapport :</label>
                                                    <input type="file" class="form-control" name="n_rapport" id="rapport" accept=".pdf">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="rapport.php" class="btn btn-info btn-fill pull-right">Annuler</a>
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
