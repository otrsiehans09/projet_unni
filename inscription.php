<?php
include('connexion.php');

session_start();

if (isset($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $sql_check_email = "SELECT * FROM accounts WHERE email='$email'";
    $result_check = $conn->query($sql_check_email);

    if ($result_check->num_rows > 0) {
        $message = "L'email est déjà utilisé. Veuillez en choisir un autre.";
    } else {
        $sql = "INSERT INTO accounts (nom, prenom, cin, email, mot_de_passe, role, date_de_creation) VALUES ('$nom', '$prenom', '$cin', '$email', '$mot_de_passe', '$role', NOW())";
        if ($conn->query($sql) === TRUE) {
            $message = "Inscription réussie!";
        } else {
            $message = "Erreur : " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Inscription</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .form-container {
            width: 100%;
            max-width: 500px;
            margin: 80px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h3.title {
            text-align: center;
            margin-bottom: 30px;
            color: #444;
        }

        label {
            display: block;
            font-weight: 600;
            margin-top: 15px;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 5px;
            box-sizing: border-box;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-success {
            background-color: #2575fc;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-success:hover {
            background-color: #1a5edb;
        }

        .link {
            text-align: center;
            margin-top: 20px;
        }

        .link a {
            color: #2575fc;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .form-check input {
            margin-right: 10px;
        }
    </style>
</head>

<body>

    <?php if (!empty($message)) : ?>
        <script>
            Swal.fire({
                title: 'Message',
                text: '<?php echo $message; ?>',
                icon: '<?php echo ($message === "Inscription réussie!") ? "success" : "warning"; ?>',
                confirmButtonText: 'OK'
            }).then(() => {
                <?php if ($message === "Inscription réussie!") : ?>
                window.location.href = 'login.php';
                <?php endif; ?>
            });
        </script>
    <?php endif; ?>

    <div class="form-container">
        <h3 class="title">Créer un compte</h3>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

            <div class="form-group">
                <label for="cin">CIN :</label>
                <input type="number" name="cin" id="cin" required>
            </div>

            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" name="prenom" id="prenom" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" required>
            </div>

            <div class="form-group">
                <label for="role">Rôle :</label>
                <select name="role" id="role" required>
                    <option value="etudiant">Étudiant</option>
                    <option value="admin">Administrateur</option>
                    <option value="enseignant">Enseignant</option>
                </select>
            </div>

            <div class="form-check">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">J'accepte les termes et conditions</label>
            </div>

            <br>
            <button type="submit" class="btn-success">Inscription</button>

            <div class="link">
                <p><a href="login.php">Vous avez déjà un compte ?</a></p>
            </div>
        </form>
    </div>

</body>
</html>
