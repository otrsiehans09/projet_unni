<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('connexion.php'); // Connexion à la base de données

if (isset($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if (isset($_POST['email']) && isset($_POST['mot_de_passe'])) {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM accounts WHERE email='$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($mot_de_passe, $row['mot_de_passe'])) {
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: dashboard.php");
                exit();
            } elseif ($row['role'] === 'enseignant') {
                header("Location: dashboard_enseignant.php");
                exit();
            } elseif ($row['role'] === 'etudiant') {
                header("Location: accueil.php");
                exit();
            } else {
                $message = "Rôle inconnu.";
            }
        } else {
            $message = "Mot de passe incorrect.";
        }
    } else {
        $message = "Utilisateur non trouvé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #444;
        }

        label {
            float: left;
            margin: 10px 0 5px;
            font-weight: 600;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #2575fc;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #1a5edb;
        }

        .message {
            color: red;
            font-weight: bold;
            margin-top: 15px;
        }

        .link {
            margin-top: 20px;
            display: block;
        }

        a {
            color: #2575fc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <div class="login-container">
        <h2>Connexion</h2>

        <?php if (!empty($message)) : ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="email">Email :</label>
            <input type="email" name="email" id="email" required>

            <label for="mot_de_passe">Mot de passe :</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" required>

            <input type="submit" value="Se connecter">
        </form>

        
    </div>

</body>
</html>
