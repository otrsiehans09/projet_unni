<?php
header('Content-Type: application/json');
// Tu pourras ici brancher sur une vraie BDD plus tard
echo json_encode([
    "labels" => ["Math", "Info", "Chimie", "Physique", "Anglais"],
    "values" => [85, 90, 70, 65, 78]
]);
?>
