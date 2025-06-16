<?php
// change_password.php

session_start();
require_once 'connexion.php'; // Use require_once for critical dependencies

// Redirect if user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Basic validation
    if (empty($old_password) || empty($new_password)) {
        $message = "Tous les champs sont requis.";
    } elseif (strlen($new_password) < 8) {
        $message = "Le nouveau mot de passe doit contenir au moins 8 caractères.";
    } else {
        // Fetch current password hash
        $stmt = $conn->prepare("SELECT mot_de_passe FROM accounts WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['mot_de_passe'];

            if (password_verify($old_password, $hashed_password)) {
                // Hash new password
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password
                $stmt = $conn->prepare("UPDATE accounts SET mot_de_passe = ? WHERE email = ?");
                $stmt->bind_param("ss", $new_hashed_password, $email);

                if ($stmt->execute()) {
                    $message = "Mot de passe modifié avec succès.";
                    session_unset();
                    session_destroy();
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès',
                            text: '$message',
                            confirmButtonColor: '#6c5ce7'
                        }).then(() => { window.location.href = 'login.php'; });
                    </script>";
                    $stmt->close();
                    exit();
                } else {
                    $message = "Erreur lors de la mise à jour du mot de passe.";
                }
                $stmt->close();
            } else {
                $message = "Ancien mot de passe incorrect.";
            }
        } else {
            $message = "Utilisateur non trouvé.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Mot de Passe - Université XYZ</title>
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
            max-width: 500px;
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
                <h1>Modifier Votre Mot de Passe</h1>
                <?php if ($message && $message !== "Mot de passe modifié avec succès."): ?>
                    <div class="error"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <form method="POST" action="change_password.php">
                    <div class="form-group">
                        <label for="old_password">Ancien Mot de Passe :</label>
                        <input type="password" class="form-control" name="old_password" 
                               placeholder="Entrez votre ancien mot de passe" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nouveau Mot de Passe :</label>
                        <input type="password" class="form-control" name="new_password" 
                               placeholder="Entrez votre nouveau mot de passe" required>
                    </div>
                    <button type="submit">Modifier le Mot de Passe</button>
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