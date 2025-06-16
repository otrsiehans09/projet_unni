<?php
// ajouter_seance.php

session_start();
require_once 'connexion.php';

// Redirect if not logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT id, role FROM accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || $user['role'] !== 'enseignant') {
    header("Location: unauthorized.php");
    exit();
}

$enseignant_id = $user['id'];
$success_message = '';
$error_message = '';

// Fetch specialties
$specialites = [];
$stmt = $conn->prepare("SELECT id, nom_specialite FROM specialite ORDER BY nom_specialite");
$stmt->execute();
$result_specialites = $stmt->get_result();
while ($spec = $result_specialites->fetch_assoc()) {
    $specialites[] = $spec;
}
$stmt->close();

// Fetch teacher's sessions
$sessions = [];
$stmt = $conn->prepare("
    SELECT s.id, s.titre, s.date_seance, s.duree, s.commentaire, sp.nom_specialite
    FROM seances s
    JOIN specialite sp ON s.specialite_id = sp.id
    WHERE s.enseignant_id = ? AND s.date_seance >= CURDATE()
    ORDER BY s.date_seance ASC
");
$stmt->bind_param("i", $enseignant_id);
$stmt->execute();
$result_sessions = $stmt->get_result();
while ($session = $result_sessions->fetch_assoc()) {
    $sessions[] = $session;
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre_seance = trim($_POST['titre_seance'] ?? '');
    $date_seance = $_POST['date_seance'] ?? '';
    $duree_seance = $_POST['duree_seance'] ?? '';
    $commentaire_seance = trim($_POST['commentaire_seance'] ?? '');
    $specialite_id = (int)($_POST['specialite_id'] ?? 0);

    // Validation
    $errors = [];
    if (empty($titre_seance)) {
        $errors[] = "Le titre est requis.";
    }
    if (empty($date_seance) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $date_seance)) {
        $errors[] = "Date invalide.";
    }
    if (empty($duree_seance) || !preg_match("/^\d{2}:\d{2}(:\d{2})?$/", $duree_seance)) {
        $errors[] = "Durée invalide (HH:MM).";
    }
    if ($specialite_id <= 0) {
        $errors[] = "Spécialité requise.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO seances (titre, date_seance, duree, commentaire, enseignant_id, specialite_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssii", $titre_seance, $date_seance, $duree_seance, $commentaire_seance, $enseignant_id, $specialite_id);

        if ($stmt->execute()) {
            $success_message = "Séance ajoutée avec succès.";
            // Refresh sessions
            $stmt->close();
            $stmt = $conn->prepare("
                SELECT s.id, s.titre, s.date_seance, s.duree, s.commentaire, sp.nom_specialite
                FROM seances s
                JOIN specialite sp ON s.specialite_id = sp.id
                WHERE s.enseignant_id = ? AND s.date_seance >= CURDATE()
                ORDER BY s.date_seance ASC
            ");
            $stmt->bind_param("i", $enseignant_id);
            $stmt->execute();
            $sessions = [];
            $result_sessions = $stmt->get_result();
            while ($session = $result_sessions->fetch_assoc()) {
                $sessions[] = $session;
            }
        } else {
            $error_message = "Erreur : " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = implode("<br>", $errors);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planifier une Séance - Université XYZ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
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

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #6c5ce7;
            outline: none;
        }

        select {
            appearance: none;
            background: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236c5ce7' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") no-repeat right 1rem center;
            background-size: 1.2em;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .submit-btn {
            background: #6c5ce7;
            color: white;
            padding: 16px 24px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            max-width: 200px;
        }

        .submit-btn:hover {
            background: #5a4bd6;
        }

        .cancel-btn {
            background: #e0e0e0;
            color: #333;
            padding: 16px 24px;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            max-width: 200px;
        }

        .cancel-btn:hover {
            background: #d0d0d0;
        }

        .sessions-section {
            margin-top: 3rem;
        }

        .sessions-section h2 {
            font-size: 20px;
            color: #333;
            font-weight: 600;
        }

        .sessions-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
        }

        .sessions-table th, .sessions-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .sessions-table th {
            background: #6c5ce7;
            color: white;
            font-weight: 600;
        }

        .sessions-table td {
            font-size: 14px;
            color: #555;
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

            .sessions-table {
                display: block;
                overflow-x: auto;
            }

            .button-group {
                flex-direction: column;
                gap: 0.75rem;
            }

            .submit-btn, .cancel-btn {
                max-width: 100%;
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

            <!-- Main Content -->
            <div class="container">
                <div class="form-header">
                    <h1><i class="bi bi-calendar-plus"></i> Nouvelle Séance</h1>
                </div>

                <!-- Form -->
                <form method="POST" action="ajouter_seance.php" id="session-form">
                    <div class="form-group">
                        <label for="specialite_id"><i class="bi bi-book"></i> Spécialité</label>
                        <select name="specialite_id" id="specialite_id" required>
                            <option value="">-- Sélectionner une spécialité --</option>
                            <?php foreach ($specialites as $spec): ?>
                                <option value="<?php echo htmlspecialchars($spec['id']); ?>">
                                    <?php echo htmlspecialchars($spec['nom_specialite']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="titre_seance"><i class="bi bi-fonts"></i> Titre</label>
                        <input type="text" name="titre_seance" id="titre_seance" placeholder="Ex. Introduction à la programmation" required>
                    </div>

                    <div class="form-group">
                        <label for="date_seance"><i class="bi bi-calendar"></i> Date</label>
                        <input type="date" name="date_seance" id="date_seance" required>
                    </div>

                    <div class="form-group">
                        <label for="duree_seance"><i class="bi bi-clock"></i> Durée (HH:MM)</label>
                        <input type="time" name="duree_seance" id="duree_seance" required>
                    </div>

                    <div class="form-group">
                        <label for="commentaire_seance"><i class="bi bi-chat-dots"></i> Notes</label>
                        <textarea name="commentaire_seance" id="commentaire_seance" placeholder="Objectifs, matériel, etc."></textarea>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="submit-btn">
                            <i class="bi bi-calendar-plus"></i> Ajouter la Séance
                        </button>
                        <button type="button" class="cancel-btn" onclick="window.location.href='dashboard_enseignant.php'">
                            <i class="bi bi-x-circle"></i> Annuler
                        </button>
                    </div>
                </form>

                <!-- Sessions List -->
                <div class="sessions-section">
                    <h2><i class="bi bi-calendar-check"></i> Séances Programmées</h2>
                    <?php if (empty($sessions)): ?>
                        <p style="text-align: center; color: #666;">Aucune séance programmée.</p>
                    <?php else: ?>
                        <table class="sessions-table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Spécialité</th>
                                    <th>Date</th>
                                    <th>Durée</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $session): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($session['titre']); ?></td>
                                        <td><?php echo htmlspecialchars($session['nom_specialite']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($session['date_seance'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($session['duree'])); ?></td>
                                        <td><?php echo htmlspecialchars($session['commentaire'] ?: 'Aucun'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer -->
            <footer class="university-footer">
                <?php include 'footer.php'; ?>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('session-form').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Confirmer ?',
                text: 'Ajouter cette séance ?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6c5ce7',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, ajouter',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

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