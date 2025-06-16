<?php
include('connexion.php');
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $nom = $row['nom'];
    $prenom = $row['prenom'];
    $numero_telephone = $row['numero_telephone'];
    $adresse = $row['adresse'];
    $cin = $row['cin'];
    $classe = $row['classe'];
    $date_naissance = $row['date_naissance'];

    $role = $row['role'];


    if ($role !== 'etudiant') {
        header("Location: unauthorized.php"); 
        exit();
    }
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $type = $_POST['type'];


    if (isset($_FILES['rapport']) && $_FILES['rapport']['error'] === UPLOAD_ERR_OK) {
        $rapport = $_FILES['rapport'];


        $file_extension = pathinfo($rapport['name'], PATHINFO_EXTENSION);
        if ($file_extension != 'pdf') {
            $message = "Veuillez télécharger un fichier PDF.";
        } else {
            $destination = 'pdf/' . $rapport['name'];


            if (move_uploaded_file($rapport['tmp_name'], $destination)) {

                $sql = "INSERT INTO rapport (type, rapport, date_creation, nom, prenom, email, numero_telephone, adresse, cin, classe, date_naissance) VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)";


                $stmt = $conn->prepare($sql);

                $stmt->bind_param("ssssssssss", $type, $destination, $nom, $prenom, $email, $numero_telephone, $adresse, $cin, $classe, $date_naissance);

                if ($stmt->execute()) {
                    $message = "Le rapport a été ajouté avec succès!";
                } else {
                    echo "Erreur : " . $sql . "<br>" . $conn->error;
                }

                $stmt->close();
            } else {
                $message = "Erreur lors du téléchargement du fichier.";
            }
        }
    } else {
        $message = "Veuillez sélectionner un fichier PDF.";
    }
}

$conn->close();
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
                                    <h4 class="title"><i class="fas fa-plus" style="margin-right: 10px;"></i>Ajouter un nouveau rapport de stage</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="type">Type :</label>
                                                    <select class="form-control" name="type" id="type" required>
                                                        <option value="Stage de projet de fin d'études">Devoir</option>
                                                        <option value="Stage de projet de fin d'études">Execice</option>
                                                        <option value="Stage d'initiation">Stage d'initiation</option>
                                                        <option value="Stage de perfectionnement">Stage de perfectionnement</option>
                                                        <option value="Stage d'été">Stage d'été</option>
                                                        <option value="Stage de projet de fin d'études">Stage de projet de fin d'études</option>

                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="rapport">Rapport (PDF) :</label>
                                                    <input type="file" class="form-control" name="rapport" id="rapport" accept=".pdf" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <a href="rapport.php" class="btn btn-info btn-fill pull-right">Annuler</a>
                                            <button type="submit" class="btn btn-success btn-fill" style="margin-left: 82%;">Ajouter</button>
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
