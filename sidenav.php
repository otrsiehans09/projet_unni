<?php
include('connexion.php');
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']); 
$is_departement_page = strpos($current_page, 'département') !== false;
$is_specialite_page = strpos($current_page, 'spécialité') !== false;
$is_classe_page = strpos($current_page, 'classe') !== false;
$is_etudiant_page = strpos($current_page, 'étudiant') !== false;
$is_role_page = strpos($current_page, 'role') !== false;
$is_enseignant_page = strpos($current_page, 'enseignant') !== false;
$is_matiere_page = strpos($current_page, 'matière') !== false;
$is_actualite_page = strpos($current_page, 'actualité') !== false;
$is_parametre_page = strpos($current_page, 'paramètre') !== false;
$is_liste_etudiants_page = $current_page === 'liste_etudiants.php';
$is_rapport_page = strpos($current_page, 'rapport') !== false;

$email = $_SESSION['email'];
$sql_fetch_user = "SELECT * FROM accounts WHERE email='$email'";
$result_user = $conn->query($sql_fetch_user);

if ($result_user->num_rows > 0) {
    $row = $result_user->fetch_assoc();
    $role = $row['role'];
    $user_email = $row['email'];
    $nom = $row['nom'];
    $prenom = $row['prenom'];
}
?>

<div class="sidebar" data-color="blue">
    <style>
        /* Styles modernes intégrés */
        .sidebar {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            position: fixed;
            width: 250px;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-wrapper {
            padding: 0 10px;
        }

        .logo {
            padding: 15px 0;
            margin-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .logo a {
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            text-decoration: none;
        }

        .nav {
            list-style: none;
            padding: 0;
            margin-top: 15px;
        }

        .nav li {
            margin-bottom: 4px;
            position: relative;
        }

        .nav li a {
            color: rgba(255, 255, 255, 0.8);
            margin: 5px 10px;
            border-radius: 6px;
            padding: 10px 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .nav li a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(3px);
        }

        .nav li a i {
            margin-right: 10px;
            font-size: 1.1rem;
            min-width: 24px;
            text-align: center;
        }

        .nav li a p {
            margin: 0;
            font-size: 0.85rem;
            font-weight: 400;
        }

        .nav li.active a {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        [class^="pe-7s-"], [class*=" pe-7s-"] {
            transition: transform 0.3s ease;
        }

        .nav li a:hover i {
            transform: scale(1.1);
        }

        /* Tooltip for collapsed state */
        .nav li a .tooltiptext {
            visibility: hidden;
            width: 120px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            top: 50%;
            left: 100%;
            margin-left: 10px;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s;
            font-size: 0.8rem;
        }

        .nav li a:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }

            .logo a {
                font-size: 0;
            }

            .logo a:before {
                content: "☰";
                font-size: 1.4rem;
                color: white;
            }

            .nav li a p {
                display: none;
            }

            .nav li a {
                justify-content: center;
                padding: 12px !important;
            }

            .nav li a i {
                margin-right: 0;
                font-size: 1.3rem;
            }

            .nav li a .tooltiptext {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 250px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .logo a {
                font-size: 1rem;
            }

            .logo a:before {
                content: none;
            }

            .nav li a p {
                display: block;
            }

            .nav li a {
                justify-content: flex-start;
                padding: 10px 12px !important;
            }

            .nav li a i {
                margin-right: 10px;
                font-size: 1.1rem;
            }

            .nav li a .tooltiptext {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 200px;
            }

            .logo a {
                font-size: 0.9rem;
            }

            .nav li a {
                padding: 8px 10px !important;
            }

            .nav li a i {
                font-size: 1rem;
            }

            .nav li a p {
                font-size: 0.8rem;
            }
        }
    </style>

    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="#" class="simple-text">
                <?php echo htmlspecialchars($nom . ' ' . $prenom); ?>
            </a>
        </div>

        <ul class="nav">
            <?php if ($role === 'admin'): ?>
                <li <?php echo ($current_page == 'dashboard.php') ? 'class="active"' : ''; ?>>
                    <a href="dashboard.php">
                        <i class="pe-7s-home"></i>
                        <p>Accueil</p>
                        <span class="tooltiptext">Accueil</span>
                    </a>
                </li>
                <li <?php echo $is_departement_page ? 'class="active"' : ''; ?>>
                    <a href="départements.php">
                        <i class="pe-7s-wallet"></i>
                        <p>Départements</p>
                        <span class="tooltiptext">Départements</span>
                    </a>
                </li>
                <li <?php echo $is_specialite_page ? 'class="active"' : ''; ?>>
                    <a href="spécialités.php">
                        <i class="pe-7s-credit"></i>
                        <p>Spécialités</p>
                        <span class="tooltiptext">Spécialités</span>
                    </a>
                </li>
                <li <?php echo $is_classe_page ? 'class="active"' : ''; ?>>
                    <a href="classes.php">
                        <i class="pe-7s-diskette"></i>
                        <p>Classes</p>
                        <span class="tooltiptext">Classes</span>
                    </a>
                </li>
                <li <?php echo $is_etudiant_page ? 'class="active"' : ''; ?>>
                    <a href="étudiants.php">
                        <i class="pe-7s-user"></i>
                        <p>Etudiants</p>
                        <span class="tooltiptext">Etudiants</span>
                    </a>
                </li>
                <li <?php echo $is_enseignant_page ? 'class="active"' : ''; ?>>
                    <a href="enseignants.php">
                        <i class="pe-7s-users"></i>
                        <p>Enseignants</p>
                        <span class="tooltiptext">Enseignants</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'gestion_emploi.php') ? 'class="active"' : ''; ?>>
                    <a href="gestion_emploi.php">
                        <i class="pe-7s-date"></i>
                        <p>Emploi du Temps</p>
                        <span class="tooltiptext">Emploi du Temps</span>
                    </a>
                </li>
                <li <?php echo $is_actualite_page ? 'class="active"' : ''; ?>>
                    <a href="actualités.php">
                        <i class="pe-7s-note2"></i>
                        <p>Actualités</p>
                        <span class="tooltiptext">Actualités</span>
                    </a>
                </li>
                <li <?php echo $is_role_page ? 'class="active"' : ''; ?>>
                    <a href="roles.php">
                        <i class="pe-7s-lock"></i>
                        <p>Gestion de rôles</p>
                        <span class="tooltiptext">Gestion de rôles</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'modifier_mot_de_passe.php') ? 'class="active"' : ''; ?>>
                    <a href="modifier_mot_de_passe.php">
                        <i class="pe-7s-id"></i>
                        <p>Modifier Mot de passe</p>
                        <span class="tooltiptext">Modifier Mot de passe</span>
                    </a>
                </li>
                <li <?php echo $is_parametre_page || $is_liste_etudiants_page ? 'class="active"' : ''; ?>>
                    <a href="paramètres.php">
                        <i class="pe-7s-tools"></i>
                        <p>Paramètres</p>
                        <span class="tooltiptext">Paramètres</span>
                    </a>
                </li>
                
            <?php elseif ($role === 'enseignant'): ?>
                <li <?php echo ($current_page == 'dashboard_enseignant.php') ? 'class="active"' : ''; ?>>
                    <a href="dashboard_enseignant.php">
                        <i class="pe-7s-home"></i>
                        <p>Accueil Enseignant</p>
                        <span class="tooltiptext">Accueil Enseignant</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'ajouter_seance.php') ? 'class="active"' : ''; ?>>
                    <a href="ajouter_seance.php">
                        <i class="pe-7s-plus"></i>
                        <p>Créer une Séance de Cours</p>
                        <span class="tooltiptext">Créer une Séance</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'gestion_notes.php') ? 'class="active"' : ''; ?>>
                    <a href="gestion_notes.php">
                        <i class="pe-7s-note2"></i>
                        <p>Gérer les Notes</p>
                        <span class="tooltiptext">Gérer les Notes</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'deposer_examen.php') ? 'class="active"' : ''; ?>>
                    <a href="deposer_examen.php">
                        <i class="pe-7s-upload"></i>
                        <p>Déposer un Examen</p>
                        <span class="tooltiptext">Déposer un Examen</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'profil_enseignant.php') ? 'class="active"' : ''; ?>>
                    <a href="profil_enseignant.php">
                        <i class="pe-7s-user"></i>
                        <p>Mettre à Jour le Profil</p>
                        <span class="tooltiptext">Mettre à Jour le Profil</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'modifier_mot_de_passe.php') ? 'class="active"' : ''; ?>>
                    <a href="modifier_mot_de_passe.php">
                        <i class="pe-7s-id"></i>
                        <p>Modifier Mot de passe</p>
                        <span class="tooltiptext">Modifier Mot de passe</span>
                    </a>
                </li>

            <?php elseif ($role === 'etudiant'): ?>
                <li <?php echo ($current_page == 'dashboard_etudiant.php') ? 'class="active"' : ''; ?>>
                    <a href="dashboard_etudiant.php">
                        <i class="pe-7s-home"></i>
                        <p>Tableau de bord</p>
                        <span class="tooltiptext">Tableau de bord</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'cours.php') ? 'class="active"' : ''; ?>>
                    <a href="cours.php">
                        <i class="pe-7s-copy-file"></i>
                        <p>Liste des cours</p>
                        <span class="tooltiptext">Liste des cours</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'notes.php') ? 'class="active"' : ''; ?>>
                    <a href="notes.php">
                        <i class="pe-7s-note2"></i>
                        <p>Mes notes</p>
                        <span class="tooltiptext">Mes notes</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'questions.php') ? 'class="active"' : ''; ?>>
                    <a href="questions.php">
                        <i class="pe-7s-chat"></i>
                        <p>Mes questions</p>
                        <span class="tooltiptext">Mes questions</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'accueil.php') ? 'class="active"' : ''; ?>>
                    <a href="accueil.php">
                        <i class="pe-7s-browser"></i>
                        <p>Accueil</p>
                        <span class="tooltiptext">Accueil</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'rapport.php') ? 'class="active"' : ''; ?>>
                    <a href="rapport.php">
                        <i class="pe-7s-file"></i>
                        <p>Rapport & Devoirs</p>
                        <span class="tooltiptext">Rapport & Devoirs</span>
                    </a>
                </li>
                <li <?php echo ($current_page == 'modifier_mot_de_passe.php') ? 'class="active"' : ''; ?>>
                    <a href="modifier_mot_de_passe.php">
                        <i class="pe-7s-lock"></i>
                        <p>Modifier Mot de passe</p>
                        <span class="tooltiptext">Modifier Mot de passe</span>
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="logout.php">
                    <i class="pe-7s-settings"></i>
                    <p>Déconnexion</p>
                    <span class="tooltiptext">Déconnexion</span>
                    </a>
            </li>
        </ul>
    </div>
</div>

<script>
    // Toggle sidebar on mobile
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const toggleButton = document.querySelector('.navbar-toggle');

        if (toggleButton) {
            toggleButton.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768 && !sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
    });
</script>