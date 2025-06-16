<?php
// profil_etudiant.php

session_start();
require_once 'connexion.php'; // Use require_once for critical dependencies

// Redirect if user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT nom, prenom, date_naissance,	numero_telephone, role FROM accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: login.php");
    exit();
}

// Restrict access to students
if ($user['role'] !== 'etudiant') {
    header("Location: unauthorized.php");
    exit();
}

// Extract user details
$nom = $user['nom'];
$prenom = $user['prenom'];
$date_naissance = $user['date_naissance'];
$numero_tel = $user['numero_tel'] ?: 'Non renseigné';

// Calculate age
$age = 'Non calculé';
if ($date_naissance) {
    $birthDate = new DateTime($date_naissance);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Université XYZ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
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

        .profile-card {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }

        .profile-card div {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 16px;
        }

        .profile-card div:last-child {
            border-bottom: none;
        }

        .profile-card strong {
            color: #333;
            font-weight: 500;
        }

        .profile-card span {
            color: #555;
        }

        .btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 12px;
            background-color: #6c5ce7;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: rgb(24, 0, 238);
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

            .profile-card div {
                flex-direction: column;
                gap: 5px;
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
                <h1>Mon Profil</h1>
                <div class="profile-card">
                    <div>
                        <strong>Nom</strong>
                        <span><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></span>
                    </div>
                    <div>
                        <strong>Email</strong>
                        <span><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <div>
                        <strong>Date de naissance</strong>
                        <span><?php echo htmlspecialchars($date_naissance); ?></span>
                    </div>
                    <div>
                        <strong>Âge</strong>
                        <span><?php echo htmlspecialchars($age); ?> ans</span>
                    </div>
                    <div>
                        <strong>Numéro de téléphone</strong>
                        <span><?php echo htmlspecialchars($numero_tel); ?></span>
                    </div>
                </div>
                <a href="modifier_profil.php" class="btn">Modifier mon profil</a>
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