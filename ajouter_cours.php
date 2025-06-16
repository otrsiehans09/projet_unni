<?php
session_start();
include('connexion.php');  // Connexion à la base de données

// Vérifier si l'utilisateur est connecté et s'il s'agit bien d'un enseignant
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];
    $enseignant_id = $row['id'];  // L'ID de l'enseignant connecté

    // Si l'utilisateur n'est pas un enseignant, rediriger vers une page d'erreur
    if ($role !== 'enseignant') {
        header("Location: unauthorized.php");
        exit();
    }
} else {
    echo "Utilisateur non trouvé!";
    exit();
}

// Traitement du formulaire d'ajout de cours
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre_cours = $_POST['titre_cours'];
    $description_cours = $_POST['description_cours'];

    // Requête pour insérer le cours dans la base de données
    $sql_cours = "INSERT INTO cours (titre, description, enseignant_id) 
                  VALUES ('$titre_cours', '$description_cours', '$enseignant_id')";
    
    if ($conn->query($sql_cours) === TRUE) {
        $message = "Cours ajouté avec succès!";
    } else {
        $message = "Erreur lors de l'ajout du cours : " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Ajouter un Cours</title>
    <?php include('index.css'); ?>
</head>
<body>
    <div class="content">
        <div class="container-fluid" style="width: 80%; margin: 0 auto;">
            <?php if (isset($message)) { ?>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
                <script>
                    Swal.fire({
                        title: 'Résultat',
                        text: '<?php echo $message; ?>',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href = 'dashboard_enseignant.php'; // Rediriger vers le tableau de bord
                    });
                </script>
            <?php } ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="width: 70%; margin: 0 auto;">
                        <div class="header">
                            <h3 class="title" style="font-weight: bold; text-align: center;">Ajouter un Cours</h3>
                        </div>
                        <div class="content">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="titre_cours">Titre du Cours :</label>
                                            <input type="text" class="form-control" name="titre_cours" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description_cours">Description :</label>
                                            <textarea class="form-control" name="description_cours" rows="5" required></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-success btn-fill" style="margin-left: 42%;">Ajouter le Cours</button>
                                    <br /><br />
                                    <a href="dashboard_enseignant.php" style="margin-left: 38%;">Retour au Tableau de Bord</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<?php include('index.js'); ?>

</html>
