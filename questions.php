<?php
// questions.php

require_once 'connexion.php'; // Use require_once for critical dependencies
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user details
$sql_fetch_user = "SELECT id, role FROM accounts WHERE email = ?";
$stmt = $conn->prepare($sql_fetch_user);
$stmt->bind_param("s", $email);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();
$stmt->close();

// Restrict access to students only
if ($user['role'] !== 'etudiant') {
    echo "Accès interdit.";
    exit();
}

// Fetch student's questions
$sql = "SELECT id, question, statut, date_creation FROM questions WHERE id_etudiant = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Questions</title>
    <?php include('index.css'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        /* Sidebar */
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

        .sidebar .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar .logo a {
            font-size: 20px;
            color: white;
            font-weight: bold;
            text-decoration: none;
        }

        .sidebar .nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar .nav li a {
            display: block;
            color: white;
            padding: 15px 20px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .sidebar .nav li a:hover {
            background-color: #1abc9c;
        }

        .sidebar .nav .active a {
            background-color: #34495e;
        }

        /* Main Panel */
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .btn {
            display: block;
            width: 200px;
            margin: 30px auto;
            padding: 10px;
            text-align: center;
            background-color: #6c5ce7;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: rgb(24, 0, 238);
        }

        .btn-delete {
            display: inline-block;
            padding: 6px 12px;
            background-color: #e74c3c;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 500;
        }

        .status.en-attente {
            background-color: #f39c12;
            color: white;
        }

        .status.repondu {
            background-color: #27ae60;
            color: white;
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
                <h1>Mes Questions</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['question']); ?></td>
                                <td>
                                    <span class="status <?php echo $row['statut'] === 'en attente' ? 'en-attente' : 'repondu'; ?>">
                                        <?php echo htmlspecialchars($row['statut']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['date_creation']); ?></td>
                                <td>
                                    <a href="supprimer_question.php?id_question=<?php echo $row['id']; ?>" 
                                       class="btn-delete" 
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette question ?');">
                                        🗑 Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="poser_question.php" class="btn">Poser une nouvelle question</a>
            </div>
        </div>
    </div>

    <?php include('index.js'); ?>
</body>
</html>

<?php
// Clean up
$result->free();
$conn->close();
?>