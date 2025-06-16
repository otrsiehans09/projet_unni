<?php
// dashboard_enseignant.php

session_start();
require_once 'connexion.php';

// Redirect if user is not logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Restrict access to admins and teachers
if (!in_array($_SESSION['role'], ['admin', 'enseignant'])) {
    header("Location: unauthorized.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT id, nom, prenom, role FROM accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: login.php");
    exit();
}

$user_id = $user['id'];

// Fetch stats
// Specialties taught (via seances)
$stmt = $conn->prepare("SELECT COUNT(DISTINCT specialite_id) as total FROM seances WHERE enseignant_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_specialites = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Upcoming sessions (next 7 days)
$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+7 days'));
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM seances WHERE enseignant_id = ? AND date_seance BETWEEN ? AND ?");
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$total_sessions = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Future exams
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM examens WHERE enseignant_id = ? AND date_exam >= CURDATE()");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_exams = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Fetch upcoming sessions (next 5)
$sessions = [];
$stmt = $conn->prepare("SELECT titre, date_seance, duree FROM seances WHERE enseignant_id = ? AND date_seance >= CURDATE() ORDER BY date_seance ASC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_sessions = $stmt->get_result();
while ($session = $result_sessions->fetch_assoc()) {
    $sessions[] = $session;
}
$stmt->close();

// Fetch recent activity (with fallback)
$activities = [];
try {
    $stmt = $conn->prepare("SELECT action, description, created_at FROM activity_log WHERE action LIKE ? ORDER BY created_at DESC LIMIT 5");
    $action_pattern = "%_teacher_%";
    $stmt->bind_param("s", $action_pattern);
    $stmt->execute();
    $result_activities = $stmt->get_result();
    while ($activity = $result_activities->fetch_assoc()) {
        $activities[] = $activity;
    }
    $stmt->close();
} catch (Exception $e) {
    $activities = []; // Fallback if activity_log table doesn't exist
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Enseignant - Université XYZ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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

        .header {
            background: linear-gradient(135deg, #6c5ce7, #a29bfe);
            color: white;
            padding: 1.5rem;
            border-radius: 0 0 20px 20px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 26px;
            margin: 0;
            font-weight: 600;
        }

        .header p {
            font-size: 14px;
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 28px;
            color: #6c5ce7;
            margin-bottom: 8px;
        }

        .stat-card h3 {
            font-size: 14px;
            color: #333;
            margin: 8px 0;
        }

        .stat-card p {
            font-size: 22px;
            font-weight: 600;
            color: #6c5ce7;
            margin: 0;
        }

        .function-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .function-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid #6c5ce7;
            position: relative;
            overflow: hidden;
        }

        .function-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .function-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(108, 92, 231, 0.05), rgba(255, 255, 255, 0));
            z-index: 0;
        }

        .function-card > * {
            position: relative;
            z-index: 1;
        }

        .function-card i {
            font-size: 2rem;
            color: #6c5ce7;
            margin-bottom: 0.8rem;
        }

        .function-card h2 {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 0.8rem;
            font-weight: 600;
        }

        .function-card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1.2rem;
        }

        .university-btn {
            background: #6c5ce7;
            color: white;
            padding: 0.7rem 1.2rem;
            border-radius: 6px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: background 0.3s;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .university-btn:hover {
            background: #5a4bd6;
        }

        .university-btn i {
            font-size: 0.9rem;
        }

        .schedule-preview {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .schedule-preview h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            overflow-x: auto;
            display: block;
        }

        .schedule-table th, .schedule-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .schedule-table th {
            background: #6c5ce7;
            color: white;
            font-weight: 600;
        }

        .schedule-table tr:hover {
            background: #f8f9fa;
        }

        .activity-log {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .activity-log h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }

        .activity-log ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-log li {
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #555;
        }

        .activity-log li:last-child {
            border-bottom: none;
        }

        .activity-log .date {
            color: #6c5ce7;
            font-weight: 500;
        }

        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip .tooltiptext {
            visibility: hidden;
            width: 100px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 4px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -50px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
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
                padding: 10px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .function-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .header {
                padding: 1rem;
            }

            .header h1 {
                font-size: 22px;
            }

            .header p {
                font-size: 13px;
            }

            .stat-card {
                padding: 12px;
            }

            .stat-card i {
                font-size: 24px;
            }

            .stat-card h3 {
                font-size: 13px;
            }

            .stat-card p {
                font-size: 20px;
            }

            .function-card {
                padding: 12px;
            }

            .function-card i {
                font-size: 1.8rem;
            }

            .function-card h2 {
                font-size: 1.1rem;
            }

            .function-card p {
                font-size: 0.85rem;
            }

            .university-btn {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }

            .schedule-preview h2, .activity-log h2 {
                font-size: 16px;
            }

            .schedule-table {
                display: block;
                overflow-x: auto;
            }

            .schedule-table th, .schedule-table td {
                font-size: 13px;
                padding: 8px;
                min-width: 100px;
            }

            .activity-log li {
                font-size: 12px;
                flex-direction: column;
                gap: 4px;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 20px;
            }

            .header p {
                font-size: 12px;
            }

            .stat-card p {
                font-size: 18px;
            }

            .function-card {
                min-width: 100%;
            }

            .tooltip .tooltiptext {
                width: 80px;
                margin-left: -40px;
                font-size: 12px;
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

            <!-- Header -->
            <header class="header">
                <div class="container">
                    <h1>Bienvenue, Pr. <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h1>
                    <p>Espace Enseignant - Université XYZ</p>
                </div>
            </header>

            <!-- Main Content -->
            <div class="container">
                <!-- Quick Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="bi bi-book"></i>
                        <h3>Spécialités Enseignées</h3>
                        <p><?php echo htmlspecialchars($total_specialites); ?></p>
                    </div>
                    <div class="stat-card">
                        <i class="bi bi-calendar-event"></i>
                        <h3>Séances à Venir</h3>
                        <p><?php echo htmlspecialchars($total_sessions); ?></p>
                    </div>
                    <div class="stat-card">
                        <i class="bi bi-file-earmark-text"></i>
                        <h3>Examens à Venir</h3>
                        <p><?php echo htmlspecialchars($total_exams); ?></p>
                    </div>
                </div>

                <!-- Function Grid -->
                <div class="function-grid">
                    <!-- Session Planning -->
                    <div class="function-card">
                        <i class="bi bi-calendar-plus"></i>
                        <h2>Planification de Séances</h2>
                        <p>Créez et organisez vos séances pour vos spécialités.</p>
                        <div class="tooltip">
                            <a href="ajouter_seance.php" class="university-btn">
                                <i class="bi bi-plus"></i> Nouvelle Séance
                            </a>
                            <span class="tooltiptext">Ajouter une nouvelle séance</span>
                        </div>
                    </div>

                    <!-- Exam Management -->
                    <div class="function-card">
                        <i class="bi bi-file-earmark-text"></i>
                        <h2>Gestion des Examens</h2>
                        <p>Déposez et gérez les sujets d'examen pour vos spécialités.</p>
                        <div class="tooltip">
                            <a href="deposer_examen.php" class="university-btn">
                                <i class="bi bi-upload"></i> Gérer Examens
                            </a>
                            <span class="tooltiptext">Gérer les examens</span>
                        </div>
                    </div>

                    <!-- Grade Management -->
                    <div class="function-card">
                        <i class="bi bi-clipboard-check"></i>
                        <h2>Gestion des Notes</h2>
                        <p>Attribuez et consultez les notes des étudiants.</p>
                        <div class="tooltip">
                            <a href="gestion_notes.php" class="university-btn">
                                <i class="bi bi-pencil"></i> Gérer Notes
                            </a>
                            <span class="tooltiptext">Gérer les notes</span>
                        </div>
                    </div>

                    <!-- Profile -->
                    <div class="function-card">
                        <i class="bi bi-person-circle"></i>
                        <h2>Profil Enseignant</h2>
                        <p>Modifiez vos informations personnelles et paramètres.</p>
                        <div class="tooltip">
                            <a href="modifier_profil.php" class="university-btn">
                                <i class="bi bi-gear"></i> Modifier Profil
                            </a>
                            <span class="tooltiptext">Modifier votre profil</span>
                        </div>
                    </div>
                </div>

                <!-- Schedule Preview -->
                <div class="schedule-preview">
                    <h2>Séances à Venir</h2>
                    <?php if (empty($sessions)): ?>
                        <p style="text-align: center; color: #666;">Aucune séance prévue.</p>
                    <?php else: ?>
                        <table class="schedule-table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Date</th>
                                    <th>Durée</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sessions as $session): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($session['titre']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($session['date_seance'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($session['duree'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Recent Activity -->
                <div class="activity-log">
                    <h2>Activités Récentes</h2>
                    <?php if (empty($activities)): ?>
                        <p style="text-align: center; color: #666;">Aucune activité récente.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($activities as $activity): ?>
                                <li>
                                    <span><?php echo htmlspecialchars($activity['description']); ?></span>
                                    <span class="date"><?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
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