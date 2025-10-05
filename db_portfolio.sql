-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 05 oct. 2025 à 15:11
-- Version du serveur : 8.0.31
-- Version de PHP : 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_portfolio`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles_blog`
--

DROP TABLE IF EXISTS `articles_blog`;
CREATE TABLE IF NOT EXISTS `articles_blog` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `extrait` longtext COLLATE utf8mb4_general_ci,
  `contenu` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `image_principale` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `statut` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `date_publication` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `nombre_vues` int NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9AE99604989D9B62` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles_blog`
--

INSERT INTO `articles_blog` (`id`, `titre`, `slug`, `extrait`, `contenu`, `image_principale`, `statut`, `date_publication`, `nombre_vues`, `created_at`, `updated_at`) VALUES
(3, 'Développeur WEB | SYMFONY | WORDPRESS', 'd-veloppeur-web-symfony-wordpress', 'ggf', '<p>fjhjfjjf</p>', '/uploads/articles/68dfcbed5f9db.jpg', 'publié', NULL, 1, '2025-10-03 14:13:17', '2025-10-03 14:13:17'),
(4, 'L’importance de l’UX/UI dans un projet digital', 'l-importance-de-l-ux-ui-dans-un-projet-digital', 'shdsshghgss', '<p data-start=\"2321\" data-end=\"2699\">Un beau design attire, mais une <strong data-start=\"2616\" data-end=\"2657\">bonne exp&eacute;rience utilisateur fid&eacute;lise</strong>.<br data-start=\"2658\" data-end=\"2661\">Dans mes projets, je mets en avant :</p>\r\n<ul data-start=\"2700\" data-end=\"2873\">\r\n<li data-start=\"2700\" data-end=\"2739\">\r\n<p data-start=\"2702\" data-end=\"2739\">Une interface claire et accessible.</p>\r\n</li>\r\n<li data-start=\"2740\" data-end=\"2778\">\r\n<p data-start=\"2742\" data-end=\"2778\">Des parcours utilisateurs fluides.</p>\r\n</li>\r\n<li data-start=\"2779\" data-end=\"2873\">\r\n<p data-start=\"2781\" data-end=\"2873\">Des couleurs et typographies harmonis&eacute;es (ex. Montserrat, mes couleurs #11845a &amp; #FF6347).</p>\r\n</li>\r\n</ul>\r\n<p data-start=\"2875\" data-end=\"2984\">R&eacute;sultat : des interfaces modernes, agr&eacute;ables et faciles &agrave; utiliser, adapt&eacute;es aux besoins de chaque client.</p>', '/uploads/articles/68dfd0bb89c6f.jpg', 'publié', '2025-10-03 14:33:47', 0, '2025-10-03 14:33:47', '2025-10-03 14:33:47');

-- --------------------------------------------------------

--
-- Structure de la table `articles_blog_categories_blog`
--

