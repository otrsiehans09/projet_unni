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
$nom_specialite = ""; 
$description = "";
$departement = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id_specialite = $_POST['id'];
    
    // Assurez-vous que $id_specialite est défini et non vide
    if (!empty($id_specialite)) {
        $nouveau_nom = $conn->real_escape_string($_POST['nouveau_nom']);
        $nouvelle_description = $conn->real_escape_string($_POST['nouvelle_description']);
        $nouveau_departement = $conn->real_escape_string($_POST['nouveau_departement']);
        $nouveau_type_seance = $conn->real_escape_string($_POST['type_seance']);  // Récupérer le type de séance

        $sql_update = "UPDATE specialite SET nom_specialite = '$nouveau_nom', description = '$nouvelle_description', departement = '$nouveau_departement', type_seance = '$nouveau_type_seance' WHERE id = $id_specialite";

        if ($conn->query($sql_update) === TRUE) {
            $message = "La spécialité a été modifiée avec succès!";
        } else {
            echo "Erreur lors de la mise à jour : " . $conn->error;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id_specialite = $_GET['id'];

    // Récupérer les informations du département depuis la base de données
    $sql_select = "SELECT * FROM specialite WHERE id = $id_specialite";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nom_specialite = $row['nom_specialite'];
        $description = $row['description'];
        $departement = $row['departement'];
    } 
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
                                window.location.href = 'spécialités.php';
                            });
                        </script>
                    <?php } ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title"><i class="fas fa-edit" style="margin-right: 10px;"></i>Modifier cette spécialité</h4>
                                </div>
                                <div class="content">
                                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <input type="hidden" name="id" value="<?php echo $id_specialite; ?>">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="nom_specialite">Nom du spécialité :</label>
                                                    <input type="text" class="form-control" name="nouveau_nom" id="nom_specialite" value="<?php echo htmlspecialchars($nom_specialite, ENT_QUOTES); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="type_seance">Type de séance :</label>
                                                    <select class="form-control" name="type_seance" id="type_seance" required>
                                                        <option value="CM" <?php echo ($row['type_seance'] == 'CM') ? 'selected' : ''; ?>>CM</option>
                                                        <option value="TP" <?php echo ($row['type_seance'] == 'TP') ? 'selected' : ''; ?>>TP</option>
                                                        <option value="TD" <?php echo ($row['type_seance'] == 'TD') ? 'selected' : ''; ?>>TD</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description du spécialité :</label>
                                                    <textarea class="form-control" name="nouvelle_description" id="description" rows="5" required><?php echo htmlspecialchars($description, ENT_QUOTES); ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="departement">Département :</label>
                                                    <select class="form-control" name="nouveau_departement" id="departement" required>
                                                        <?php
                                                        while ($row_departement = $result_departements->fetch_assoc()) {
                                                            $selected = ($departement == $row_departement['nom_departement']) ? "selected" : "";
                                                            echo "<option value='" . $row_departement['nom_departement'] . "' $selected>" . $row_departement['nom_departement'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="spécialités.php" class="btn btn-info btn-fill pull-right">Annuler</a>
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
