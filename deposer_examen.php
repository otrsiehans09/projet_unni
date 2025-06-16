<?php
// deposer_examen.php

session_start();
require_once 'connexion.php'; // Use require_once for critical dependencies

// Redirect if user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT id, role FROM accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: login.php");
    exit();
}

if ($user['role'] !== 'enseignant') {
    header("Location: unauthorized.php");
    exit();
}

$enseignant_id = $user['id'];

// Fetch specialties
$specialites = [];
$stmt = $conn->prepare("SELECT id, nom_specialite FROM specialite");
$stmt->execute();
$result_specialites = $stmt->get_result();
while ($spec = $result_specialites->fetch_assoc()) {
    $specialites[] = $spec;
}
$stmt->close();

// Fetch teacher's exams
$stmt = $conn->prepare("SELECT e.id, e.titre, e.date_exam, e.fichier, s.nom_specialite 
                        FROM examens e 
                        JOIN specialite s ON e.specialite_id = s.id 
                        WHERE e.enseignant_id = ? 
                        ORDER BY e.date_exam DESC");
$stmt->bind_param("i", $enseignant_id);
$stmt->execute();
$result_exams = $stmt->get_result();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titre_examen = $_POST['titre_examen'];
    $date_examen = $_POST['date_examen'];
    $specialite_id = (int)$_POST['specialite_id'];

    $target_dir = "Uploads/examens/";
    $fichier_examen = basename($_FILES['fichier_examen']['name']);
    $target_file = $target_dir . $fichier_examen;

    // Create directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["fichier_examen"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO examens (titre, date_exam, fichier, enseignant_id, specialite_id) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $titre_examen, $date_examen, $fichier_examen, $enseignant_id, $specialite_id);

        if ($stmt->execute()) {
            echo "<script>alert('Examen déposé avec succès'); window.location.href = 'deposer_examen.php';</script>";
        } else {
            echo "<div class='error'>Erreur base de données : " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='error'>Erreur lors de l'envoi du fichier.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer un Examen - Université XYZ</title>
    <?php include('index.css'); ?>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
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
            background-color: rgb(2, 19, 255);
            color: white;
            padding-top: 20px;
            z-index: 1000;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
        }

        .main-panel {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button[type="submit"] {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            background-color: #6c5ce7;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: rgb(24, 0, 238);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #6c5ce7;
            color: #fff;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .file-link {
            color: #6c5ce7;
            text-decoration: none;
        }

        .file-link:hover {
            text-decoration: underline;
        }

        .error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
        }

        .no-exams {
            text-align: center;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <?php include 'sidenav.php'; ?>
        </div>

        <!-- Main Panel -->
        <div class="main-panel">
            <!-- Top Navigation -->
            <div class="navtop">
                <?php include 'navtop.php'; ?>
            </div>

            <div class="container">
                <h1>Déposer un Examen</h1>
                <form method="POST" action="deposer_examen.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="specialite_id">Spécialité :</label>
                        <select class="form-control" name="specialite_id" required>
                            <option value="">-- Sélectionner une spécialité --</option>
                            <?php foreach ($specialites as $spec): ?>
                                <option value="<?php echo htmlspecialchars($spec['id']); ?>">
                                    <?php echo htmlspecialchars($spec['nom_specialite']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="titre_examen">Titre de l'Examen :</label>
                        <input type="text" class="form-control" name="titre_examen" required>
                    </div>
                    <div class="form-group">
                        <label for="date_examen">Date de l'Examen :</label>
                        <input type="date" class="form-control" name="date_examen" required>
                    </div>
                    <div class="form-group">
                        <label for="fichier_examen">Fichier de l'Examen :</label>
                        <input type="file" class="form-control" name="fichier_examen" required>
                    </div>
                    <button type="submit">Déposer l'Examen</button>
                </form>

                <h1>Mes Examens Déposés</h1>
                <?php if ($result_exams->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Spécialité</th>
                                <th>Date Examen</th>
                                <th>Fichier</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($exam = $result_exams->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($exam['titre']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['nom_specialite']); ?></td>
                                    <td><?php echo htmlspecialchars($exam['date_exam']); ?></td>
                                    <td>
                                        <a href="Uploads/examens/<?php echo htmlspecialchars($exam['fichier']); ?>" 
                                           class="file-link" target="_blank">
                                            <?php echo htmlspecialchars($exam['fichier']); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-exams">Aucun examen déposé pour le moment.</p>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <footer class="university-footer">
                <?php include 'footer.php'; ?>
            </footer>
        </div>
    </div>

    <?php include('index.js'); ?>
</body>
</html>

<?php
// Clean up
$result_exams->free();
$conn->close();
?>