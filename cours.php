<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

include('connexion.php');

// Récupérer l'ID de l'étudiant connecté
$sql_fetch_user = "SELECT * FROM accounts WHERE email = ?";
$stmt = $conn->prepare($sql_fetch_user);
$stmt->bind_param("s", $email);
$stmt->execute();
$result_user = $stmt->get_result();
$row = $result_user->fetch_assoc();
$id_etudiant = $row['id']; // L'ID de l'étudiant connecté

// Traitement des actions d'inscription/désinscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['specialite_id'])) {
        $specialite_id = $_POST['specialite_id'];
        
        if ($_POST['action'] === 'inscrire') {
            // Inscription
            $sql_inscrire = "INSERT INTO inscriptions (id_etudiant, id_specialite, date_inscription) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($sql_inscrire);
            $stmt->bind_param("ii", $id_etudiant, $specialite_id);
            $stmt->execute();
        } elseif ($_POST['action'] === 'desinscrire') {
            // Désinscription
            $sql_desinscrire = "DELETE FROM inscriptions WHERE id_etudiant = ? AND id_specialite = ?";
            $stmt = $conn->prepare($sql_desinscrire);
            $stmt->bind_param("ii", $id_etudiant, $specialite_id);
            $stmt->execute();
        }
        
        // Redirection pour éviter la resoumission du formulaire
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('index.css'); ?>
    <style>
        .swal2-popup {
            font-size: 14px !important;
        }

        .modal-backdrop.in {
            opacity: 0 !important;
        }
        
        /* Style supplémentaire pour les boutons */
        .btn-inscription {
            transition: all 0.3s ease;
        }
        .btn-inscription:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-desinscription {
            background-color: #f44336 !important;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include('sidenav.php'); ?>

        <div class="main-panel">
            <?php include('navtop.php'); ?>

            <br />

            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h4 class="title">Gestion des inscriptions aux spécialités</h4>
                                    <p class="category">Inscrivez-vous ou désinscrivez-vous des spécialités disponibles</p>
                                </div>
                                <div class="content table-responsive table-full-width">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Spécialité</th>
                                                <th>Date d'ajout</th>
                                                <th>Statut</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Récupérer toutes les spécialités disponibles
                                            $sqlfetsh = "SELECT * FROM specialite ORDER BY id ASC";
                                            $res = $conn->query($sqlfetsh);
                                            while ($row = $res->fetch_assoc()) {
                                                $specialite_id = $row['id'];

                                                // Vérification si l'étudiant est déjà inscrit à cette spécialité
                                                $check_inscription = "SELECT * FROM inscriptions WHERE id_etudiant = ? AND id_specialite = ?";
                                                $stmt_check = $conn->prepare($check_inscription);
                                                $stmt_check->bind_param("ii", $id_etudiant, $specialite_id);
                                                $stmt_check->execute();
                                                $inscription_res = $stmt_check->get_result();
                                                $already_registered = ($inscription_res->num_rows > 0);
                                                
                                                // Récupérer la date d'inscription si déjà inscrit
                                                $date_inscription = '';
                                                if ($already_registered) {
                                                    $inscription_data = $inscription_res->fetch_assoc();
                                                    $date_inscription = $inscription_data['date_inscription'];
                                                }
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($row['nom_specialite']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['date_de_creation']); ?></td>
                                                    <td>
                                                        <?php if ($already_registered) { ?>
                                                            <span class="label label-success">Inscrit depuis <?php echo date('d/m/Y', strtotime($date_inscription)); ?></span>
                                                        <?php } else { ?>
                                                            <span class="label label-default">Non inscrit</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <form method="POST" action="" style="display: inline;">
                                                            <input type="hidden" name="specialite_id" value="<?php echo $specialite_id; ?>">
                                                            <?php if ($already_registered) { ?>
                                                                <input type="hidden" name="action" value="desinscrire">
                                                                <button type="submit" class="btn btn-desinscription btn-fill btn-inscription">
                                                                    <i class="pe-7s-close-circle"></i> Se désinscrire
                                                                </button>
                                                            <?php } else { ?>
                                                                <input type="hidden" name="action" value="inscrire">
                                                                <button type="submit" class="btn btn-primary btn-fill btn-inscription">
                                                                    <i class="pe-7s-check"></i> S'inscrire
                                                                </button>
                                                            <?php } ?>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('footer.php'); ?>

        </div>
    </div>

    <?php include('index.js'); ?>
    <script>
        // Script pour confirmation avant désinscription
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                if ($(this).find('input[name="action"]').val() === 'desinscrire') {
                    e.preventDefault();
                    swal({
                        title: "Confirmer la désinscription",
                        text: "Êtes-vous sûr de vouloir vous désinscrire de cette spécialité ?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#f44336",
                        confirmButtonText: "Oui, se désinscrire",
                        cancelButtonText: "Annuler",
                        closeOnConfirm: false
                    }, function() {
                        e.target.submit();
                    });
                }
            });
        });
    </script>
</body>

</html>
