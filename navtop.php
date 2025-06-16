<?php
// navtop.php

include('connexion.php');
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

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

<style>
    /* Styles spécifiques pour la navbar */
    .navbar-default {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        min-height: 60px;
        margin-bottom: 0;
    }

    .navbar-default .navbar-nav > li > a {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        padding: 20px 12px;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        align-items: center;
    }

    .navbar-default .navbar-nav > li > a:hover {
        color: white;
        background: rgba(255, 255, 255, 0.1);
    }

    .navbar-default .navbar-nav > li > a:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 3px;
        background: white;
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }

    .navbar-default .navbar-nav > li > a:hover:after {
        width: 80%;
    }

    .navbar-default .navbar-toggle {
        border: none;
        margin-top: 15px;
        padding: 10px;
    }

    .navbar-default .navbar-toggle .icon-bar {
        background-color: white;
        width: 24px;
        height: 3px;
        margin: 4px 0;
        transition: all 0.2s;
    }

    .navbar-default .navbar-toggle:hover .icon-bar {
        background-color: #f8f8f8;
    }

    .navbar-default .navbar-brand {
        height: 60px;
        padding: 20px 12px;
        color: white;
        font-weight: 600;
        font-size: 18px;
        display: flex;
        align-items: center;
    }

    .navbar-default .navbar-brand i {
        margin-right: 8px;
        font-size: 22px;
    }

    .user-name {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .user-name i {
        font-size: 16px;
    }

    .menuu {
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .menuu p {
        margin: 0;
    }

    .navbar-collapse {
        background: rgba(106, 17, 203, 0.98);
        max-height: 80vh;
        overflow-y: auto;
    }

    @media (max-width: 768px) {
        .navbar-default {
            min-height: 50px;
        }

        .navbar-default .navbar-nav > li > a {
            padding: 12px 15px;
            font-size: 16px;
        }

        .navbar-default .navbar-nav > li > a:after {
            display: none; /* Remove underline effect on mobile */
        }

        .navbar-collapse {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            margin-top: 0;
        }

        .navbar-default .navbar-brand {
            font-size: 16px;
            padding: 15px 10px;
            height: 50px;
        }

        .navbar-default .navbar-brand i {
            font-size: 20px;
        }

        .navbar-toggle {
            margin-top: 10px;
        }

        .menuu {
            font-size: 16px;
        }

        .user-name i {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .navbar-default .navbar-brand {
            font-size: 14px;
            padding: 12px 8px;
        }

        .navbar-default .navbar-brand i {
            font-size: 18px;
        }

        .navbar-default .navbar-nav > li > a {
            padding: 10px 12px;
            font-size: 14px;
        }

        .menuu {
            font-size: 14px;
        }

        .user-name i {
            font-size: 12px;
        }

        .navbar-toggle {
            padding: 8px;
        }

        .navbar-toggle .icon-bar {
            width: 20px;
            height: 2px;
        }
    }
</style>

<nav class="navbar navbar-default navbar-fixed">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navigation-example-2" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">
                <i class="pe-7s-study"></i> <?php echo $role === 'etudiant' ? 'Espace Étudiant' : ($role === 'enseignant' ? 'Espace Enseignant' : 'Administration'); ?>
            </a>
        </div>
        <div class="collapse navbar-collapse" id="navigation-example-2">
            <ul class="nav navbar-nav navbar-right">
                <!-- Lien vers le Profil -->
                <li>
                    <a class="menuu" href="<?php echo $role === 'etudiant' ? 'profil_etudiant.php' : ($role === 'enseignant' ? 'profil_enseignant.php' : 'profil.php'); ?>">
                        <i class="pe-7s-user hidden-md hidden-lg"></i>
                        <p>Mon Profil</p>
                    </a>
                </li>

                <!-- Affichage du nom de l'utilisateur -->
                <li>
                    <a class="menuu" href="#">
                        <div class="user-name">
                            <i class="pe-7s-id hidden-md hidden-lg"></i>
                            <p><?php echo htmlspecialchars($prenom . ' ' . $nom); ?></p>
                        </div>
                    </a>
                </li>
                
                <!-- Lien de déconnexion -->
                <li>
                    <a class="menuu" href="logout.php">
                        <i class="pe-7s-power hidden-md hidden-lg"></i>
                        <p>Déconnexion</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>