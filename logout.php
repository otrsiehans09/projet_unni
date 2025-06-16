<?php
// logout.php

// Démarrez la session (si ce n'est pas déjà fait)
session_start();

// Détruire toutes les données de session
session_unset();
session_destroy();

// Rediriger vers la page de connexion
header("Location: login.php");
exit();
