<?php
// gestion_classes.php

session_start();
require_once 'connexion.php'; // Use require_once for critical dependencies

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

if (!$user) {
    header("Location: login.php");
    exit();
}

// Restrict access to admins
if ($user['role'] !== 'admin') {
    header("Location: unauthorized.php");
    exit();
}

// Fetch classes
$classes = [];
$stmt = $conn->prepare("SELECT id, nom, niveau, departement, specialite, nombre_etudiants, date_de_creation FROM classe ORDER BY id ASC");
$stmt->execute();
$result_classes = $stmt->get_result();
while ($class = $result_classes->fetch_assoc()) {
    $classes[] = $class;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Classes - Université XYZ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .search-bar {
            width: 300px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
            transition: border-color 0.3s ease;
        }

        .search-bar:focus {
            outline: none;
            border-color: #6c5ce7;
            background-color: #fff;
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

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #6c5ce7;
            color: white;
            font-weight: 600;
        }

        .table tr:hover {
            background-color: #f1f1f1;
        }

        .action-btn {
            padding: 10px;
            border-radius: 6px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
        }

        .action-btn.edit {
            background-color: #f39c12;
            margin-right: 10px;
        }

        .action-btn.edit:hover {
            background-color: #e67e22;
        }

        .action-btn.delete {
            background-color: #e74c3c;
        }

        .action-btn.delete:hover {
            background-color: #c0392b;
        }

        .no-results {
            text-align: center;
            color: #666;
            margin-top: 20px;
            font-size: 16px;
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

            .controls {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-bar {
                width: 100%;
            }

            .table th, .table td {
                font-size: 14px;
                padding: 10px;
            }

            .action-btn {
                width: 32px;
                height: 32px;
                font-size: 14px;
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
                <h1>Gestion des Classes</h1>
                <div class="controls">
                    <a href="ajouter_classe.php" class="btn"><i class="bi bi-plus"></i> Ajouter une classe</a>
                    <input type="text" id="searchBar" class="search-bar" placeholder="Rechercher une classe...">
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Classe</th>
                            <th>Niveau</th>
                            <th>Département</th>
                            <th>Spécialité</th>
                            <th>Nombre d'étudiants</th>
                            <th>Date de création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="classTable">
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($class['nom']); ?></td>
                                <td><?php echo htmlspecialchars($class['niveau']); ?></td>
                                <td><?php echo htmlspecialchars($class['departement']); ?></td>
                                <td><?php echo htmlspecialchars($class['specialite']); ?></td>
                                <td><?php echo htmlspecialchars($class['nombre_etudiants']); ?></td>
                                <td><?php echo htmlspecialchars($class['date_de_creation']); ?></td>
                                <td>
                                    <a href="modifier_classe.php?id=<?php echo $class['id']; ?>" 
                                       class="action-btn edit"><i class="bi bi-pencil"></i></a>
                                    <button type="button" class="action-btn delete" 
                                            onclick="confirmDelete(<?php echo $class['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (empty($classes)): ?>
                    <p class="no-results">Aucune classe trouvée.</p>
                <?php endif; ?>
            </div>

            <!-- Footer -->
            <footer class="university-footer">
                <?php include 'footer.php'; ?>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include('index.js'); ?>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Cette action supprimera définitivement cette classe !',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#6c5ce7',
                cancelButtonColor: '#e74c3c',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'supprimer_classe.php?id=' + id;
                }
            });
        }

        // Client-side search functionality
        document.getElementById('searchBar').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#classTable tr');
            let hasResults = false;

            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const niveau = row.cells[1].textContent.toLowerCase();
                const departement = row.cells[2].textContent.toLowerCase();
                const specialite = row.cells[3].textContent.toLowerCase();
                if (name.includes(searchTerm) || niveau.includes(searchTerm) || 
                    departement.includes(searchTerm) || specialite.includes(searchTerm)) {
                    row.style.display = '';
                    hasResults = true;
                } else {
                    row.style.display = 'none';
                }
            });

            const noResults = document.querySelector('.no-results');
            if (noResults) {
                noResults.style.display = hasResults ? 'none' : 'block';
            }
        });
    </script>
</body>
</html>