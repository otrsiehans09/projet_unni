<?php
// dashboard.php

session_start();
require_once 'connexion.php';

// Redirect if user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT role FROM accounts WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();
$stmt->close();

if (!$user || $user['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}

// Fetch counts
// Students
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM accounts WHERE role = 'etudiant'");
$stmt->execute();
$total_etudiants = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Teachers
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM enseignant");
$stmt->execute();
$total_enseignants = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Departments
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM departement");
$stmt->execute();
$total_departements = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Specialties
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM specialite");
$stmt->execute();
$total_specialites = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Classes
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM classe");
$stmt->execute();
$total_classes = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Role distribution for pie chart
$stmt = $conn->prepare("SELECT role, COUNT(*) as count FROM accounts GROUP BY role");
$stmt->execute();
$result_roles = $stmt->get_result();
$role_counts = ['admin' => 0, 'etudiant' => 0, 'enseignant' => 0];
while ($row = $result_roles->fetch_assoc()) {
    $role_counts[$row['role']] = $row['count'];
}
$stmt->close();



$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Admin - Université XYZ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            max-width: 1200px;
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

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card i {
            font-size: 30px;
            color: #6c5ce7;
            margin-bottom: 10px;
        }

        .card h3 {
            font-size: 18px;
            color: #333;
            margin: 10px 0;
        }

        .card p {
            font-size: 24px;
            font-weight: 600;
            color: #6c5ce7;
            margin: 0;
        }

        .charts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }

        .chart-container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .chart-container canvas {
            max-height: 300px;
        }

        .quick-links {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 40px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 12px 20px;
            background-color: #6c5ce7;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: rgb(24, 0, 238);
        }

        .btn i {
            margin-right: 8px;
        }

        .activity-log {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .activity-log h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
        }

        .activity-log ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-log li {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #555;
        }

        .activity-log li:last-child {
            border-bottom: none;
        }

        .activity-log .date {
            color: #6c5ce7;
            font-weight: 500;
        }

        @media screen and (max-width: 768px) {
            .main-panel {
                margin-left: 0;
                width: 100%;
            }

            .sidebar {
                display: none;
            }

            .container {
                margin: 20px;
                padding: 20px;
            }

            .charts {
                grid-template-columns: 1fr;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .quick-links {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                justify-content: center;
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
                <h1>Tableau de Bord Admin</h1>

                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="card">
                        <i class="bi bi-person"></i>
                        <h3>Étudiants</h3>
                        <p><?php echo htmlspecialchars($total_etudiants); ?></p>
                    </div>
                    <div class="card">
                        <i class="bi bi-chalkboard"></i>
                        <h3>Enseignants</h3>
                        <p><?php echo htmlspecialchars($total_enseignants); ?></p>
                    </div>
                    <div class="card">
                        <i class="bi bi-building"></i>
                        <h3>Départements</h3>
                        <p><?php echo htmlspecialchars($total_departements); ?></p>
                    </div>
                    <div class="card">
                        <i class="bi bi-book"></i>
                        <h3>Spécialités</h3>
                        <p><?php echo htmlspecialchars($total_specialites); ?></p>
                    </div>
                    <div class="card">
                        <i class="bi bi-mortarboard"></i>
                        <h3>Classes</h3>
                        <p><?php echo htmlspecialchars($total_classes); ?></p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts">
                    <!-- Bar Chart -->
                    <div class="chart-container">
                        <h2 style="text-align: center; font-size: 18px; margin-bottom: 20px;">Statistiques Générales</h2>
                        <canvas id="barChart"></canvas>
                    </div>
                    <!-- Pie Chart -->
                    <div class="chart-container">
                        <h2 style="text-align: center; font-size: 18px; margin-bottom: 20px;">Répartition des Rôles</h2>
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="quick-links">
                    <a href="étudiants.php" class="btn"><i class="bi bi-person"></i> Gérer Étudiants</a>
                    <a href="enseignants.php" class="btn"><i class="bi bi-chalkboard"></i> Gérer Enseignants</a>
                    <a href="départements.php" class="btn"><i class="bi bi-building"></i> Gérer Départements</a>
                    <a href="spécialités.php" class="btn"><i class="bi bi-book"></i> Gérer Spécialités</a>
                    <a href="classes.php" class="btn"><i class="bi bi-mortarboard"></i> Gérer Classes</a>
                    <a href="roles.php" class="btn"><i class="bi bi-gear"></i> Gérer Utilisateurs</a>
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

    <script>
        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Étudiants', 'Enseignants', 'Départements', 'Spécialités', 'Classes'],
                datasets: [{
                    label: 'Nombre',
                    data: [<?php echo $total_etudiants; ?>, <?php echo $total_enseignants; ?>, <?php echo $total_departements; ?>, <?php echo $total_specialites; ?>, <?php echo $total_classes; ?>],
                    backgroundColor: 'rgba(108, 92, 231, 0.2)',
                    borderColor: '#6c5ce7',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Admins', 'Étudiants', 'Enseignants'],
                datasets: [{
                    data: [<?php echo $role_counts['admin']; ?>, <?php echo $role_counts['etudiant']; ?>, <?php echo $role_counts['enseignant']; ?>],
                    backgroundColor: ['#6c5ce7', '#00cec9', '#f39c12'],
                    borderColor: ['#fff', '#fff', '#fff'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 14 } }
                    }
                }
            }
        });
    </script>
    <?php include('index.js'); ?>
</body>
</html>