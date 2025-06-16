<?php
// profil.php

session_start();
require_once 'connexion.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch admin details
$stmt = $conn->prepare("SELECT id, cin, email, date_de_creation, role, nom, prenom, image, classe, date_naissance, adresse, numero_telephone FROM accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || $user['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Université XYZ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <?php include('index.css'); ?>
    <style>
    .profile-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .profile-header h1 {
        color: #333;
        font-size: 24px;
        font-weight: 600;
    }

    .profile-image {
        display: block;
        margin: 0 auto 1rem;
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #6c5ce7;
    }

    .info-group {
        margin-bottom: 1rem;
    }

    .info-label {
        display: block;
        color: #6c5ce7;
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        color: #333;
        font-size: 16px;
        padding: 12px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        .profile-image {
            width: 120px;
            height: 120px;
        }
    }
</style>
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <?php include 'sidenav.php'; ?>
        </div>

        <div class="main-panel">
            <div class="navtop">
                <?php include 'navtop.php'; ?>
            </div>

            <div class="container">
                <div class="profile-header">
                    <h1><i class="bi bi-person-circle"></i> Profil Admin</h1>
                </div>

                <img src="<?php echo htmlspecialchars($user['image'] ?? 'uploads/default.jpg'); ?>" alt="Profile Image" class="profile-image">

                <div class="profile-info">
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-hash"></i> ID</span>
                        <div class="info-value"><?php echo htmlspecialchars($user['id']); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-card-id"></i> CIN</span>
                        <div class="info-value"><?php echo htmlspecialchars($user['cin']); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-envelope"></i> Email</span>
                        <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-person"></i> Nom</span>
                        <div class="info-value"><?php echo htmlspecialchars($user['nom']); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-person"></i> Prénom</span>
                        <div class="info-value"><?php echo htmlspecialchars($user['prenom']); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-calendar"></i> Date de Naissance</span>
                        <div class="info-value"><?php echo htmlspecialchars(date('d/m/Y', strtotime($user['date_naissance']))); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-geo-alt"></i> Adresse</span>
                        <div class="info-value"><?php echo htmlspecialchars($user['adresse']); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-telephone"></i> Numéro de Téléphone</span>
                        <div class="info-value"><?php echo htmlspecialchars($user['numero_telephone']); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-shield-check"></i> Rôle</span>
                        <div class="info-value"><?php echo htmlspecialchars($user['role']); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-calendar-plus"></i> Date de Création</span>
                        <div class="info-value"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user['date_de_creation']))); ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label"><i class="bi bi-mortarboard"></i> Classe</span>
                        <div class="info-value"><?php echo htmlspecialchars($user['classe'] ?? 'Aucune'); ?></div>
                    </div>
                </div>

                <a href="modifier_profil.php" class="edit-btn">
                    <i class="bi bi-person-gear"></i> Modifier le Profil
                </a>
            </div>

            <footer class="university-footer">
                <?php include 'footer.php'; ?>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include('index.js'); ?>
</body>
</html>