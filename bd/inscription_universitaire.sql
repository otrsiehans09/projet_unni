-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 14 août 2024 à 16:14
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `inscription_universitaire`
--

-- --------------------------------------------------------

--
-- Structure de la table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11)  AUTO_INCREMENT PRIMARY KEY,
  `cin` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `date_de_creation` date DEFAULT NULL,
  `role` text DEFAULT NULL,
  `nom` text DEFAULT NULL,
  `prenom` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `classe` text DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `numero_telephone` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `accounts`
--

INSERT INTO `accounts` (`id`, `cin`, `email`, `mot_de_passe`, `date_de_creation`, `role`, `nom`, `prenom`, `image`, `classe`, `date_naissance`, `adresse`, `numero_telephone`) VALUES
(7, 23456789, 'fatma.khemiri@gmail.com', '$2y$10$hHrO7QJKl2J7gk.xI7/SiObS.QKBltnvLCnupbvsxFmURaTCLdOSq', '2024-08-09', 'etudiant', 'Khemiri', 'Fatma', 'uploads/health.webp', 'ASRI2', '1997-09-22', '5 Avenue Habib Thameur, Sfax', 21474836),
(13, 34567890, 'mehdi.dridi@gmail.com', NULL, '2024-08-10', 'etudiant', 'Dridi', 'Mehdi', 'uploads/images (1).jfif', 'GL2', '1999-02-10', '15 Rue Farhat Hached, Sousse', 22154236),
(15, 45678901, 'amina.benali@gmail.com', NULL, '2024-08-10', 'etudiant', 'Ben Ali', 'Amina', 'uploads/images (2).jfif', 'GL3', '1996-11-18', '8 Avenue Habib Bourguiba, Bizerte', 23522144),
(16, 56789012, 'youssef.benamor@gmail.com', '$2y$10$Qs7JC9z6MAnwlqSqd1aKGeZo3KYcYOFgCAXFbvwuw1exA1g0bAIWG', '2024-08-10', 'etudiant', 'Ben Amor', 'Youssef ', 'uploads/images (3).jfif', 'AN', '1998-07-20', '12 Rue Mohamed V, Monastir', 24125634),
(21, 123456789, 'admin@gmail.com', '$2y$10$O7O3ijGRSEA1q8hMMT61dOwFuu04sHGD4JThQb6LEyxXbBi6SwH92', '2024-08-12', 'admin', 'Amdouni', 'Nesrine', NULL, NULL, '1998-12-22', 'Tunis', 22222222),
(22, 67890123, 'sarra.gharbi@gmail.com', '$2y$10$Fi8UnBIjj5jALo5ZC9cg7eeirE06zgKgEDj8Oc03naBcvV022zXX2', '2024-08-12', 'etudiant', 'Gharbi', 'Sarra ', 'uploads/images (1).png', 'GD2', '1997-04-18', '3 Rue Habib Thameur, Mahdia', 25142365),
(24, 78901234, 'hatem.mejri@gmail.com', '$2y$10$sPuggi3/y9UTlU.PYAoCROi3ciYzcpJry2sMdIInh4sJK.CFeZlSO', '2024-08-13', 'etudiant', 'Mejri', 'Hatem', 'uploads/images (4).jfif', 'GD2', '1997-12-25', '20 Avenue Farhat Hached, Gabès', 26478521),
(25, 89012345, 'nour.benmansour@gmail.com', '$2y$10$fa4NFDLdRD2lhw8NKABvb.MWO/rlYfOjcqjJgSmsPdwahgIEliBjO', '2024-08-14', 'etudiant', 'Ben Mansour', 'Nour', 'uploads/images (5).jfif', 'ASRI1', '1999-01-30', '6 Rue Habib Bourguiba, Kairouan', 27474836),
(26, 12345678, 'ali.byoussef@gmail.com', '$2y$10$ZgJ7DsJHZlkWfLhln8Z/Ve5iC7OsncaTkcskZXngWs3kYL1.NjfgO', '2024-08-14', 'etudiant', 'Ben Youssef', 'Ali', 'uploads/securite-sociale-etudiant.jpg', 'ASRI1', '1998-05-15', '10 Rue Habib Bourguiba, Tunis', 58474875),
(27, 90123456, 'wael.chaabane@gmail.com', '$2y$10$2t.tlReb9eQ/t18XiOongO2E41hgtKrLyzlIwCCZIV6WZdNG5niui', '2024-08-14', 'etudiant', 'Chaabane', 'Wael', 'uploads/images (6).jfif', 'GM1', '1996-08-12', '18 Avenue Habib Thameur, Nabeul', 55425478),
(28, 24512635, 'benjemaa.safa@gmail.com', '$2y$10$9OVXSW4KLLWtSnsuBw2jcOXCvdMk7TSlwwZ5HtJc.B4OBHLTuw4zW', '2024-08-14', 'etudiant', 'Ben jemaa', 'Safa', 'uploads/téléchargement (6).jfif', 'GD2', '1997-01-01', '15 Rue Farhat Hached, Sousse', 24851277),
(29, 45215685, 'mejri.mohammed@gmail.com', '$2y$10$5FWq8GSu2GsjHE6.5ch16.AQLMepZ9a85.jbbvWdc/POfDKHNSV3G', '2024-08-14', 'etudiant', 'Mejri', 'Mohammed', 'uploads/images (9).jfif', 'ASRI1', '2000-02-02', '5 Avenue Habib Thameur, Sfax', 26352155);

