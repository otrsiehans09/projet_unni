<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'etudiant') {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
include('connexion.php');

// Récupération des données étudiant
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);
if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $etudiant_id = $row['id'];
    $etudiant_nom = $row['nom'];
    $etudiant_prenom = $row['prenom'];
    $photo_profil = $row['photo_profil'] ?? 'default.jpg';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Étudiant</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include('index.css'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.2);
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
        }

        .welcome-message h1 {
            margin: 0;
            font-size: 1.8rem;
        }

        .welcome-message p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .stat-card i {
            font-size: 2rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }

        .stat-card h3 {
            margin: 0 0 0.5rem;
            color: var(--dark);
        }

        .stat-card p {
            margin: 0;
            color: #6c757d;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
            margin: 0.5rem 0;
        }

        .section-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--dark);
            margin-bottom: 1.5rem;
        }

        .section-title i {
            color: var(--accent);
            font-size: 1.5rem;
        }

        .course-item, .note-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }

        .course-item:hover, .note-item:hover {
            background: #f8f9fa;
        }

        .course-info, .note-info {
            flex: 1;
        }

        .course-name, .note-course {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .course-schedule, .note-value {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .note-value {
            font-weight: bold;
            color: var(--primary);
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: var(--primary);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }

        .action-btn:hover {
            background: var(--secondary);
            color: white;
        }

        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 50rem;
        }

        .badge-primary {
            color: #fff;
            background-color: var(--primary);
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include('sidenav.php'); ?>

        <div class="main-panel">
            <?php include('navtop.php'); ?>

            <div class="content">
                <div class="container-fluid">
                    <!-- En-tête du dashboard -->
                    <div class="dashboard-header">
                        <img src="\uploads\images (10).jpg<?php echo $photo_profil; ?>" alt="Photo de profil" class="profile-avatar">
                        <div class="welcome-message">
                            <h1>Bonjour, <?php echo $etudiant_prenom; ?> !</h1>
                            <p>Bienvenue sur votre espace étudiant</p>
                        </div>
                    </div>

                    <!-- Statistiques rapides -->
                    <div class="dashboard-grid">
                        <?php
                        // Récupération des stats
                        $sql_courses_count = "SELECT COUNT(*) as total FROM inscriptions WHERE id_etudiant = $etudiant_id";
                        $sql_avg_notes = "SELECT AVG(note) as moyenne FROM notes WHERE id_etudiant = $etudiant_id";
                        $sql_pending_questions = "SELECT COUNT(*) as total FROM questions WHERE id_etudiant = $etudiant_id AND statut = 'En attente'";
                        
                        $courses_count = $conn->query($sql_courses_count)->fetch_assoc()['total'];
                        $avg_notes = $conn->query($sql_avg_notes)->fetch_assoc()['moyenne'];
                        $pending_questions = $conn->query($sql_pending_questions)->fetch_assoc()['total'];
                        ?>
                        
                        <div class="stat-card">
                            <i class="fas fa-book-open"></i>
                            <h3>Cours suivis</h3>
                            <div class="stat-value"><?php echo $courses_count; ?></div>
                            <p>Cette année académique</p>
                        </div>

                        <div class="stat-card">
                            <i class="fas fa-star"></i>
                            <h3>Moyenne générale</h3>
                            <div class="stat-value"><?php echo $avg_notes ? number_format($avg_notes, 2) : '--'; ?></div>
                            <p>/20</p>
                        </div>

                        <div class="stat-card">
                            <i class="fas fa-question-circle"></i>
                            <h3>Questions en attente</h3>
                            <div class="stat-value"><?php echo $pending_questions; ?></div>
                            <p>Réponses attendues</p>
                        </div>
                    </div>

                    <!-- Cours suivis -->
                    <div class="section-card">
                        <div class="section-title">
                            <i class="fas fa-book"></i>
                            <h2>Mes Cours</h2>
                        </div>
                        
                        <?php
                        $sql_courses = "SELECT cours.id, cours.titre, cours.description 
                                        FROM cours 
                                        JOIN inscriptions ON cours.id = inscriptions.id_cours 
                                        WHERE inscriptions.id_etudiant = $etudiant_id
                                        LIMIT 5";
                        $result_courses = $conn->query($sql_courses);
                        
                        if ($result_courses->num_rows > 0) {
                            while ($course = $result_courses->fetch_assoc()) {
                                echo '<div class="course-item">
                                        <div class="course-info">
                                            <div class="course-name">'.$course['titre'].'</div>
                                            <div class="course-schedule">'.substr($course['description'], 0, 60).'...</div>
                                        </div>
                                        <a href="cours_details.php?id='.$course['id'].'" class="action-btn">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>';
                            }
                            echo '<div style="text-align: center; margin-top: 1rem;">
                                    <a href="mes_cours.php" class="action-btn">
                                        <i class="fas fa-list"></i> Voir tous mes cours
                                    </a>
                                </div>';
                        } else {
                            echo '<p>Aucun cours inscrit pour le moment.</p>';
                        }
                        ?>
                    </div>

                    <!-- Dernières notes -->
                    <div class="section-card">
                        <div class="section-title">
                            <i class="fas fa-chart-line"></i>
                            <h2>Mes Notes Récentes</h2>
                        </div>
                        
                        <?php
                        $sql_notes = "SELECT cours.titre, notes.note, notes.date_attribution 
                                      FROM notes 
                                      JOIN cours ON notes.id_specialite = specialite.id 
                                      WHERE notes.id_etudiant = $etudiant_id
                                      ORDER BY notes.date_notation DESC
                                      LIMIT 5";
                        $result_notes = $conn->query($sql_notes);
                        
                        if ($result_notes->num_rows > 0) {
                            while ($note = $result_notes->fetch_assoc()) {
                                $note_class = ($note['note'] >= 10) ? 'badge-primary' : '';
                                echo '<div class="note-item">
                                        <div class="note-info">
                                            <div class="note-course">'.$note['titre'].'</div>
                                            <div class="course-schedule">'.date('d/m/Y', strtotime($note['date_attribution'])).'</div>

                                        </div>
                                        <span class="note-value badge '.$note_class.'">'.$note['note'].'/20</span>
                                    </div>';
                            }
                            echo '<div style="text-align: center; margin-top: 1rem;">
                                    <a href="mes_notes.php" class="action-btn">
                                        <i class="fas fa-list"></i> Voir toutes mes notes
                                    </a>
                                </div>';
                        } else {
                            echo '<p>Aucune note disponible pour le moment.</p>';
                        }
                        ?>
                    </div>

                    <!-- Emploi du temps -->
                    <div class="section-card">
                        <div class="section-title">
                            <i class="fas fa-calendar-alt"></i>
                            <h2>Mon Emploi du Temps</h2>
                        </div>
                        
                        <div style="text-align: center;">
                            <a href="emploi_du_temps.php" class="action-btn" style="display: inline-flex;">
                                <i class="fas fa-calendar-week"></i> Consulter mon EDT complet
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('footer.php'); ?>
        </div>
    </div>

    <?php include('index.js'); ?>
</body>
</html>