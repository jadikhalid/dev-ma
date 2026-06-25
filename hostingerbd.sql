-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 25 juin 2026 à 20:51
-- Version du serveur : 11.8.6-MariaDB-log
-- Version de PHP : 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `u282907555_devma`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'talents',
  `excerpt` text NOT NULL,
  `content` longtext NOT NULL,
  `translations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`translations`)),
  `cover_emoji` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
  `published_at` timestamp NULL DEFAULT NULL,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id`, `title`, `slug`, `category`, `excerpt`, `content`, `translations`, `cover_emoji`, `is_published`, `published_at`, `author_id`, `created_at`, `updated_at`) VALUES
(1, 'Le pool de talents tech au Maroc : état des lieux 2026', 'pool-talents-tech-maroc-2026', 'talents', 'Le Maroc compte plus de 30 000 talents tech qualifiés. Découvrez pourquoi les entreprises françaises s\'y tournent de plus en plus.', '<p>Le Maroc s\'est imposé comme un hub tech majeur en Afrique du Nord.</p>', '{\"en\":{\"title\":\"Morocco\'s tech talent pool: 2026 overview\",\"excerpt\":\"Morocco has over 30,000 qualified tech talents. Discover why French companies are turning to them.\",\"content\":\"<p>Morocco has established itself as a major tech hub in North Africa.<\\/p>\"}}', '🇲🇦', 1, '2026-05-27 23:55:34', NULL, '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(2, 'Remote France-Maroc : les bonnes pratiques', 'remote-france-maroc-bonnes-pratiques', 'guides', 'Fuseau horaire, communication, outils collaboratifs : tout ce qu\'il faut savoir.', '<p>Le décalage horaire France-Maroc est un atout pour la collaboration.</p>', '{\"en\":{\"title\":\"France-Morocco remote: best practices\",\"excerpt\":\"Time zones, communication, collaborative tools: everything you need to know.\",\"content\":\"<p>The France-Morocco time difference is an asset for collaboration.<\\/p>\"}}', '🌍', 1, '2026-04-30 23:55:34', NULL, '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(3, 'Pourquoi externaliser son développement au Maroc ?', 'pourquoi-externaliser-developpement-maroc', 'ecosysteme', 'Qualité, coût, proximité culturelle : les raisons qui convainquent les DSI françaises.', '<p>Les entreprises françaises choisissent le Maroc pour la qualité des profils.</p>', '{\"en\":{\"title\":\"Why outsource development to Morocco?\",\"excerpt\":\"Quality, cost, cultural proximity: reasons that convince French IT departments.\",\"content\":\"<p>French companies choose Morocco for the quality of profiles.<\\/p>\"}}', '💼', 1, '2026-06-09 23:55:34', NULL, '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(4, 'S\'installer au Maroc : guide de l\'entreprise française', 'installer-entreprise-francaise-maroc', 'guides', 'Juridique, fiscal, RH : les étapes clés pour implanter votre activité tech.', '<p>Au-delà du recrutement, de nombreuses entreprises s\'implantent au Maroc.</p>', '{\"en\":{\"title\":\"Setting up in Morocco: a guide for French companies\",\"excerpt\":\"Legal, tax, HR: key steps to establish your tech business.\",\"content\":\"<p>Beyond recruitment, many companies are establishing themselves in Morocco.<\\/p>\"}}', '🏢', 1, '2026-06-09 23:55:34', NULL, '2026-06-15 23:55:34', '2026-06-15 23:55:34');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('talents-du-maroc-cache-065a6bbcbbadd7fb5f3b22a8cd0407ba4095c5d6', 'i:3;', 1782235317),
('talents-du-maroc-cache-065a6bbcbbadd7fb5f3b22a8cd0407ba4095c5d6:timer', 'i:1782235317;', 1782235317),
('talents-du-maroc-cache-193503faa226a6dc23c862c40afb09219e8b04a9', 'i:1;', 1782304018),
('talents-du-maroc-cache-193503faa226a6dc23c862c40afb09219e8b04a9:timer', 'i:1782304018;', 1782304018),
('talents-du-maroc-cache-3f4c0ddecf53621d8aa00b81b29cfa91aa70871b', 'i:1;', 1782190831),
('talents-du-maroc-cache-3f4c0ddecf53621d8aa00b81b29cfa91aa70871b:timer', 'i:1782190831;', 1782190831),
('talents-du-maroc-cache-472b07b9fcf2c2451e8781e944bf5f77cd8457c8', 'i:2;', 1782235378),
('talents-du-maroc-cache-472b07b9fcf2c2451e8781e944bf5f77cd8457c8:timer', 'i:1782235378;', 1782235378),
('talents-du-maroc-cache-51a695347186668c9d991a51ab6ee2345c4f31dd', 'i:1;', 1782311341),
('talents-du-maroc-cache-51a695347186668c9d991a51ab6ee2345c4f31dd:timer', 'i:1782311341;', 1782311341),
('talents-du-maroc-cache-91ab578be187738971877b3283a1cef927831700', 'i:1;', 1782272168),
('talents-du-maroc-cache-91ab578be187738971877b3283a1cef927831700:timer', 'i:1782272168;', 1782272168),
('talents-du-maroc-cache-9a20012b869c327bc6e0868cbf0bae0976975f4c', 'i:1;', 1782345948),
('talents-du-maroc-cache-9a20012b869c327bc6e0868cbf0bae0976975f4c:timer', 'i:1782345948;', 1782345948),
('talents-du-maroc-cache-a1b7d361d22a9076249b9ce3d12c105b0f08256b', 'i:1;', 1781976325),
('talents-du-maroc-cache-a1b7d361d22a9076249b9ce3d12c105b0f08256b:timer', 'i:1781976325;', 1781976325),
('talents-du-maroc-cache-e04698029fff9caedc78e9adb85438b7a9828898', 'i:1;', 1782232643),
('talents-du-maroc-cache-e04698029fff9caedc78e9adb85438b7a9828898:timer', 'i:1782232643;', 1782232643),
('talents-du-maroc-cache-geoip:country:105.156.28.66', 's:2:\"MA\";', 1782431615),
('talents-du-maroc-cache-geoip:country:160.172.23.237', 's:2:\"MA\";', 1782390103),
('talents-du-maroc-cache-geoip:country:160.172.41.151', 's:2:\"MA\";', 1782318983),
('talents-du-maroc-cache-geoip:country:160.179.34.44', 's:2:\"MA\";', 1782334741),
('talents-du-maroc-cache-geoip:country:59.14.17.48', 's:2:\"KR\";', 1782397681);

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `company_profiles`
--

CREATE TABLE `company_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `sector` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'France',
  `city` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `employee_count` varchar(255) DEFAULT NULL,
  `hiring_needs` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `company_profiles`
--

INSERT INTO `company_profiles` (`id`, `user_id`, `company_name`, `sector`, `country`, `city`, `description`, `website`, `employee_count`, `hiring_needs`, `created_at`, `updated_at`) VALUES
(1, 14, 'Mueller LLC', 'SaaS', 'France', 'Paris', 'Entreprise européenne en recherche de talents tech marocains pour des projets en remote et du renfort ponctuel.', 'http://herman.com/dolorum-nam-maiores-omnis-asperiores', '200+', 'Talents full-stack, mobile et backend pour missions longue durée.', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(2, 15, 'Kessler-Lockman', 'Fintech', 'Belgique', 'Nantes', 'Entreprise européenne en recherche de talents tech marocains pour des projets en remote et du renfort ponctuel.', 'http://www.mcdermott.com/unde-dolor-nihil-voluptas-rerum-itaque.html', '200+', 'Talents full-stack, mobile et backend pour missions longue durée.', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(3, 16, 'O\'Connell Inc', 'Agence digitale', 'Suisse', 'Genève', 'Entreprise européenne en recherche de talents tech marocains pour des projets en remote et du renfort ponctuel.', 'http://bruen.com/officia-est-deleniti-quis-debitis', '51-200', 'Talents full-stack, mobile et backend pour missions longue durée.', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(4, 17, 'Senger-Keebler', 'Fintech', 'France', 'Paris', 'Entreprise européenne en recherche de talents tech marocains pour des projets en remote et du renfort ponctuel.', 'http://jacobi.com/', '200+', 'Talents full-stack, mobile et backend pour missions longue durée.', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(5, 18, 'Reichel, Armstrong and Stanton', 'SaaS', 'France', 'Paris', 'Entreprise européenne en recherche de talents tech marocains pour des projets en remote et du renfort ponctuel.', 'http://monahan.net/', '1-10', 'Talents full-stack, mobile et backend pour missions longue durée.', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(6, 19, NULL, NULL, 'France', NULL, NULL, NULL, NULL, NULL, '2026-06-15 23:57:16', '2026-06-15 23:57:16');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(255) NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `magazine_banner_items`
--

CREATE TABLE `magazine_banner_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `magazine_banner_items`
--

INSERT INTO `magazine_banner_items` (`id`, `title`, `subtitle`, `url`, `thumbnail`, `created_by`, `created_at`, `updated_at`) VALUES
(8, 'HPS. Brahim Berrada fait le point sur l\'avancement de son plan stratégique                                     .', 'Après plusieurs années d\'investissements structurants, HPS estime être entré dans une phase d\'exécution et de création de valeur. Backlog record, montée en puissance du SaaS, intégration de CR2 et expansion internationale...', 'https://medias24.com/2026/06/19/hps-brahim-berrada-fait-le-point-sur-lavancement-de-son-plan-strategique-1703925/', 'magazine-banner/rbTWeZUxy79kNiyZYqdoMSRSX3gYHmWTbQ7Ldnei.jpg', 21, '2026-06-20 20:53:34', '2026-06-20 20:53:34'),
(9, 'Le CMI lance WaSL et ouvre la voie aux Super Apps marocaines.', 'Cette infrastructure permet au CMI d\'ouvrir une nouvelle étape : passer du paiement multicanal à la distribution massive de services connectés.', 'https://telquel.ma/sponsors/le-cmi-lance-wasl-et-ouvre-la-voie-aux-super-apps-marocaines_1994277', 'magazine-banner/lZU4jeIGgGS7jGmsKmJDdehciSumtFVqGzDJB8Ku.png', 21, '2026-06-20 20:55:26', '2026-06-20 20:55:26'),
(10, 'De Marwa à Haldorix: le pari technologique de Karim Tazi', 'Exit le prêt-à-porter, place désormais au code informatique, aux algorithmes, à l’intelligence artificielle et aux start-up.', 'https://lareleve.ma/194837/', 'magazine-banner/EZZ0BvdYoahExmGpou5OEwnGaz2scLmb4leuHqhq.jpg', 21, '2026-06-22 21:12:41', '2026-06-22 21:12:41'),
(11, 'TELUS Digital inaugure son nouveau site à Casablanca Finance City', 'TELUS Digital a inauguré, jeudi 18 juin, un nouveau site au sein de Casablanca Finance City (CFC), renforçant ainsi sa présence au Maroc', 'https://lnt.ma/telus-digital-inaugure-son-nouveau-site-a-casablanca-finance-city/', 'magazine-banner/lwR7ZYmOIKJkMnaO81g9gIubfu5JxSIjltzb9osy.jpg', 21, '2026-06-22 21:17:49', '2026-06-22 21:17:49'),
(12, 'Après son roadshow en Chine, CFC détaille sa stratégie pour attirer les entreprises tournées vers l\'Afrique.', 'Casablanca Finance City veut capter l’étage supérieur de cette présence : les centres de pilotage, les services financiers, les holdings et les dispositifs de financement durable.', 'https://medias24.com/2026/06/22/apres-son-roadshow-en-chine-cfc-detaille-sa-strategie-pour-attirer-les-entreprises-tournees-vers-lafrique-1705139/', 'magazine-banner/bupmtAFpGwZKWox8s7Sq9Ip9nOjW7KXKYdZoLBUm.png', 21, '2026-06-23 07:53:38', '2026-06-23 07:53:38'),
(14, 'SAHAM BANK investit le Bitcoin.', 'Premier produit d\'investissement adossé au Bitcoin au Maroc.', 'https://medias24.com/2026/06/23/exclusif-saham-bank-devoile-a-medias24-les-dessous-et-la-strategie-derriere-son-produit-adosse-au-bitcoin-1706013/', 'magazine-banner/te1Z64mYy7BG8ELbYWAiycidonZfwSmzJpJ2YFXO.png', 21, '2026-06-24 09:17:21', '2026-06-24 09:17:21'),
(15, 'Revolut au Maroc !? La banque centrale temporise...', 'Le wali explique que la Banque centrale est actuellement mobilisée par plusieurs chantiers réglementaires et institutionnels.', 'https://medias24.com/2026/06/24/revolut-au-maroc-jouahri-explique-pourquoi-le-dossier-navance-pas-1707185/', 'magazine-banner/Y8nzbkMvQ9GMzh49I8F29RGpewz93WUP9TOA50zk.jpg', 21, '2026-06-25 09:26:22', '2026-06-25 09:26:22');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_06_10_190851_add_role_and_subscription_to_users_table', 1),
(5, '2026_06_10_191123_create_profiles_table', 1),
(6, '2026_06_11_100001_create_company_profiles_table', 1),
(7, '2026_06_11_100002_add_country_to_profiles_table', 1),
(8, '2026_06_11_100003_create_articles_table', 1),
(9, '2026_06_11_100004_create_services_table', 1),
(10, '2026_06_11_100005_create_recruitment_requests_table', 1),
(11, '2026_06_11_110000_add_translations_to_articles_and_services', 1),
(12, '2026_06_19_100000_create_professions_tables', 2),
(13, '2026_06_20_100000_create_magazine_banner_items_table', 3);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `professions`
--

CREATE TABLE `professions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `name_fr` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `professions`
--

INSERT INTO `professions` (`id`, `slug`, `name_fr`, `name_en`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'web-developer', 'Développeur web', 'Web developer', 1, 1, '2026-06-20 16:58:18', '2026-06-20 16:58:18');

-- --------------------------------------------------------

--
-- Structure de la table `profession_suggestions`
--

CREATE TABLE `profession_suggestions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `profession_id` bigint(20) UNSIGNED NOT NULL,
  `label_fr` varchar(255) NOT NULL,
  `label_en` varchar(255) NOT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `profession_suggestions`
--

INSERT INTO `profession_suggestions` (`id`, `profession_id`, `label_fr`, `label_en`, `keywords`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Laravel', 'Laravel', 'php framework', 1, 1, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(2, 1, 'React', 'React', 'javascript frontend js', 1, 2, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(3, 1, 'Vue.js', 'Vue.js', 'javascript frontend js', 1, 3, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(4, 1, 'Next.js', 'Next.js', 'react javascript frontend', 1, 4, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(5, 1, 'Angular', 'Angular', 'typescript frontend', 1, 5, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(6, 1, 'PHP', 'PHP', 'backend symfony laravel', 1, 6, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(7, 1, 'Symfony', 'Symfony', 'php backend', 1, 7, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(8, 1, 'JavaScript', 'JavaScript', 'js frontend backend node', 1, 8, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(9, 1, 'TypeScript', 'TypeScript', 'ts javascript', 1, 9, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(10, 1, 'Node.js', 'Node.js', 'javascript backend api', 1, 10, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(11, 1, 'Développeur full-stack', 'Full-stack developer', 'fullstack full stack', 1, 11, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(12, 1, 'Développeur frontend', 'Frontend developer', 'front-end ui', 1, 12, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(13, 1, 'Développeur backend', 'Backend developer', 'back-end api', 1, 13, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(14, 1, 'API REST', 'REST API', 'api backend', 1, 14, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(15, 1, 'GraphQL', 'GraphQL', 'api backend', 1, 15, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(16, 1, 'MySQL', 'MySQL', 'sql database bdd', 1, 16, '2026-06-20 16:58:18', '2026-06-20 16:58:18'),
(17, 1, 'PostgreSQL', 'PostgreSQL', 'sql database bdd postgres', 1, 17, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(18, 1, 'MongoDB', 'MongoDB', 'nosql database', 1, 18, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(19, 1, 'Docker', 'Docker', 'devops conteneur container', 1, 19, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(20, 1, 'DevOps', 'DevOps', 'ci cd infrastructure', 1, 20, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(21, 1, 'Tailwind CSS', 'Tailwind CSS', 'css frontend ui', 1, 21, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(22, 1, 'WordPress', 'WordPress', 'cms php', 1, 22, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(23, 1, 'Shopify', 'Shopify', 'e-commerce ecommerce', 1, 23, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(24, 1, 'React Native', 'React Native', 'mobile javascript', 1, 24, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(25, 1, 'Flutter', 'Flutter', 'mobile dart', 1, 25, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(26, 1, 'Python', 'Python', 'django flask backend', 1, 26, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(27, 1, 'Django', 'Django', 'python backend', 1, 27, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(28, 1, 'Product Designer', 'Product Designer', 'ux ui design', 1, 28, '2026-06-20 16:58:19', '2026-06-20 16:58:19'),
(29, 1, 'UI/UX Designer', 'UI/UX Designer', 'design figma', 1, 29, '2026-06-20 16:58:19', '2026-06-20 16:58:19');

-- --------------------------------------------------------

--
-- Structure de la table `profiles`
--

CREATE TABLE `profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skills`)),
  `experience_years` int(11) NOT NULL DEFAULT 0,
  `daily_rate_eur` int(11) DEFAULT NULL,
  `availability` varchar(255) NOT NULL DEFAULT 'disponible',
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'Maroc',
  `github_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `title`, `bio`, `skills`, `experience_years`, `daily_rate_eur`, `availability`, `city`, `country`, `github_url`, `linkedin_url`, `portfolio_url`, `created_at`, `updated_at`) VALUES
(1, 1, 'Talent Mobile Flutter & React Native', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"Laravel\",\"Node.js\",\"PHP\",\"TypeScript\"]', 11, 391, 'disponible', 'Casablanca', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(2, 2, 'Talent Frontend React / Next.js', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"Node.js\",\"Laravel\",\"TypeScript\",\"React\"]', 11, 488, 'sous 2 semaines', 'Marrakech', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(3, 3, 'Ingénieur Backend Laravel & Node.js', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"Node.js\",\"React\",\"Flutter\",\"PHP\"]', 13, 264, 'mission en cours', 'Marrakech', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(4, 4, 'Talent Mobile Flutter & React Native', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"React\",\"Docker\",\"Laravel\",\"TypeScript\"]', 4, 280, 'sous 2 semaines', 'Marrakech', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(5, 5, 'Talent Full Stack MERN', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"React\",\"Flutter\",\"MySQL\",\"Laravel\"]', 11, 451, 'sous 2 semaines', 'Marrakech', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(6, 6, 'Talent Mobile Flutter & React Native', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"PHP\",\"Docker\",\"Flutter\",\"Laravel\"]', 15, 546, 'disponible', 'Casablanca', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(7, 7, 'Talent Mobile Flutter & React Native', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"TypeScript\",\"PHP\",\"Node.js\",\"Flutter\"]', 9, 391, 'mission en cours', 'Casablanca', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(8, 8, 'Talent Frontend React / Next.js', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"React\",\"Flutter\",\"Laravel\",\"Docker\"]', 4, 544, 'sous 2 semaines', 'Casablanca', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(9, 9, 'Talent Mobile Flutter & React Native', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"MySQL\",\"PHP\",\"Flutter\",\"React\"]', 10, 482, 'mission en cours', 'Casablanca', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(10, 10, 'Talent Mobile Flutter & React Native', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"PHP\",\"Laravel\",\"Flutter\",\"MySQL\"]', 4, 356, 'sous 2 semaines', 'Marrakech', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(11, 11, 'Architecte Cloud & PHP Engineer', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"React\",\"Docker\",\"MySQL\",\"Laravel\"]', 7, 301, 'sous 2 semaines', 'Casablanca', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(12, 12, 'Talent Mobile Flutter & React Native', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"Docker\",\"Node.js\",\"Flutter\",\"TypeScript\"]', 7, 329, 'disponible', 'Marrakech', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(13, 13, 'Ingénieur Backend Laravel & Node.js', 'Passionné par le développement web depuis plusieurs années. Spécialisé dans les architectures modernes, j\'accompagne les entreprises françaises et européennes dans la création d\'applications scalables et performantes. Rigoureux, autonome et habitué au travail en full-remote.', '[\"React\",\"TypeScript\",\"Docker\",\"Flutter\"]', 15, 423, 'mission en cours', 'Tanger', 'Maroc', 'https://github.com', 'https://linkedin.com', 'https://google.com', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(14, 20, 'Full Stack Developer', 'abc ajdc[o sqowjnd spjqw idj', '[]', 10, 500, 'disponible', 'Casablanca', 'Maroc', NULL, NULL, NULL, '2026-06-20 15:13:26', '2026-06-20 15:16:12');

-- --------------------------------------------------------

--
-- Structure de la table `recruitment_requests`
--

CREATE TABLE `recruitment_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_user_id` bigint(20) UNSIGNED NOT NULL,
  `developer_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mode` enum('direct','intermediary') NOT NULL DEFAULT 'intermediary',
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `summary` text NOT NULL,
  `content` longtext NOT NULL,
  `translations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`translations`)),
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `slug`, `title`, `icon`, `summary`, `content`, `translations`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'conseil-juridique', 'Conseil juridique', '⚖️', 'Création de société, contrats, conformité et accompagnement réglementaire pour s\'installer au Maroc.', '<p>Nous accompagnons les entreprises françaises et européennes dans leurs démarches juridiques au Maroc.</p>', '{\"en\":{\"title\":\"Legal advisory\",\"summary\":\"Company formation, contracts, compliance and regulatory support for setting up in Morocco.\",\"content\":\"<p>We support French and European companies with legal procedures in Morocco.<\\/p>\"}}', 1, 1, '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(2, 'conseil-financier', 'Conseil financier & DAF', '💰', 'Fiscalité, comptabilité, trésorerie et pilotage financier pour votre activité marocaine.', '<p>Optimisez votre structure financière au Maroc avec un accompagnement sur mesure.</p>', '{\"en\":{\"title\":\"Financial advisory & CFO\",\"summary\":\"Tax, accounting, treasury and financial management for your Moroccan operations.\",\"content\":\"<p>Optimize your financial structure in Morocco with tailored support.<\\/p>\"}}', 2, 1, '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(3, 'relations-publiques', 'Relations publiques', '📢', 'Visibilité locale, relations presse et communication pour ancrer votre marque au Maroc.', '<p>Développez votre notoriété au Maroc grâce à une stratégie RP adaptée.</p>', '{\"en\":{\"title\":\"Public relations\",\"summary\":\"Local visibility, press relations and communication to anchor your brand in Morocco.\",\"content\":\"<p>Build your reputation in Morocco with an adapted PR strategy.<\\/p>\"}}', 3, 1, '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(4, 'ressources-humaines', 'Ressources humaines', '👥', 'Recrutement local, gestion des équipes et conformité sociale marocaine.', '<p>Constituez et managez vos équipes au Maroc.</p>', '{\"en\":{\"title\":\"Human resources\",\"summary\":\"Local recruitment, team management and Moroccan labour compliance.\",\"content\":\"<p>Build and manage your teams in Morocco.<\\/p>\"}}', 4, 1, '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(5, 'accompagnement-implantation', 'Accompagnement implantation', '🏢', 'Bureau, coworking, réseau local et mise en relation avec l\'écosystème tech marocain.', '<p>De la recherche de locaux à l\'intégration dans les hubs tech.</p>', '{\"en\":{\"title\":\"Setup support\",\"summary\":\"Office, coworking, local network and connection to the Moroccan tech ecosystem.\",\"content\":\"<p>From finding premises to integrating into tech hubs.<\\/p>\"}}', 5, 1, '2026-06-15 23:55:34', '2026-06-15 23:55:34');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0AfCZU0ZgxC6jVQtnABBPtfHKpT3gKgZPFzQsGRG', NULL, '54.87.90.56', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4950.2 Safari/537.36', 'eyJfdG9rZW4iOiJWQWYyeE1pdWFoVnFUcVByakllVHZKZ1NIMHAzeTd6MXFPYjA1YW8wIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782393504),
('37Z24TUcQxN7yyNj4DhYFHoE70sXXbCQZE6cs5O2', NULL, '135.148.195.16', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJKSEw1NWFJdVd2U1ZTNnBXbkRUUUhjbVRDek5kVUZZSklPejBKVGN0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782383834),
('5PVkxKytNaiuszLeklQhbzrwmQ5romsOv9suVvvA', NULL, '165.232.133.200', 'Mozilla/5.0 (X11; Linux x86_64; rv:142.0) Gecko/20100101 Firefox/142.0', 'eyJfdG9rZW4iOiJ5TlhhRDVYb1Bja2p0ZmlmVk5RcUZFWmdFUE94dDZVSkw5b09KZDV5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782411820),
('6T3lTjNO9Kcs2HvDpa9XABoYY6lEfXg7SlFmWTvS', NULL, '105.154.88.63', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJXVjkxeFA0RE40YlgyejB5MjFGZjNIWUZvV0pYNURaMkJUY0JTcG55IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782404888),
('7BgUcQv05JHsDcJFGl1RvUxccsyo62qQSrnk0DyS', NULL, '23.83.81.176', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiI2UEp2SmZrTVVnQTlMUktDaGJyTXEzek0zQWVVODZVc3RiTEY5WnRXIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC93d3cudGFsZW50c2R1bWFyb2MuY29tIiwicm91dGUiOiJob21lIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1782387880),
('FQ172K8HSM0wescyBeS2kiA0pUvHHDVmrhb0yHFO', NULL, '168.76.131.151', 'Mozilla/5.0 (Linux; Android 7.1.1; Pixel XL Build/NMF26Q) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Mobile Safari/537.36', 'eyJfdG9rZW4iOiJ3NXRjT2hrYWRweUZXOEg2Mlc3bHlOSDdDQU44bnAwc1FldjJ1N3I5IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782404051),
('iKYQgE65mWf1q3TOplnm3wH0hGnS7g7msGlG7LdA', 21, '196.127.13.216', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJTeTU1M1Y0Wm5KUFE3MjB2OUtlWDJSbGNTQ1hyb0RSMm5XTW9kaVpMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MjF9', 1782384858),
('Q7jZ5rphKO18AYYgsp2VfRIPVeBcad9jSlZmzOFk', NULL, '105.156.28.66', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJZVDhpdHg3SXVjYVRJOGprVGM0TnBHdHZNaHVLeGRGYkhxTEpjNnpYIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782390885),
('xTDq6LFiMs2ZnmVetaUlgtq8r6he82rqsdmnfSqa', NULL, '138.226.69.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.3', 'eyJfdG9rZW4iOiJXNVdKdHF2YjFscG1uMWFnT3BQeVowdUU3MVpHMU5BOTdINzJWa2hxIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782417654),
('y1p2L6t5l76yWK2bFRw1Xkz8g5owr4ExXNtdAmRA', NULL, '105.154.88.63', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJxcER2MnJKM3d0QmZGMkVYbDRsbkZNTzFQWGFISzlLbUowWEdLaGdMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782415731),
('z9eWkAhq3RGiq7Y7kTXpvl396qe9aKuhll1Gf0Qu', NULL, '41.250.193.134', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJXZklNVkhZREhRZEV3T1ZQUFJmZlpqbElNQ0twYVpMMTdncXpSSjZ0IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC90YWxlbnRzZHVtYXJvYy5jb20iLCJyb3V0ZSI6ImhvbWUifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==', 1782401461);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'dev',
  `is_subscribed` tinyint(1) NOT NULL DEFAULT 0,
  `subscription_expires_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_subscribed`, `subscription_expires_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Kiley Kertzmann', 'nitzsche.bria@example.org', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', 'fRQYLNwtiZ', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(2, 'Ray Bergstrom', 'cberge@example.com', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', 'pbS311q4wt', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(3, 'Dr. Alene O\'Conner MD', 'quigley.julie@example.net', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', 'LLiVMCt7M9', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(4, 'Trenton Deckow', 'rruecker@example.net', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', 'hsV7rfjjjA', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(5, 'Winona Sipes', 'nwilkinson@example.org', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', 'Sv8tYxc263', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(6, 'Mertie Rodriguez', 'fgaylord@example.org', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', 'Uh6ltH0uYK', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(7, 'Janie Sauer', 'merritt22@example.org', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', 'CTx1uqSGKB', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(8, 'Madeline Harvey', 'hodkiewicz.milo@example.com', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', 'DgHDKXDi3P', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(9, 'Meggie Fahey Jr.', 'adach@example.com', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', '8btkomAKUH', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(10, 'Dr. Keshawn Cronin Jr.', 'graciela31@example.net', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 1, '2027-06-15 23:55:34', 'dB6nA572Sp', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(11, 'Reagan Schaden Sr.', 'zpaucek@example.com', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 0, NULL, 'nigxTpJyKD', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(12, 'Prof. Keanu Sanford', 'vhackett@example.org', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 0, NULL, 'D9imyiTYAg', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(13, 'Belle Douglas DVM', 'tyrell53@example.org', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'dev', 0, NULL, 'vH8ngaM6Hq', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(14, 'Trystan Langworth', 'hettinger.reina@example.org', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'company', 0, NULL, 'VDZCfXdgkp', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(15, 'Felipa Schinner Sr.', 'haylie.corkery@example.com', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'company', 0, NULL, 'cGdWozJSMN', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(16, 'Miss Bernadette Abbott', 'mschaefer@example.com', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'company', 0, NULL, 'apkNa6RRpK', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(17, 'Ms. Kaelyn Schumm IV', 'allen.kilback@example.net', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'company', 0, NULL, 'p3alLVDeT6', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(18, 'Dr. Jonas Champlin Jr.', 'darrel94@example.net', '2026-06-15 23:55:34', '$2y$12$k82r.J.5Pf/YalRZjsRSr.WCnmHlBVu6nzxnWjFi6/8P5pF60eEjy', 'company', 0, NULL, 'UkH7H296hC', '2026-06-15 23:55:34', '2026-06-15 23:55:34'),
(19, 'jadi khalid', 'jadikhalid@gmail.com', NULL, '$2y$12$oxJBpNAvN580Zr52wTMI9.H9k.8nDIEHlSeO4CqnXhYxL1zVpQEEK', 'company', 0, NULL, NULL, '2026-06-15 23:57:16', '2026-06-15 23:57:16'),
(20, 'jadi badr', 'jadibadr@gmail.com', NULL, '$2y$12$EuYP/Bzn6z9c/JyBGqx1K.I41eXUIGWh1GmLIY/pl0whMw8tXsWde', 'dev', 0, NULL, NULL, '2026-06-20 15:13:26', '2026-06-20 15:13:26'),
(21, 'Administrateur', 'admin@talentsdumaroc.com', '2026-06-20 20:43:40', '$2y$12$tQMA0I/wbkK53suSA3Kg6OLC1LF.vWte7irYkZ1ViQxjM5Nu9zpj2', 'admin', 0, NULL, NULL, '2026-06-20 20:43:40', '2026-06-20 20:58:25');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `articles_slug_unique` (`slug`),
  ADD KEY `articles_author_id_foreign` (`author_id`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Index pour la table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_profiles_user_id_foreign` (`user_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `magazine_banner_items`
--
ALTER TABLE `magazine_banner_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `magazine_banner_items_created_by_foreign` (`created_by`),
  ADD KEY `magazine_banner_items_created_at_index` (`created_at`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `professions`
--
ALTER TABLE `professions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `professions_slug_unique` (`slug`);

--
-- Index pour la table `profession_suggestions`
--
ALTER TABLE `profession_suggestions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profession_suggestions_profession_id_is_active_index` (`profession_id`,`is_active`);

--
-- Index pour la table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profiles_user_id_foreign` (`user_id`);

--
-- Index pour la table `recruitment_requests`
--
ALTER TABLE `recruitment_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recruitment_requests_company_user_id_foreign` (`company_user_id`),
  ADD KEY `recruitment_requests_developer_user_id_foreign` (`developer_user_id`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_slug_unique` (`slug`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `company_profiles`
--
ALTER TABLE `company_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `magazine_banner_items`
--
ALTER TABLE `magazine_banner_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `professions`
--
ALTER TABLE `professions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `profession_suggestions`
--
ALTER TABLE `profession_suggestions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `recruitment_requests`
--
ALTER TABLE `recruitment_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD CONSTRAINT `company_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `magazine_banner_items`
--
ALTER TABLE `magazine_banner_items`
  ADD CONSTRAINT `magazine_banner_items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `profession_suggestions`
--
ALTER TABLE `profession_suggestions`
  ADD CONSTRAINT `profession_suggestions_profession_id_foreign` FOREIGN KEY (`profession_id`) REFERENCES `professions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `recruitment_requests`
--
ALTER TABLE `recruitment_requests`
  ADD CONSTRAINT `recruitment_requests_company_user_id_foreign` FOREIGN KEY (`company_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recruitment_requests_developer_user_id_foreign` FOREIGN KEY (`developer_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
