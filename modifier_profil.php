<?php
// modifier_profil.php

session_start();
require_once 'connexion.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT id, cin, email, mot_de_passe, date_de_creation, role, nom, prenom, image, classe, date_naissance, adresse, numero_telephone FROM accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($user['role'] === 'etudiant') {
        // Student: Submit modification request
        $champ_modifié = $_POST['champ_modifié'] ?? '';
        $nouvelle_valeur = trim($_POST['nouvelle_valeur'] ?? '');

        $valid_champs = ['nom', 'prenom', 'date_naissance', 'email'];
        if (!in_array($champ_modifié, $valid_champs)) {
            $error_message = "Champ invalide.";
        } elseif (empty($nouvelle_valeur)) {
            $error_message = "La nouvelle valeur est requise.";
        } elseif ($champ_modifié === 'email' && !filter_var($nouvelle_valeur, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Adresse email invalide.";
        } elseif ($champ_modifié === 'date_naissance' && !DateTime::createFromFormat('Y-m-d', $nouvelle_valeur)) {
            $error_message = "Format de date invalide (AAAA-MM-JJ).";
        } else {
            $stmt = $conn->prepare("INSERT INTO modifs_en_attente (id_etudiant, champ_modifié, nouvelle_valeur) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user['id'], $champ_modifié, $nouvelle_valeur);
            if ($stmt->execute()) {
                $success_message = "Demande envoyée avec succès !";
            } else {
                $error_message = "Erreur : " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        // Admin/Enseignant: Direct update
        $cin = trim($_POST['cin'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';
        $confirm_mot_de_passe = $_POST['confirm_mot_de_passe'] ?? '';
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $date_naissance = $_POST['date_naissance'] ?? '';
        $adresse = trim($_POST['adresse'] ?? '');
        $numero_telephone = trim($_POST['numero_telephone'] ?? '');
        $image = $user['image'];

        // Validation
        if (empty($cin) || !preg_match("/^[A-Za-z0-9]{8}$/", $cin)) {
            $errors[] = "CIN requis (8 caractères alphanumériques).";
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide.";
        }
        if (!empty($mot_de_passe) && $mot_de_passe !== $confirm_mot_de_passe) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (!empty($mot_de_passe) && strlen($mot_de_passe) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if (empty($nom)) {
            $errors[] = "Nom requis.";
        }
        if (empty($prenom)) {
            $errors[] = "Prénom requis.";
        }
        if (empty($date_naissance) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date_naissance)) {
            $errors[] = "Date de naissance invalide.";
        } else {
            $dob = new DateTime($date_naissance);
            $today = new DateTime();
            $age = $today->diff($dob)->y;
            if ($age < 18) {
                $errors[] = "Vous devez avoir au moins 18 ans.";
            }
        }
        if (empty($adresse)) {
            $errors[] = "Adresse requise.";
        }
        if (empty($numero_telephone) || !preg_match("/^\+\d{1,3}\d{6,14}$/", $numero_telephone)) {
            $errors[] = "Numéro de téléphone invalide (ex. +22612345678).";
        }

        // Image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowed_types = ['image/jpeg', 'image/png'];
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!in_array($file['type'], $allowed_types)) {
                $errors[] = "Image doit être JPG ou PNG.";
            } elseif ($file['size'] > $max_size) {
                $errors[] = "Image trop grande (max 2MB).";
            } else {
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = ($user['role'] === 'admin' ? 'admin' : 'enseignant') . '_' . $user['id'] . '_' . time() . '.' . $ext;
                $destination = 'uploads/' . $filename;
                if (!move_uploaded_file($file['tmp_name'], $destination)) {
                    $errors[] = "Erreur lors du téléchargement de l'image.";
                } else {
                    $image = $destination;
                    if ($user['image'] && file_exists($user['image']) && $user['image'] !== 'uploads/default.jpg') {
                        unlink($user['image']);
                    }
                }
            }
        }

        // Check email uniqueness
        if ($email !== $user['email']) {
            $stmt = $conn->prepare("SELECT id FROM accounts WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user['id']);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $errors[] = "Cet email est déjà utilisé.";
            }
            $stmt->close();
        }

        if (empty($errors)) {
            $query = "UPDATE accounts SET cin = ?, email = ?, nom = ?, prenom = ?, date_naissance = ?, adresse = ?, numero_telephone = ?, image = ?";
            $params = [$cin, $email, $nom, $prenom, $date_naissance, $adresse, $numero_telephone, $image];
            $types = "ssssssss";

            if (!empty($mot_de_passe)) {
                $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $query .= ", mot_de_passe = ?";
                $params[] = $hashed_password;
                $types .= "s";
            }

            $query .= " WHERE id = ?";
            $params[] = $user['id'];
            $types .= "i";

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $success_message = "Profil modifié avec succès.";
                $_SESSION['email'] = $email;
                $stmt->close();
                $stmt = $conn->prepare("SELECT id, cin, email, mot_de_passe, date_de_creation, role, nom, prenom, image, classe, date_naissance, adresse, numero_telephone FROM accounts WHERE id = ?");
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
            } else {
                $error_message = "Erreur : " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = implode("<br>", $errors);
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
    <title><?php echo $user['role'] === 'etudiant' ? 'Demander une Modification de Profil' : 'Modifier Profil'; ?> - Université XYZ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <?php include('index.css'); ?>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #f8f9fa;
        }


        

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h1 {
            color: #333;
            font-size: 24px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        label i {
            color: #6c5ce7;
            margin-right: 8px;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            background: #f9f9f9;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #6c5ce7;
            outline: none;
            background: #fff;
        }

        input[type="file"] {
            padding: 8px;
        }

        input[readonly] {
            background: #f1f1f1;
            cursor: not-allowed;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 30px;
        }

        
        .submit-btn:hover {
            background: #5a4bd6;
        }

        

        .cancel-btn:hover {
            background: #d0d0d0;
        }

        .profile-image {
            display: block;
            margin: 0 auto 1rem;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #6c5ce7;
        }

        .error, .success {
            text-align: center;
            margin-bottom: 1rem;
            font-size: 14px;
        }

        .error {
            color: #e74c3c;
        }

        .success {
            color: #27ae60;
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

            .button-group {
                flex-direction: column;
                gap: 0.75rem;
            }

            .submit-btn, .cancel-btn {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
    .profile-image {
        width: 100px;
        height: 100px;
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
                <div class="form-header">
                    <h1>
                        <i class="bi bi-person-gear"></i>
                        <?php echo $user['role'] === 'etudiant' ? 'Demander une Modification de Profil' : 'Modifier Profil'; ?>
                    </h1>
                </div>

                <?php if ($success_message): ?>
                    <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <?php if ($user['role'] === 'etudiant'): ?>
                    <!-- Student Form -->
                    <form method="POST" action="modifier_profil.php" id="student-form">
                        <div class="form-group">
                            <label for="champ_modifié"><i class="bi bi-list"></i> Champ à modifier</label>
                            <select name="champ_modifié" id="champ_modifié" class="form-control" required>
                                <option value="">-- Sélectionner un champ --</option>
                                <option value="nom">Nom</option>
                                <option value="prenom">Prénom</option>
                                <option value="date_naissance">Date de naissance</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="nouvelle_valeur"><i class="bi bi-pencil"></i> Nouvelle valeur</label>
                            <input type="text" name="nouvelle_valeur" id="nouvelle_valeur" class="form-control" placeholder="Entrez la nouvelle valeur" required>
                        </div>
                        <div class="button-group">
                            <button type="submit" class="submit-btn">
                                <i class="bi bi-send"></i> Envoyer la demande
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Admin/Enseignant Form -->
                    <img src="<?php echo htmlspecialchars($user['image'] ?? 'uploads/default.jpg'); ?>" alt="Profile Image" class="profile-image">
                    <form method="POST" action="modifier_profil.php" enctype="multipart/form-data" id="profile-form">
                        <div class="form-group">
                            <label for="id"><i class="bi bi-hash"></i> ID</label>
                            <input type="text" name="id" id="id" value="<?php echo htmlspecialchars($user['id']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="cin"><i class="bi bi-card-id"></i> CIN</label>
                            <input type="text" name="cin" id="cin" value="<?php echo htmlspecialchars($user['cin']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="bi bi-envelope"></i> Email</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="mot_de_passe"><i class="bi bi-lock"></i> Nouveau Mot de Passe (laisser vide pour ne pas changer)</label>
                            <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Minimum 8 caractères">
                        </div>
                        <div class="form-group">
                            <label for="confirm_mot_de_passe"><i class="bi bi-lock"></i> Confirmer Mot de Passe</label>
                            <input type="password" name="confirm_mot_de_passe" id="confirm_mot_de_passe" placeholder="Répéter le mot de passe">
                        </div>
                        <div class="form-group">
                            <label for="nom"><i class="bi bi-person"></i> Nom</label>
                            <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="prenom"><i class="bi bi-person"></i> Prénom</label>
                            <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="image"><i class="bi bi-image"></i> Photo de Profil (JPG/PNG, max 2MB)</label>
                            <input type="file" name="image" id="image" accept="image/jpeg,image/png">
                        </div>
                        <div class="form-group">
                            <label for="date_naissance"><i class="bi bi-calendar"></i> Date de Naissance</label>
                            <input type="date" name="date_naissance" id="date_naissance" value="<?php echo htmlspecialchars($user['date_naissance']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="adresse"><i class="bi bi-geo-alt"></i> Adresse</label>
                            <textarea name="adresse" id="adresse" required><?php echo htmlspecialchars($user['adresse']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="numero_telephone"><i class="bi bi-telephone"></i> Numéro de Téléphone</label>
                            <input type="text" name="numero_telephone" id="numero_telephone" value="<?php echo htmlspecialchars($user['numero_telephone']); ?>" placeholder="+22612345678" required>
                        </div>
                        <div class="form-group">
                            <label for="role"><i class="bi bi-shield-check"></i> Rôle</label>
                            <input type="text" name="role" id="role" value="<?php echo htmlspecialchars($user['role']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="date_de_creation"><i class="bi bi-calendar-plus"></i> Date de Création</label>
                            <input type="text" name="date_de_creation" id="date_de_creation" value="<?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user['date_de_creation']))); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="classe"><i class="bi bi-mortarboard"></i> Classe</label>
                            <input type="text" name="classe" id="classe" value="<?php echo htmlspecialchars($user['classe'] ?? 'Aucune'); ?>" readonly>
                        </div>
                        <div class="button-group">
                            <button type="submit" class="submit-btn">
                                <i class="bi bi-person-check"></i> Modifier le Profil
                            </button>
                            <button type="button" class="cancel-btn" onclick="window.location.href='dashboard_<?php echo $user['role']; ?>.php'">
                                <i class="bi bi-x-circle"></i> Annuler
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <footer class="university-footer">
                <?php include 'footer.php'; ?>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($user['role'] === 'etudiant'): ?>
            document.getElementById('student-form').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Confirmer ?',
                    text: 'Envoyer la demande de modification ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6c5ce7',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, envoyer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        <?php else: ?>
            document.getElementById('profile-form').addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Confirmer ?',
                    text: 'Modifier votre profil ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6c5ce7',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Oui, modifier',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        <?php endif; ?>

        <?php if ($success_message): ?>
            Swal.fire({
                icon: 'success',
                title: 'Succès',
                text: '<?php echo addslashes($success_message); ?>',
                confirmButtonColor: '#6c5ce7'
            });
        <?php endif; ?>
        <?php if ($error_message): ?>
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                html: '<?php echo addslashes($error_message); ?>',
                confirmButtonColor: '#6c5ce7'
            });
        <?php endif; ?>
    </script>
    <?php include('index.js'); ?>
</body>
</html>