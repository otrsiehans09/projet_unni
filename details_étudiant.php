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

            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4" style="margin-left: 33%;width: 50%;">
                            <div class="card card-user">
                                <div class="image">
                                    <img src="<?php echo $image; ?>" alt="Student Image" />
                                </div>
                                <div class="content">
                                    <div class="author">
                                        <img class="avatar border-gray" src="<?php echo $image; ?>" alt="Student Avatar" />

                                        <h4 class="title"><strong><?php echo $nom_modif; ?> <?php echo $prenom_modif; ?></strong></h4>
                                        <br />
                                        <h5 class="title"><strong>CIN:</strong> <?php echo $cin; ?></h5><br />
                                        <h5 class="title"><strong>Email:</strong> <?php echo $email_modif; ?></h5><br />
                                        <h5 class="title"><strong>Téléphone:</strong> <?php echo $numero_telephone; ?></h5><br />
                                        <h5 class="title"><strong>Adresse:</strong> <?php echo $adresse; ?></h5><br />
                                        <h5 class="title"><strong>Classe:</strong> <?php echo $classe; ?></h5><br />
                                        <h5 class="title"><strong>Date de naissance:</strong> <?php echo $date_naissance; ?></h5>
                                    </div>

                                </div>
                                <hr>
                                <div class="text-center">
                                    <button href="#" class="btn btn-simple"><i class="fa fa-facebook-square"></i></button>
                                    <button href="#" class="btn btn-simple"><i class="fa fa-twitter"></i></button>
                                    <button href="#" class="btn btn-simple"><i class="fa fa-google-plus-square"></i></button>
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
