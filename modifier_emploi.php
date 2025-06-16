<?php
include('connexion.php');
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID non fourni.";
    exit();
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM emplois_du_temps WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Aucune séance trouvée.";
    exit();
}

$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jour = $_POST['jour'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $matiere = $_POST['matiere'];
    $salle = $_POST['salle'];

    $update = "UPDATE emplois_du_temps SET 
               jour = '$jour',
               heure_debut = '$heure_debut',
               heure_fin = '$heure_fin',
               matiere = '$matiere',
               salle = '$salle'
               WHERE id = $id";

    if ($conn->query($update) === TRUE) {
        header("Location: gestion_emploi.php");
        exit();
    } else {
        echo "Erreur lors de la mise à jour : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Modifier la Séance</title>
    <style>
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="time"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-size: 16px;
            outline: none;
        }

        input[type="text"]::placeholder,
        input[type="time"]::placeholder {
            color: #ccc;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 10px;
            background-color: #00c9a7;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background-color: #00b39f;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Modifier la Séance</h2>
        <form method="POST">
            <label for="jour">Jour :</label>
            <input type="text" name="jour" value="<?php echo htmlspecialchars($row['jour']); ?>" required>

            <label for="heure_debut">Heure Début :</label>
            <input type="time" name="heure_debut" value="<?php echo $row['heure_debut']; ?>" required>

            <label for="heure_fin">Heure Fin :</label>
            <input type="time" name="heure_fin" value="<?php echo $row['heure_fin']; ?>" required>

            <label for="matiere">Matière :</label>
            <input type="text" name="matiere" value="<?php echo htmlspecialchars($row['matiere']); ?>" required>

            <label for="salle">Salle :</label>
            <input type="text" name="salle" value="<?php echo htmlspecialchars($row['salle']); ?>" required>

            <button type="submit">💾 Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>
