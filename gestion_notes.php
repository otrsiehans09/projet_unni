<?php
// gestion_notes.php

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

// Fetch students
$etudiants = [];
$stmt = $conn->prepare("SELECT id, nom, prenom FROM accounts WHERE role = 'etudiant' ORDER BY prenom, nom");
$stmt->execute();
$result_etudiants = $stmt->get_result();
while ($etudiant = $result_etudiants->fetch_assoc()) {
    $etudiants[] = $etudiant;
}
$stmt->close();

// Fetch specialties
$specialites = [];
$stmt = $conn->prepare("SELECT id, nom_specialite FROM specialite ORDER BY nom_specialite");
$stmt->execute();
$result_specialites = $stmt->get_result();
while ($spec = $result_specialites->fetch_assoc()) {
    $specialites[] = $spec;
}
$stmt->close();

// Handle form submission
$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $etudiant_id = (int)$_POST['etudiant_id'];
    $specialite_id = (int)$_POST['specialite'];
    $note = (float)$_POST['note'];

    // Validate note
    if ($note < 0 || $note > 20) {
        $error = "La note doit être entre 0 et 20.";
    } else {
        $stmt = $conn->prepare("INSERT INTO notes (id_etudiant, id_specialite, note, enseignant_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iidi", $etudiant_id, $specialite_id, $note, $enseignant_id);

        if ($stmt->execute()) {
            echo "<script>Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: 'Note attribuée avec succès',
                confirmButtonColor: '#6c5ce7'
            }).then(() => { window.location.href = 'dashboard_enseignant.php'; });</script>";
        } else {
            $error = "Erreur base de données : " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attribuer une Note - Université XYZ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #6c5ce7;
            background-color: #fff;
        }

        .form-control::placeholder {
            color: #999;
        }

        button {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: #6c5ce7;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: rgb(24, 0, 238);
        }

        .error {
            color: #e74c3c;
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 30px;
        }

        @media screen and (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }

            .main-panel {
                margin-left: 0;
                width: 100%;
            }

            .sidebar {
                display: none; /* Hide sidebar on mobile; adjust based on your sidenav.php */
            }
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
                <h1>Attribuer une Note à un Étudiant</h1>
                <?php if ($error): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="gestion_notes.php">
                    <div class="form-group">
                        <label for="etudiant_id">Étudiant :</label>
                        <select name="etudiant_id" class="form-control" required>
                            <option value="">-- Choisir un étudiant --</option>
                            <?php foreach ($etudiants as $etudiant): ?>
                                <option value="<?php echo htmlspecialchars($etudiant['id']); ?>">
                                    <?php echo htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="specialite">Spécialité :</label>
                        <select name="specialite" class="form-control" required>
                            <option value="">-- Choisir une spécialité --</option>
                            <?php foreach ($specialites as $spec): ?>
                                <option value="<?php echo htmlspecialchars($spec['id']); ?>">
                                    <?php echo htmlspecialchars($spec['nom_specialite']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="note">Note (0-20) :</label>
                        <input type="number" step="0.01" min="0" max="20" class="form-control" 
                               name="note" placeholder="Ex: 15.50" required>
                    </div>
                    <button type="submit">Attribuer la Note</button>
                </form>
            </div>

            <!-- Footer -->
            <footer class="university-footer">
                <?php include 'footer.php'; ?>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include('index.js'); ?>
</body>
</html>