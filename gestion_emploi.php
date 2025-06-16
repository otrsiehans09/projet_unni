<?php
// gestion_emploi.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('connexion.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Fetch classes
$sql_classes = "SELECT id AS id_classe, nom AS nom_classe FROM classe";
$result_classes = $conn->query($sql_classes);
if (!$result_classes) {
    die("Erreur lors de la récupération des classes : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Emplois du Temps</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <?php include('index.css'); ?>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #f8f9fa;
        }

        .wrapper {
            display: flex;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background: rgb(2, 19, 255);
            color: white;
            padding-top: 20px;
            z-index: 1000;
        }

        .main-panel {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
        }

        h2 {
            color: #333;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        h3 {
            color: #333;
            font-size: 20px;
            font-weight: 500;
            margin: 1rem 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #6c5ce7;
            color: white;
            font-weight: 600;
        }

        td {
            color: #555;
            font-size: 14px;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-size: 16px;
            margin: 1rem 0;
        }

        a {
            color: #00c9a7;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        hr {
            border: 1px solid #444;
            margin: 2rem 0;
        }

        @media (max-width: 768px) {
            .main-panel {
                margin-left: 0;
                width: 100%;
            }

            .sidebar {
                display: none;
            }

            .container {
                margin: 1rem;
                padding: 1.5rem;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            th, td {
                font-size: 12px;
                padding: 10px;
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
                <div class="container">
                    <h2>Gestion des Emplois du Temps</h2>

                    <?php while ($classe = $result_classes->fetch_assoc()) : ?>
                        <h3>Classe : <?php echo htmlspecialchars($classe['nom_classe'] ?? 'Nom inconnu'); ?></h3>

                        <?php
                        $id_classe = $classe['id_classe'];
                        $sql_emploi = "
                            SELECT e.id, e.jour, e.heure_debut, e.heure_fin, e.matiere, e.salle, s.nom_specialite
                            FROM emplois_du_temps e
                            JOIN specialite s ON e.id_specialite = s.id
                            WHERE e.id_classe = ?
                            ORDER BY 
                            FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'),
                            heure_debut
                        ";
                        $stmt = $conn->prepare($sql_emploi);
                        $stmt->bind_param("i", $id_classe);
                        $stmt->execute();
                        $result_emploi = $stmt->get_result();
                        ?>

                        <?php if ($result_emploi->num_rows > 0): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Jour</th>
                                        <th>Heure Début</th>
                                        <th>Heure Fin</th>
                                        <th>Spécialité</th>
                                        <th>Matière</th>
                                        <th>Salle</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($emploi = $result_emploi->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($emploi['jour']); ?></td>
                                            <td><?php echo substr($emploi['heure_debut'], 0, 5); ?></td>
                                            <td><?php echo substr($emploi['heure_fin'], 0, 5); ?></td>
                                            <td><?php echo htmlspecialchars($emploi['nom_specialite']); ?></td>
                                            <td><?php echo htmlspecialchars($emploi['matiere']); ?></td>
                                            <td><?php echo htmlspecialchars($emploi['salle'] ?? 'Non attribuée'); ?></td>
                                            <td>
                                                <a href="modifier_emploi.php?id=<?php echo $emploi['id']; ?>">Modifier</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="no-data">Aucun emploi du temps pour cette classe.</p>
                        <?php endif; ?>
                        <?php $stmt->close(); ?>
                        <hr/>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php include('footer.php'); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include('index.js'); ?>
</body>
</html>
<?php $conn->close(); ?>