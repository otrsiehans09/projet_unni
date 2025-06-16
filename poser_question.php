<?php
session_start();
include('connexion.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$message = ""; // Message de validation ou d'erreur

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_specialite = $_POST['id_specialite']; // ID de la spécialité sélectionnée
    $question = trim($_POST['question']);

    if (empty($question)) {
        $message = "<div class='error-message'>La question ne peut pas être vide.</div>";
    } else {
        // Récupération de l'ID de l'étudiant via l'e-mail (sécurité)
        $stmt_user = $conn->prepare("SELECT id FROM accounts WHERE email = ?");
        $stmt_user->bind_param("s", $email);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($result_user->num_rows > 0) {
            $user = $result_user->fetch_assoc();
            $id_etudiant = $user['id'];

            $stmt = $conn->prepare("INSERT INTO questions (id_etudiant, id_specialite, question) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $id_etudiant, $id_specialite, $question);

            if ($stmt->execute()) {
                $message = "<div class='success-message'>Votre question a été posée avec succès.</div>";
            } else {
                $message = "<div class='error-message'>Erreur lors de l'enregistrement de la question.</div>";
            }
        } else {
            $message = "<div class='error-message'>Utilisateur introuvable.</div>";
        }
    }
}

// Récupérer les spécialités
$sql_specialites = "SELECT * FROM specialite";
$result_specialites = $conn->query($sql_specialites);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poser une Question</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .container {
            width: 70%;
            margin: 50px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        label {
            font-weight: 500;
            margin-bottom: 10px;
            display: block;
        }

        select, textarea, button {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        select, textarea {
            font-family: 'Poppins', sans-serif;
        }

        button {
            background-color: #6c5ce7;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #5a4dc6;
        }

        .success-message, .error-message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
            text-align: center;
        }

        .success-message {
            background-color: #27ae60;
            color: white;
        }

        .error-message {
            background-color: #e74c3c;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
<?php
if (!empty($message)) {
    echo $message;
}
?>

    <h1>Poser une nouvelle question</h1>

    <form method="post" action="">

        <label for="id_specialite">Sélectionner la spécialité :</label>
        <select name="id_specialite" id="id_specialite" required>
            <option value="">Choisir une spécialité</option>
            <?php
            // Récupérer les spécialités disponibles
            $sql_specialites = "SELECT * FROM specialite";
            $result_specialites = $conn->query($sql_specialites);
            // Afficher les spécialités disponibles
            while ($specialite = $result_specialites->fetch_assoc()) {
                echo "<option value='".$specialite['id']."'>".$specialite['nom_specialite']."</option>";
            }
            ?>
        </select>

        <label for="question">Votre question :</label>
        <textarea name="question" rows="5" required></textarea>

        <button type="submit">Poser la question</button>
    </form>
</div>

</body>
</html>
