<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

include('connexion.php');

$email = $_SESSION['email'];
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];
    $enseignant_id = $row['id'];
} else {
    header("Location: login.php");
    exit();
}

if ($role !== 'enseignant') {
    header("Location: unauthorized.php");
    exit();
}

// Récupérer l'ID de la séance
if (!isset($_GET['id_seance'])) {
    echo "ID de séance non spécifié.";
    exit();
}

$id_seance = intval($_GET['id_seance']);

// Mise à jour des présences
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['presence'] as $etudiant_id => $presence) {
        $commentaire = $_POST['commentaire'][$etudiant_id] ?? '';

        // Vérifier si la ligne existe déjà
        $check_sql = "SELECT * FROM seance_etudiants WHERE id_seance = $id_seance AND id_etudiant = $etudiant_id";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            // Mettre à jour
            $update_sql = "UPDATE seance_etudiants SET presence = '$presence', commentaire = '$commentaire' 
                           WHERE id_seance = $id_seance AND id_etudiant = $etudiant_id";
            $conn->query($update_sql);
        } else {
            // Insérer
            $insert_sql = "INSERT INTO seance_etudiants (id_seance, id_etudiant, presence, commentaire) 
                           VALUES ($id_seance, $etudiant_id, '$presence', '$commentaire')";
            $conn->query($insert_sql);
        }
    }

    echo "<script>alert('Présences mises à jour avec succès.'); window.location.href = 'gestion_seance.php?id_seance=$id_seance';</script>";
    exit();
}

// Récupérer les infos de la séance
$sql_seance = "SELECT * FROM seances WHERE id = $id_seance AND enseignant_id = $enseignant_id";
$result_seance = $conn->query($sql_seance);

if ($result_seance->num_rows === 0) {
    echo "Séance non trouvée ou vous n'avez pas les droits.";
    exit();
}

$seance = $result_seance->fetch_assoc();

// Récupérer tous les étudiants
$sql_etudiants = "SELECT * FROM accounts WHERE role = 'etudiant' ORDER BY nom ASC";
$result_etudiants = $conn->query($sql_etudiants);

// Récupérer les présences associées à la séance
$presences = [];
$sql_presences = "SELECT * FROM seance_etudiants WHERE id_seance = $id_seance";
$result_presences = $conn->query($sql_presences);

while ($row = $result_presences->fetch_assoc()) {
    $presences[$row['id_etudiant']] = [
        'presence' => $row['presence'],
        'commentaire' => $row['commentaire']
    ];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Gérer la Séance</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 1000px; margin: 40px auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        h2, h3 { margin-bottom: 10px; }
        .btn { padding: 10px 20px; background-color: #2E8B57; color: white; border: none; cursor: pointer; }
        .btn:hover { background-color: #246f47; }
    </style>
</head>
<body>

<div class="container">
    <h2>Gestion de la séance : <?= htmlspecialchars($seance['titre']) ?></h2>
    <p><strong>Date :</strong> <?= $seance['date_seance'] ?> | <strong>Durée :</strong> <?= $seance['duree'] ?></p>
    <p><strong>Commentaire :</strong> <?= nl2br(htmlspecialchars($seance['commentaire'])) ?></p>

    <form method="POST">
        <h3>Liste des étudiants</h3>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Présence</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($etudiant = $result_etudiants->fetch_assoc()) {
                    $id = $etudiant['id'];
                    $presence_val = $presences[$id]['presence'] ?? '';
                    $commentaire_val = $presences[$id]['commentaire'] ?? '';
                ?>
                    <tr>
                        <td><?= htmlspecialchars($etudiant['nom']) ?></td>
                        <td><?= htmlspecialchars($etudiant['prenom']) ?></td>
                        <td>
                            <select name="presence[<?= $id ?>]">
                                <option value="">-- Choisir --</option>
                                <option value="present" <?= $presence_val === 'present' ? 'selected' : '' ?>>Présent</option>
                                <option value="absent" <?= $presence_val === 'absent' ? 'selected' : '' ?>>Absent</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="commentaire[<?= $id ?>]" value="<?= htmlspecialchars($commentaire_val) ?>">
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <br>
        <button type="submit" class="btn">Enregistrer</button>
    </form>
</div>

</body>
</html>