DROP TABLE IF EXISTS `articles_blog_categories_blog`;
CREATE TABLE IF NOT EXISTS `articles_blog_categories_blog` (
  `articles_blog_id` int NOT NULL,
  `categories_blog_id` int NOT NULL,
  PRIMARY KEY (`articles_blog_id`,`categories_blog_id`),
  KEY `IDX_161963C89F851A36` (`articles_blog_id`),
  KEY `IDX_161963C8F2EAAF37` (`categories_blog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles_blog_categories_blog`
--

INSERT INTO `articles_blog_categories_blog` (`articles_blog_id`, `categories_blog_id`) VALUES
(3, 1),
(4, 1),
(5, 6),
(7, 7),
(7, 8),
(7, 9),
(11, 1),
(11, 2),
(13, 1);

-- --------------------------------------------------------

--
-- Structure de la table `articles_blog_tags_blog`
--

DROP TABLE IF EXISTS `articles_blog_tags_blog`;
CREATE TABLE IF NOT EXISTS `articles_blog_tags_blog` (
  `articles_blog_id` int NOT NULL,
  `tags_blog_id` int NOT NULL,
  PRIMARY KEY (`articles_blog_id`,`tags_blog_id`),
  KEY `IDX_E56DD0F89F851A36` (`articles_blog_id`),
  KEY `IDX_E56DD0F8FC5C9586` (`tags_blog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles_blog_tags_blog`
--

INSERT INTO `articles_blog_tags_blog` (`articles_blog_id`, `tags_blog_id`) VALUES
(5, 9),
(7, 10),
(7, 11),
(11, 13),
(11, 14),
(13, 15);

-- --------------------------------------------------------

--
-- Structure de la table `categories_blog`
--

DROP TABLE IF EXISTS `categories_blog`;
CREATE TABLE IF NOT EXISTS `categories_blog` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `couleur` varchar(7) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_creation` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_182609C5989D9B62` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories_blog`
--

INSERT INTO `categories_blog` (`id`, `nom`, `slug`, `description`, `couleur`, `date_creation`) VALUES
(1, 'Programmation', 'programmation', NULL, '#2ac680', '2025-10-03 10:39:05'),
(2, 'Technologie', 'technologie', NULL, '#2ac680', '2025-10-03 11:01:47'),
(3, 'Test Catégorie', 'test-cat-gorie', NULL, NULL, '2025-10-03 11:54:23'),
(4, 'Nouvelle Catégorie', 'nouvelle-cat-gorie', NULL, NULL, '2025-10-03 11:54:23'),
(5, 'Test Simple', 'test-simple', NULL, NULL, '2025-10-03 12:06:42'),
(6, 'Test Image', 'test-image', NULL, NULL, '2025-10-03 12:28:54'),
(7, 'Test', 'test', NULL, NULL, '2025-10-03 12:33:47'),
(8, 'Test2', 'test2', NULL, NULL, '2025-10-03 12:33:47'),
(9, 'Test3', 'test3', NULL, NULL, '2025-10-03 12:33:47');

-- --------------------------------------------------------

--
-- Structure de la table `competence`
--

DROP TABLE IF EXISTS `competence`;
CREATE TABLE IF NOT EXISTS `competence` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `competence`
--

INSERT INTO `competence` (`id`, `libelle`) VALUES
(1, 'PHP'),
(2, 'JavaScript'),
(3, 'React'),
(4, 'Symfony'),
(5, 'Node.js');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251002093958', '2025-10-02 10:40:19', 136),
('DoctrineMigrations\\Version20251002110258', '2025-10-02 12:03:22', 86),
('DoctrineMigrations\\Version20251002110305', NULL, NULL),
('DoctrineMigrations\\Version20251002110310', NULL, NULL),
('DoctrineMigrations\\Version20251002113605', NULL, NULL),
('DoctrineMigrations\\Version20251002113609', NULL, NULL),
('DoctrineMigrations\\Version20251003081843', NULL, NULL),
('DoctrineMigrations\\Version20251003090959', NULL, NULL),
('DoctrineMigrations\\Version20251003155054', NULL, NULL),
('DoctrineMigrations\\Version20251003155058', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `experiences_professionnelles`
--

DROP TABLE IF EXISTS `experiences_professionnelles`;
CREATE TABLE IF NOT EXISTS `experiences_professionnelles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `periode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `entreprise` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `poste` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ordre_affichage` int NOT NULL,
  `actif` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `experiences_professionnelles`
--

INSERT INTO `experiences_professionnelles` (`id`, `periode`, `entreprise`, `poste`, `ordre_affichage`, `actif`, `created_at`, `updated_at`, `type`, `description`) VALUES
(1, '2020-2023', 'Google Inc.', 'Développeur Full Stack', 0, 1, '2025-10-03 22:14:49', '2025-10-03 22:14:49', 'professionnelle', 'Développement d\'applications web modernes avec React et Node.js'),
(2, '2018-2020', 'Université de Paris', 'Master en Informatique', 0, 1, '2025-10-03 22:14:53', '2025-10-03 22:14:53', 'formation', 'Formation approfondie en développement logiciel et architecture des systèmes'),
(3, '2024-10-03-2025-09-30', 'SECS SARL', 'ASSISTANT INFORMATIQUE', 0, 1, '2025-10-03 22:40:02', '2025-10-03 22:40:02', 'professionnelle', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `messages_contact`
--

DROP TABLE IF EXISTS `messages_contact`;
CREATE TABLE IF NOT EXISTS `messages_contact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_expediteur` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_expediteur` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `sujet` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `statut` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parametres_site`
--

DROP TABLE IF EXISTS `parametres_site`;
CREATE TABLE IF NOT EXISTS `parametres_site` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cle` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `valeur` longtext COLLATE utf8mb4_general_ci,
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E0CADC4C41401D17` (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profil`
--

DROP TABLE IF EXISTS `profil`;
CREATE TABLE IF NOT EXISTS `profil` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `localisation` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `biographie` longtext COLLATE utf8mb4_general_ci,
  `avatar` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cv` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `liens_sociaux` json DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `prenom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `logo` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `profil`
--

INSERT INTO `profil` (`id`, `nom`, `email`, `telephone`, `localisation`, `biographie`, `avatar`, `cv`, `liens_sociaux`, `created_at`, `updated_at`, `prenom`, `logo`) VALUES
(1, 'ADJIBAKO', 'contact@armandadjibako.com', '+229 01 94 69 75 86 ', 'Cotonou, Bénin', 'Je conçois et développe des solutions web qui transforment les idées en expériences digitales fluides et efficaces.', '/uploads/avatars/68de55ab61590.png', '/uploads/documents/68dff30d2f32d.pdf', '{\"github\": \"https://github.com/Adjbk123\", \"twitter\": \"https://twitter.com/armand_dev\", \"facebook\": \"https://facebook.com/armand.adjibako\", \"linkedin\": \"https://linkedin.com/in/armand-adjibako\"}', '2025-10-02 10:33:09', '2025-10-03 17:09:32', 'Armand S.', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `projets`
--

DROP TABLE IF EXISTS `projets`;
CREATE TABLE IF NOT EXISTS `projets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `description_complete` longtext COLLATE utf8mb4_general_ci,
  `categorie` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_principale` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `galerie` json DEFAULT NULL,
  `technologies` json DEFAULT NULL,
  `fonctionnalites` json DEFAULT NULL,
  `duree` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lien_github` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lien_projet` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `statut` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `en_vedette` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projets`
--

INSERT INTO `projets` (`id`, `titre`, `description`, `description_complete`, `categorie`, `image_principale`, `galerie`, `technologies`, `fonctionnalites`, `duree`, `client`, `lien_github`, `lien_projet`, `statut`, `en_vedette`, `created_at`, `updated_at`) VALUES
(1, 'Bénin Signal', 'Plateforme citoyenne de signalement d’incidents en temps réel.', '<p>Application web et mobile permettant aux citoyens de signaler des incidents (s&eacute;curit&eacute;, environnement, voirie&hellip;) et de les transmettre automatiquement aux services comp&eacute;tents proches gr&acirc;ce &agrave; la g&eacute;olocalisation. Int&egrave;gre un tableau de bord pour les autorit&eacute;s avec attribution et suivi des signalements.</p>', 'web', '/uploads/images/68e16bfb2ec7c.jpg', '[\"/uploads/galeries/68e16bfb2f857.png\", \"/uploads/galeries/68e16bfb30900.png\", \"/uploads/galeries/68e16bfb311df.png\"]', '[\"Symfony API\", \"React Native\", \"MySQL\", \"JWT Auth\"]', '[\"Fonctionnalités\", \"Création et suivi des signalements\", \"Notifications temps réel par email\", \"Attribution automatique aux services compétents\", \"Tableau de bord citoyen et agent\"]', '6 mois', 'Projet personnel / Master', 'https://github.com/username/beninsignal', 'https://beninsignal.site', 'termine', 1, '2025-10-04 19:48:27', '2025-10-04 19:48:27');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

DROP TABLE IF EXISTS `services`;
CREATE TABLE IF NOT EXISTS `services` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_general_ci,
  `icone` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fonctionnalites` json DEFAULT NULL,
  `gamme_prix` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ordre_affichage` int NOT NULL,
  `actif` tinyint(1) NOT NULL,
  `date_creation` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tags_blog`
--

DROP TABLE IF EXISTS `tags_blog`;
CREATE TABLE IF NOT EXISTS `tags_blog` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `couleur` varchar(7) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_CFB2FD13989D9B62` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tags_blog`
--

INSERT INTO `tags_blog` (`id`, `nom`, `slug`, `couleur`, `created_at`) VALUES
(1, 'Php', 'php', '#008d4d', '2025-10-03 10:49:47'),
(2, 'React', 'react', '#008d4d', '2025-10-03 10:50:03'),
(3, 'Développement Mobile', 'd-veloppement-mobile', '#008d4d', '2025-10-03 10:50:19'),
(4, 'Développement WEb', 'd-veloppement-web', '#008d4d', '2025-10-03 10:50:34'),
(5, 'Consequatur optio veritatis odio at dolor', 'consequatur-optio-veritatis-odio-at-dolor', NULL, '2025-10-03 11:24:25'),
(6, 'Test Tag', 'test-tag', NULL, '2025-10-03 11:54:23'),
(7, 'Nouveau Tag', 'nouveau-tag', NULL, '2025-10-03 11:54:23'),
(8, 'Test Simple', 'test-simple', NULL, '2025-10-03 12:06:42'),
(9, 'Test Image', 'test-image', NULL, '2025-10-03 12:28:54'),
(10, 'fdfd', 'fdfd', NULL, '2025-10-03 12:33:47'),
(11, 'fgfg', 'fgfg', NULL, '2025-10-03 12:33:47'),
(12, 'test', 'test', NULL, '2025-10-03 12:34:08'),
(13, 'Ae', 'ae', NULL, '2025-10-03 12:45:16'),
(14, 'yyty', 'yyty', NULL, '2025-10-03 12:45:16'),
(15, 'vbvbv', 'vbvbv', NULL, '2025-10-03 13:22:05');

-- --------------------------------------------------------

--
-- Structure de la table `temoignages`
--

DROP TABLE IF EXISTS `temoignages`;
CREATE TABLE IF NOT EXISTS `temoignages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_client` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `poste_client` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `entreprise_client` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar_client` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contenu` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `note` int NOT NULL,
  `en_vedette` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `temoignages`
--

INSERT INTO `temoignages` (`id`, `nom_client`, `poste_client`, `entreprise_client`, `avatar_client`, `contenu`, `note`, `en_vedette`, `created_at`) VALUES
(1, '', NULL, NULL, NULL, '', 5, 1, '2025-10-04 23:31:09'),
(2, 'Doloribus eu qui est', 'Minima tempore quae', 'Aliqua Repudiandae ', '/uploads/avatars/68e1a1f731d3b.jpg', 'Ratione dolor dolor ', 3, 1, '2025-10-04 23:38:47'),
(3, 'Quis blanditiis cons', 'A accusamus delectus', 'Adipisicing mollit o', '/uploads/avatars/68e1a6ff4edc5.jpg', 'Officia ad ut volupt', 2, 1, '2025-10-05 00:00:15'),
(4, 'Nisi repudiandae ali', 'Natus sint in et qua', 'Accusantium anim eni', '/uploads/avatars/68e1a76d9232a.jpg', 'Nostrud asperiores e', 4, 1, '2025-10-05 00:02:05');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_general_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `nom`, `prenom`, `created_at`, `updated_at`) VALUES
(2, 'adjibako123@gmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$Bb0IjL.fN7UeoNa08HEFVe2rYd9/UycPIlHvKSSAxkrjSKbCYSynu', 'ADJIBAKO', 'Armand', '2025-10-05 01:22:07', '2025-10-05 02:23:12');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `articles_blog_categories_blog`
--
ALTER TABLE `articles_blog_categories_blog`
  ADD CONSTRAINT `FK_161963C89F851A36` FOREIGN KEY (`articles_blog_id`) REFERENCES `articles_blog` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_161963C8F2EAAF37` FOREIGN KEY (`categories_blog_id`) REFERENCES `categories_blog` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `articles_blog_tags_blog`
--
ALTER TABLE `articles_blog_tags_blog`
  ADD CONSTRAINT `FK_E56DD0F89F851A36` FOREIGN KEY (`articles_blog_id`) REFERENCES `articles_blog` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E56DD0F8FC5C9586` FOREIGN KEY (`tags_blog_id`) REFERENCES `tags_blog` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
