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

$sql_specialites = "SELECT id, nom_specialite FROM specialite";
$result_specialites = $conn->query($sql_specialites);

$message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $libelle = $_POST['libelle'];
    $coefficient = $_POST['coefficient'];
    $specialite = $_POST['specialite'];

    $sql = "INSERT INTO matière (libelle, coefficient, specialite, date_de_creation) VALUES ('$libelle', '$coefficient', '$specialite', NOW())";

    if ($conn->query($sql) === TRUE) {
        $message = "La matière a été ajouté avec succès!";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
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
                                window.location.href = 'matières.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-plus" style="margin-right: 10px;"></i>Ajouter une nouvelle matière</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="libelle">Matière :</label>
                                                    <input type="text" class="form-control" name="libelle" id="libelle" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="coefficient">Coefficient :</label>
                                                    <input type="number" class="form-control" name="coefficient" id="coefficient" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="specialite">Specialite :</label>
                                                    <select class="form-control" name="specialite" id="specialite" required>
                                                        <?php
                                                        while ($row = $result_specialites->fetch_assoc()) {
                                                            echo "<option value='" . $row['nom_specialite'] . "'>" . $row['nom_specialite'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>



                                        <div>
                                            <a href="matières.php" class="btn btn-info btn-fill pull-right">Annuler</a>
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