-- --------------------------------------------------------

--
-- Structure de la table `actualites`
--

CREATE TABLE `actualites` (
  `id` int(11) NOT NULL,
  `nom` text DEFAULT NULL,
  `date_de_creation` date DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `actualites`
--

INSERT INTO `actualites` (`id`, `nom`, `date_de_creation`, `description`) VALUES
(1, 'Lorem ipsum', '2024-08-06', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec lacus pharetra, finibus eros vel, molestie tortor. Vestibulum porttitor enim sapien, at vulputate turpis ornare vitae. Cras euismod faucibus mi nec fermentum. Sed tempor ut tellus non accumsan. Phasellus et neque in nisl blandit porttitor id at augue. Mauris nisi ligula, lobortis ut mi in, lobortis aliquam augue. Sed quis neque ac orci pretium consectetur.'),
(3, 'Lorem ipsum', '2024-08-06', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec lacus pharetra, finibus eros vel, molestie tortor. Vestibulum porttitor enim sapien, at vulputate turpis ornare vitae. Cras euismod faucibus mi nec fermentum. Sed tempor ut tellus non accumsan. Phasellus et neque in nisl blandit porttitor id at augue. Mauris nisi ligula, lobortis ut mi in, lobortis aliquam augue. Sed quis neque ac orci pretium consectetur.'),
(14, 'Lorem ipsum', '2024-08-06', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec lacus pharetra, finibus eros vel, molestie tortor. Vestibulum porttitor enim sapien, at vulputate turpis ornare vitae. Cras euismod faucibus mi nec fermentum. Sed tempor ut tellus non accumsan. Phasellus et neque in nisl blandit porttitor id at augue. Mauris nisi ligula, lobortis ut mi in, lobortis aliquam augue. Sed quis neque ac orci pretium consectetur.'),
(15, 'Lorem ipsum', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec lacus pharetra, finibus eros vel, molestie tortor. Vestibulum porttitor enim sapien, at vulputate turpis ornare vitae. Cras euismod faucibus mi nec fermentum. Sed tempor ut tellus non accumsan. Phasellus et neque in nisl blandit porttitor id at augue. Mauris nisi ligula, lobortis ut mi in, lobortis aliquam augue. Sed quis neque ac orci pretium consectetur.'),
(17, 'Lorem ipsum', '2024-08-13', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec lacus pharetra, finibus eros vel, molestie tortor. Vestibulum porttitor enim sapien, at vulputate turpis ornare vitae. Cras euismod faucibus mi nec fermentum. Sed tempor ut tellus non accumsan. Phasellus et neque in nisl blandit porttitor id at augue. Mauris nisi ligula, lobortis ut mi in, lobortis aliquam augue. Sed quis neque ac orci pretium consectetur.\r\n');

-- --------------------------------------------------------

--
-- Structure de la table `classe`
--

CREATE TABLE `classe` (
  `id` int(11) NOT NULL,
  `nom` text DEFAULT NULL,
  `niveau` text DEFAULT NULL,
  `specialite` text DEFAULT NULL,
  `departement` text DEFAULT NULL,
  `date_de_creation` date DEFAULT NULL,
  `nombre_etudiants` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `classe`
--

INSERT INTO `classe` (`id`, `nom`, `niveau`, `specialite`, `departement`, `date_de_creation`, `nombre_etudiants`) VALUES
(1, 'ASRI1', '1 ère', 'Administration et Sécurité des Réseaux Informatiques', 'Département Informatique', '2024-08-06', 30),
(2, 'ASRI2', '2 ème', 'Administration et Sécurité des Réseaux Informatiques', 'Département Informatique', '2024-08-06', 31),
(6, 'GL1', '1 ère', 'Génie Logiciel et Système d', 'Département Informatique', '2024-08-13', 28),
(7, 'GL2', '2 ème', 'Génie Logiciel et Système d', 'Département Informatique', '2024-08-14', 38),
(8, 'GL3', '3 ème', 'Génie Logiciel et Système d', 'Département Informatique', '2024-08-14', 35),
(9, 'AN', '1 ère', 'Analyse Numérique', 'Département mathématique', '2024-08-14', 36),
(10, 'GD2', '3 ème', 'Géométrie Différentielle', 'Département mathématique', '2024-08-14', 25),
(11, 'GM1', '2 ème', 'Génie Mécanique', 'Département technique', '2024-08-14', 26);

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `titre` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,  -- Description du cours
    `enseignant_id` INT(11) NOT NULL,  -- L'ID de l'enseignant responsable du cours
    `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Date de création du cours
    FOREIGN KEY (`enseignant_id`) REFERENCES `accounts`(`id`)  -- Relier à la table `accounts` pour l'enseignant
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `titre`, `cours`, `date_creation`) VALUES
(5, 'Programmation orientée objet', 'pdf/livretJava.pdf', '2024-08-14'),
(6, 'Administration et Sécurité des Réseaux Informatiques', 'pdf/ADMINISTRATION DES RESEAUX INFORMATIQUES By Prof. YENDE R..pdf', '2024-08-14'),
(7, 'Génie Logiciel', 'pdf/COURS DE GENIE LOGICIEL By Pof. YENDE R..pdf', '2024-08-14'),
(8, 'Multimédia', 'pdf/multimedia-slides.pdf', '2024-08-14'),
(9, 'Analyse Numérique', 'pdf/polyAnaNum.pdf', '2024-08-14'),
(10, 'Géométrie Différentielle', 'pdf/GeoDiff2022.pdf', '2024-08-14'),
(11, 'Génie Mécanique	', 'pdf/génie mécanique.pdf', '2024-08-14'),
(12, 'Génie Électrique et Électronique', 'pdf/Prospectus GBM filière nationale  2015.pdf', '2024-08-14'),
(13, 'Génie Environnemental', 'pdf/Génie Environnemental.pdf', '2024-08-14'),
(14, 'Chimie Organique', 'pdf/cour-chimie-organique-S2-2017-2018.pdf', '2024-08-14'),
(15, 'Chimie Inorganique', 'pdf/Chimie-inorganique.pdf', '2024-08-14'),
(16, 'Chimie Physique', 'pdf/9782804166519.pdf', '2024-08-14');

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

CREATE TABLE `departement` (
  `id` int(11) NOT NULL,
  `nom_departement` text DEFAULT NULL,
  `date_de_creation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`id`, `nom_departement`, `date_de_creation`) VALUES
(16, 'Département technique', '2024-08-06'),
(17, 'Département Informatique', '2024-08-06'),
(19, 'Département mathématique', '2024-08-06'),
(20, 'Département physique', '2024-08-09'),
(22, 'Département chimie', '2024-08-13');

-- --------------------------------------------------------

--
-- Structure de la table `enseignant`
--

CREATE TABLE `enseignant` (
  `id` int(11) NOT NULL,
  `nom` text DEFAULT NULL,
  `prenom` text DEFAULT NULL,
  `cin` int(11) DEFAULT NULL,
  `date_de_naissance` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `departement` text DEFAULT NULL,
  `date_de_creation` date DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `numero_telephone` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `enseignant`
--

INSERT INTO `enseignant` (`id`, `nom`, `prenom`, `cin`, `date_de_naissance`, `image`, `departement`, `date_de_creation`, `adresse`, `numero_telephone`, `email`) VALUES
(1, 'Ben Youssef', 'Dr. Amira ', 12345678, '1975-08-12', 'uploads/téléchargement (2).jfif', 'Département technique', '2024-08-07', '10 Rue Habib Bourguiba, Tunis', 22671358, 'amira.byoussef@example.com'),
(6, 'Dridi', 'Dr. Hela', 34567890, '1980-11-03', 'uploads/téléchargement (3).jfif', 'Département Informatique', '2024-08-10', '15 Rue Farhat Hached, Sousse', 23452222, 'hela.dridi@gmail.com'),
(7, 'Ben Ali', 'Dr. Mohamed ', 45678901, '1973-06-17', 'uploads/téléchargement (4).jfif', 'Département chimie', '2024-08-13', '8 Avenue Habib Bourguiba, Bizerte', 27456128, 'mohamed.benali@gmail.com'),
(8, 'Ben Amor', 'Dr. Leila', 56789012, '1979-09-30', 'uploads/téléchargement (5).jfif', 'Département mathématique', '2024-08-13', '12 Rue Mohamed V, Monastir', 25429763, 'leila.benamor@example.com'),
(9, 'Mejri', 'Dr. Salma', 78901234, '1972-01-10', 'uploads/images (7).jfif', 'Département physique', '2024-08-13', '20 Avenue Farhat Hached, Gabès', 21474836, 'salma.mejri@gmail.com'),
(10, 'Ben Jemaa', 'Dr. Mohamed ', 1234567, '1969-05-20', 'uploads/images (8).jfif', 'Département technique', '2024-08-13', '1 Rue Farhat Hached, Gafsa', 24716852, 'mohamed.benjemaa@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `matière`
--

CREATE TABLE `matière` (
  `id` int(11) NOT NULL,
  `libelle` text DEFAULT NULL,
  `coefficient` int(11) DEFAULT NULL,
  `specialite` text DEFAULT NULL,
  `date_de_creation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `matière`
--

INSERT INTO `matière` (`id`, `libelle`, `coefficient`, `specialite`, `date_de_creation`) VALUES
(3, 'Algorithmique', 5, 'Génie Logiciel et Système d', '2024-08-13'),
(4, 'Programmation orientée objet', 3, 'Administration et Sécurité des Réseaux Informatiques', '2024-08-13'),
(5, 'Sécurité', 4, 'Administration et Sécurité des Réseaux Informatiques', '2024-08-14'),
(6, 'Réseaux ', 2, 'Administration et Sécurité des Réseaux Informatiques', '2024-08-14'),
(7, 'Génie Logiciel', 4, 'Génie Logiciel et Système d', '2024-08-14'),
(8, 'Intelligence artificielle', 2, 'Informatique et Multimédia : IM', '2024-08-14');

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

CREATE TABLE `parametres` (
  `id` int(11) NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `date_de_creation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `parametres`
--

INSERT INTO `parametres` (`id`, `date_debut`, `date_fin`, `date_de_creation`) VALUES
(1, '2024-04-01', '2024-07-01', '2024-08-07');

-- --------------------------------------------------------

--
-- Structure de la table `rapport`
--

CREATE TABLE `rapport` (
  `id` int(11) NOT NULL,
  `type` text DEFAULT NULL,
  `rapport` varchar(255) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `nom` text DEFAULT NULL,
  `prenom` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `numero_telephone` int(11) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `cin` int(11) DEFAULT NULL,
  `classe` varchar(255) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Déchargement des données de la table `rapport`
--

INSERT INTO `rapport` (`id`, `type`, `rapport`, `date_creation`, `nom`, `prenom`, `email`, `numero_telephone`, `adresse`, `cin`, `classe`, `date_naissance`) VALUES
(12, 'Stage de projet de fin d\'études', 'pdf/1fccfb08999382699b9a91ae317b3800d293726d.pdf', '2024-08-14', 'Ben jemaa', 'Safa', 'benjemaa.safa@gmail.com', 24851277, '15 Rue Farhat Hached, Sousse', 24512635, 'GD2', '1997-01-01'),
(13, 'Stage d\'été', 'pdf/Exemple-de-plan-de-rapport-de-stage-1.pdf', '2024-08-14', 'Mejri', 'Mohammed', 'mejri.mohammed@gmail.com', 26352155, '5 Avenue Habib Thameur, Sfax', 45215685, 'ASRI1', '2000-02-02');

-- --------------------------------------------------------

--
-- Structure de la table `specialite`
--

CREATE TABLE `specialite` (
  `id` int(11) NOT NULL,
  `nom_specialite` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `departement` text DEFAULT NULL,
  `date_de_creation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

--
-- Structure de la table `inscriptions aux cours`
--

CREATE TABLE `inscriptions` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `id_etudiant` INT(11) NOT NULL,
    `id_cours` INT(11) NOT NULL,
    `date_inscription` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_etudiant`) REFERENCES `accounts`(`id`),
    FOREIGN KEY (`id_cours`) REFERENCES `cours`(`id`)
);

--
-- Structure de la table `inscriptions aux cours`
--

CREATE TABLE `examens` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `titre` VARCHAR(255),
    `description` TEXT,
    `date_exam` DATETIME,
    `enseignant_id` INT(11) NOT NULL,
    FOREIGN KEY (`enseignant_id`) REFERENCES `accounts`(`id`)
);



--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `id_etudiant` INT(11) NOT NULL,
    `id_cours` INT(11) NOT NULL,
    `contenu` TEXT NOT NULL,
    `date_commentaire` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_etudiant`) REFERENCES `accounts`(`id`),
    FOREIGN KEY (`id_cours`) REFERENCES `cours`(`id`)
);


--
-- Structure de la table `exercices`
--

CREATE TABLE `exercices` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `id_etudiant` INT(11) NOT NULL,
    `id_cours` INT(11) NOT NULL,
    `fichier` VARCHAR(255) NOT NULL,
    `date_envoi` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_etudiant`) REFERENCES `accounts`(`id`),
    FOREIGN KEY (`id_cours`) REFERENCES `cours`(`id`)
);

--
-- Structure de la table `modifs_en_attentes   pour les demandes de changements dans le profil`
--

CREATE TABLE `modifs_en_attente` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `id_etudiant` INT(11) NOT NULL,
    `champ_modifié` VARCHAR(255) NOT NULL,
    `nouvelle_valeur` TEXT NOT NULL,
    `statut` ENUM('en attente', 'accepté', 'refusé') DEFAULT 'en attente',
    `date_demande` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_etudiant`) REFERENCES `accounts`(`id`)
);



--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `id_etudiant` INT(11) NOT NULL,
    `id_cours` INT(11) NOT NULL,
    `note` DECIMAL(5,2),
    `commentaire` TEXT,
    `date_attribution` DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Correction ici
    FOREIGN KEY (`id_etudiant`) REFERENCES `accounts`(`id`),
    FOREIGN KEY (`id_cours`) REFERENCES `cours`(`id`)
);


--
-- Structure de la table `questions`
--

CREATE TABLE `questions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `id_etudiant` INT NOT NULL,
    `id_cours` INT NOT NULL,
    `question` TEXT NOT NULL,
    `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `statut` ENUM('en attente', 'répondu') DEFAULT 'en attente',
    `date_respondue` DATETIME DEFAULT NULL,  -- Nouveau champ pour la date de réponse
    FOREIGN KEY (`id_etudiant`) REFERENCES `accounts`(`id`),
    FOREIGN KEY (`id_cours`) REFERENCES `cours`(`id`)
);




--
-- Déchargement des données de la table `specialite`
--

INSERT INTO `specialite` (`id`, `nom_specialite`, `description`, `departement`, `date_de_creation`) VALUES
(5, 'Administration et Sécurité des Réseaux Informatiques', 'Cette spécialité combine des compétences en administration des systèmes informatiques avec une expertise approfondie en sécurité des réseaux pour garantir le bon fonctionnement et la protection des communications et des données au sein d\'une entreprise.', 'Département Informatique', '2024-08-06'),
(6, 'Génie Logiciel et Système d\'Information : GL', 'Le génie logiciel et les systèmes d\'information, souvent abrégés en GL, sont des domaines interconnectés qui se concentrent sur la conception, le développement, la mise en œuvre et la gestion de logiciels et de systèmes informatiques efficaces et fiables.', 'Département Informatique', '2024-08-06'),
(8, 'Informatique et Multimédia : IM', 'Cette spécialité combine des aspects de l\'informatique, tels que la programmation, les algorithmes, les systèmes d\'information, avec les domaines du multimédia, tels que la conception graphique, la création de contenu numérique, l\'audiovisuel, et la commu', 'Département Informatique', '2024-08-13'),
(9, 'Analyse Numérique', 'Cette spécialité se concentre sur les méthodes numériques pour résoudre des problèmes mathématiques complexes, tels que l\'approximation de fonctions, la résolution d\'équations différentielles, et la simulation de phénomènes physiques.', 'Département mathématique', '2024-08-14'),
(10, 'Géométrie Différentielle', 'Cette spécialité étudie les courbes, les surfaces et les variétés en utilisant des outils de calcul différentiel et intégral pour comprendre la géométrie dans des espaces de dimension supérieure.', 'Département mathématique', '2024-08-14'),
(11, 'Génie Mécanique', 'Cette spécialité se concentre sur la conception, l\'analyse et la fabrication de systèmes mécaniques, machines et véhicules.', 'Département technique', '2024-08-14'),
(12, 'Génie Électrique et Électronique', 'Cette spécialité englobe la conception, la fabrication et la maintenance des systèmes électriques, électroniques et de contrôle.', 'Département technique', '2024-08-14'),
(13, 'Génie Biomédical', 'Cette spécialité intègre les principes du génie dans le domaine de la médecine pour concevoir et développer des dispositifs médicaux, des équipements de diagnostic et des traitements médicaux.', 'Département chimie', '2024-08-14'),
(14, 'Génie Environnemental', 'Cette spécialité vise à concevoir des solutions durables pour protéger l\'environnement, gérer les ressources naturelles et réduire les impacts environnementaux des activités humaines.\r\n', 'Département physique', '2024-08-14'),
(15, 'Chimie Organique', 'Cette spécialité se concentre sur l\'étude des composés contenant du carbone, leur structure, leurs réactions et leurs applications dans divers domaines comme la pharmacie, la biotechnologie et la chimie des matériaux.', 'Département chimie', '2024-08-14'),
(16, 'Chimie Inorganique', 'Cette spécialité concerne l\'étude des composés inorganiques, tels que les métaux, les sels et les minéraux, ainsi que leur réactivité et leurs applications industrielles.', 'Département chimie', '2024-08-14'),
(17, 'Chimie Physique', 'Cette spécialité combine les principes de la physique et de la chimie pour étudier les propriétés physiques et les réactions chimiques des composés, en mettant l\'accent sur la thermodynamique, la cinétique et la spectroscopie.\r\n', 'Département physique', '2024-08-14');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `actualites`
--
ALTER TABLE `actualites`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `classe`
--
ALTER TABLE `classe`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `departement`
--
ALTER TABLE `departement`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `enseignant`
--
ALTER TABLE `enseignant`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `matière`
--
ALTER TABLE `matière`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `parametres`
--
ALTER TABLE `parametres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rapport`
--
ALTER TABLE `rapport`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `specialite`
--
ALTER TABLE `specialite`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `actualites`
--
ALTER TABLE `actualites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `classe`
--
ALTER TABLE `classe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `departement`
--
ALTER TABLE `departement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT pour la table `enseignant`
--
ALTER TABLE `enseignant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `matière`
--
ALTER TABLE `matière`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `parametres`
--
ALTER TABLE `parametres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `rapport`
--
ALTER TABLE `rapport`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `specialite`
--
ALTER TABLE `specialite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
