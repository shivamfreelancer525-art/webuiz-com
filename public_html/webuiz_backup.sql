/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: webuiz_web
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('1xIfH7piFLdeJ7hhMuNjiUuTPBPteNT7r9qhkv7T',NULL,'51.68.111.240','Mozilla/5.0 (compatible; MJ12bot/v2.0.4; http://mj12bot.com/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ0M1ZVRvNDFSdEw2Rmg1eVozdTJCT2hjUnk1MkVvNDlBODRsMHZTOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764627595),
('23rXlCD7Dfozline5HBTVnseEMCCmimplPQHXCav',NULL,'182.69.118.237','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS3hEN1h1ZkY4VENGc2JQdmxEd1ZrdHBOOXlIeHJUcVhkNExqWVFrMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764652289),
('3GaIclSIZAlLfc4GslmEQSK3WbB1To52bgjmOVrs',NULL,'43.135.185.59','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRlJUQlRlSFlQUTRuZWJEa2ZKek1kcGFLNzNodFpoSkJzakMxT1V3SCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9wcml2YWN5LXBvbGljeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764648883),
('3hhdr5VG3FsDy6Lu6zGv4y1na2IogJdSjwk1BEXV',NULL,'52.73.6.26','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot) Chrome/119.0.6045.214 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZUtqaE5JWDM0MG9ad0pVZW1UcWg3MmpSTWE0UmRBTnBBWEFQd0RUWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vY29va2llLXBvbGljeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764611427),
('3KgO6qYtH2zBP7l4TlYT1NbzqH8xnnchzGLuO6fb',NULL,'51.89.129.33','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFpuUWQwd3ZxT0dOdFJ4QmpHM1NKOW0zN1JRVTRJS1R1cGZ5bEZveCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpY2luZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764631062),
('3OGIIagz8ZfOzxJdqcwgJqmrDtFnb7NX2dqYYVkt',NULL,'98.84.13.3','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/138.0.7204.23 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU2VXRTFWakdiRmZXOUNtMUZaMWtKQldIZkdsSEFSNXlkUERrTWFMWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764623387),
('3Om1rtrhiFj3I06TSKx3ERnWh15YzUr3EaPDyhdi',NULL,'85.203.45.155','','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTFMxd1U5WktRZmo2RGhyc2w0bmZ1RHZGdHA0SzFya3A4ZTNFd21ENyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764642234),
('4FSQIyz9GTtGiI6T2ejZVkpTSzVkmquaMYLgAUTl',NULL,'54.221.16.217','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.54 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN083czYxaEUwYzhSalhVN28yZTJKTEhQV1F2d2IycElRcDA5YjBRUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764665174),
('4W5kDstlMEw0XO8dfPIExFMZtRwCHXb6v8CHSkI2',NULL,'149.56.150.88','Mozilla/5.0 (compatible; Dataprovider.com)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTHlUcmkxRHZvdDAyRFVtc3VLOURWRXJiYU5lQ08xY1FSWVJlMXBmSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764666106),
('6IpKsbYgbflsk3kWGqGdI9t9nL89Kem1GPfMSUdv',NULL,'49.51.183.220','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWVjNWFhV3dHc3g0N2g0dGVZNjdzeVFWMnAwMjNwQVNrRFlWaE5KbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764671582),
('a332cITIuPQWdicwAcH1iLASTEzAxjzfhz6z4l8J',NULL,'35.168.238.50','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot) Chrome/119.0.6045.214 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmo5N1dHemszc1liVGRPM0VLYk5nRWpHWjVYNkxDZ2lxb2h4UjFtUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcmVmdW5kLXBvbGljeSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764608871),
('AcQ1Smqd7Q3yNjxMIvumL9mdGNu2BgO3cwL7IDHo',NULL,'44.212.232.231','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot) Chrome/119.0.6045.214 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiU0x0aUx6TUF5aFNvTVFHVVpDalczWmF3TzRicFNFWE1xa3hEOVlyRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vYWJvdXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764634028),
('An5KGUigZ8i5XitAjiwqfepsL0CILTFDmdKFKIFY',NULL,'34.234.197.175','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot) Chrome/119.0.6045.214 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSVZEdkJ2ejhJWTNRckxjTVRONG1uNUtQOWlHampnU0NaSW1mRmVNeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vY29udGFjdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764593599),
('bCiOSgjgGDyZUsCwouMoS6DFoorht3iaOCiVRur5',NULL,'66.249.70.132','Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWDNWTXhuZUpZNEtPUnRWbVNoZ001N2hZTlhudUczRmRCZXpXWjVLcyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpY2luZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764612177),
('C16efUNhOHek6FNxaRa811vGnGU6c1sSyccuGVv1',NULL,'54.237.29.105','Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNUhPM3R3ZnVJeHJQc2Y3clU2R1dzM0dVSEl1Z2F4enZwRUxZdjR4NCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764643505),
('cdEWYpOQWVJydALHhXd8890tltEU1tReVmmIRA0J',NULL,'4.189.136.80','Mozilla/5.0 (Linux; Android 13; SM-S908E) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidmM5eWRjRVVqWlVtUmNNOXZyTWQ1ZkRqbG5RMDFtY3g4ajl4WjNsbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9wdWJsaWMvaW5kZXgucGhwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1764596691),
('CvU4jyDojIIlFuPYWaviujBqdYIJ0UvE2spzC5SI',NULL,'136.112.96.207','Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSVRCbkNjOG9mZnRLZVpLeFFDZVpUYlU1NUd2WUxacHV3Q200T1ZZeCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764597001),
('DHbIUcMFTTEHzfiyUMdHUxdGZy2Rme73USd1dVRV',NULL,'149.56.150.12','Mozilla/5.0 (compatible; Dataprovider.com)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWmdzbExhZnBaMThvUjE3VW5OOHdVbEJvN0NIREZuQ04zb0RCQU1EZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764666170),
('DnjZJVtvvWY77O2Gmde16yddt7SiRqUx9bZ6Q2Qy',NULL,'35.168.238.50','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot) Chrome/119.0.6045.214 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoib1ZEU1RlbGhaRllXSXd4NXA1Y1VVYmV6OU9QSWc2RkUxNnYzMDQ0NCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpdmFjeS1wb2xpY3kiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764629966),
('dqNllKIwwX7KeT6Or8tF6POy4ZSk05He6EvI9ODN',NULL,'85.203.44.6','','YTozOntzOjY6Il90b2tlbiI7czo0MDoicFczOHBnM1V3UDM3a2c3QmwyR2VpQUMzT1hEbXV4dWN2dXFpV0JRWCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764600380),
('EAiisUP6TBfAMfdBjzdt6Jf4OjbHlNrpO5RZ3GFs',NULL,'43.164.197.209','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRno0ZExhczBBbjVQS2dqb2l5SGgwQ1hPZ2dTZDJib0hZdERVTGZOZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764604756),
('f65en02MuagPaPmG7Y13Odds0JJP4rUIIa27kZMr',NULL,'43.153.123.4','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWTlZdzJRNUZtdXVnVzhYOEEwVVpKVWdSU3N3RkxBd3NBVHZKbzBrUCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764604299),
('FjmAbzsU2ZCuc8skEFt0jZIBHTtNUyTPW1htAHvD',NULL,'149.56.150.12','Mozilla/5.0 (compatible; Dataprovider.com)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiY1ZwQXRqT01mSTRVVUJMV205a0M0MmlEWlZqM2pYQmN3dXlhSUQ4OSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764666166),
('fM3b83mvcoTyE5FGCnL1o49EGlSgJObSSVaXauuu',NULL,'54.89.90.224','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot) Chrome/119.0.6045.214 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSEtHaHE5ZlpyYkx6TTRNVFFtNWxBMjZHcklLRnkyR05YVmZJc3pnOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vZmVhdHVyZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764634031),
('FRFAOsBZ30t1uoy4EkIT2p5LiKmeT7pMAx3VTJUo',NULL,'45.149.173.233','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTllnWUFwRFZwcU1ESTFlbUdKa1JUZ0FucVh2TVZqTER4VHoyMDJQRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764599323),
('gHDnHkyy5xiPAmWKjZjWiZtwIjqyFVNb690dFjT6',NULL,'195.178.110.201','Go-http-client/1.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUVgxU0ZIb012dzJ2Sjd6SUMycU9PSm1qM0tNNWg0Z0ZXN1BVQVVuSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764606396),
('hkBOYZH6pZSQlXpiVTGs7wzNtQ4YIcL6Mnibcnr2',NULL,'43.159.135.203','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoic2JwVWJrUUhRQlBOZkdSN0xVdkRwazd5UzFzZ1Fvd2tKS2JYQXZlSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9yZWZ1bmQtcG9saWN5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1764674850),
('HSOH231kCpUUkO5XrGsTKQn9kbuqGpjV3UV1ii1q',NULL,'66.249.70.132','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.7390.122 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMWczMGkwYnZsMjNCSFdYY1d1aXQzWVkxQVo1MUh4eGNhUlVPZG1ncyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764644036),
('HsvfERIc0FWtGnGtlx2roicKG3akmtPYRZIstsJZ',NULL,'54.84.93.8','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot) Chrome/119.0.6045.214 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTlVwZ3l4TjZjeURZdlZNeGw2RXBWbTVSS2c4MXEwUG9VdUJITEVxdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vZmFxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1764673290),
('I0xcc8KjFYkMIJ8TFBWKTJmRyjknzWn6apA7kzYP',NULL,'51.68.107.139','Mozilla/5.0 (compatible; MJ12bot/v2.0.4; http://mj12bot.com/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoibjczbzVGT25iamJtYkwzNUZoMWNCQmNHMmRRQjhNZFg4T3I2S2txNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764594912),
('ityIsUGCuHXJDWC1JgzMc29QxMtEaw1R7PGyqGJH',NULL,'74.7.241.28','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.3; +https://openai.com/gptbot)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN3YxTExLcEprck9MZ1ZsNnRxZXB6SGpUVzhrcFdLWkxsdTVHQ0V2VSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764659775),
('J2nXovL53ZRusFlmzQIwtV2YW6egpAcWwdvXS0i8',NULL,'35.231.240.198','Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUm9vZTQ5a0IwZ05WZ1VFalJTRnZIaElsNk5ibEpvRjRiRHZxbFFLSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764603758),
('jvMJJOhA9woIBN8dXLaOi4pKmmBTnRGvDLDLMj4K',NULL,'184.154.76.14','Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/6.0)','YTozOntzOjY6Il90b2tlbiI7czo0MDoidnBnVDdCUUZyVTk2dXNYTko5UVg0ek11WXlxVHowdFhaWWR6UVN2OSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764619483),
('JxTljBckLqDclDYKB8CKFObeJRAbwyQtteSV5iAj',NULL,'136.111.61.44','Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)','YTozOntzOjY6Il90b2tlbiI7czo0MDoic2tybTdIMEp3OWlOeUZicW0wZ1pXR3lpQ0F5cHVMMHY4aVdLd1F5NSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764598414),
('JY5VCdCrT9VhH8UjBIoTZfkGZGbwxcl2vK9X9rYR',NULL,'170.106.192.208','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoicEdIVVJHVFpKeFp2ZlFkSWZTVWtDcllyeEY2ekZHVnoxOTcwVjFnYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764644517),
('k8nYpQLJLjzGs6mtyKnOpaooHM9Jj6GkmB30KCJo',NULL,'106.119.167.146','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVUZ2bzdzQ252a1huVHc0Q2hDZnFXVjVySnJ3aG0wOWdCNjNJeUNmMyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764654301),
('KHq9ewXqtD5ie9xtZvmzkX8wBB7iw0obD0n7gNEV',NULL,'4.189.136.80','Mozilla/5.0 (Linux; Android 13; SM-G991U) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Mobile Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiamVHU1Jvb0VTbE55RGJMQXkxTW9UM2JpaGFaN1dKalJsUjZjb2JmbCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9wdWJsaWMvaW5kZXgucGhwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1764596684),
('LhT9KS6I7YThoqt3N6G2FzTYsM1OF842FyflmkqW',NULL,'4.217.181.219','Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) FxiOS/118.0 Mobile/15E148 Safari/605.1.15','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVndjaFdkeUh4d1NzajN3cUVvV2k4VWZteVdVNDJkR2I0c0lnc2pMOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9wdWJsaWMvaW5kZXgucGhwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1764614252),
('lq5Ja9VBJqhKswjCfK9Thr4E0EAk80OLjKD4aMle',NULL,'182.69.118.237','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVmhqTVY4QjVvellzU1ZVbHJCOEZIanBtdU1meDc0U1YwdUh0UG5mTSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpY2luZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764662940),
('LYOducWDbCOF9o6v5LZl00ClSFfKAMqAZLtN6KO5',NULL,'49.51.195.195','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMjV1MElkTzRVdXJEM0FTbWpYdlF1R1hkU1ZVVkxJd1Qxc003clhaSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjU6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9mYXEiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764652515),
('mBFbLYfTSRpdaRcIAuQyFe4u4bZSBSqasoGrTAp0',NULL,'185.13.98.57','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.3','YTozOntzOjY6Il90b2tlbiI7czo0MDoicjZvSVMwYUtnTjVQVUswdUhvYnRJdHQ1Sm0xZHBrQlZYM3VudHg3eCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764661924),
('MmmkPm9l0B4g84wdVyKJEWlzitatj0nKD6S28gCH',NULL,'52.205.113.104','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot) Chrome/119.0.6045.214 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUmFFVTk1ZmpmYUJ1YzZQMkI3Qk82OVJJVDFPSmVCd2dFeFZRMU9aNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpY2luZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764665544),
('mqx4UTszDH5qCNy23USElfoWtTtGvhJ9Fw7WeLSR',NULL,'168.199.82.50','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaTZ6UTBjR0NEUGFLcHQ1Y0diSHp3eXJXa0ZaWUJ2QXBzZG9aeXF0VCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpY2luZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764631682),
('NiewHz55jZ70uWKB1rx8x5K40ytRFi5T6ldF3U41',NULL,'149.107.87.58','Mozilla/5.0 (iPhone; CPU iPhone OS 26_1_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/142.0.7444.148 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTlh3ZXBPY3N0clpMZnl1M2ZwdTIwOVFCNFNpZGppVElydXVMQUFraCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764612202),
('Nx6WfQrNjEQwA6qcSfttzjogW1AEVnpmZhjZxPq6',NULL,'185.137.36.190','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWVJXaEpUeDVJTko2U3RDUzJKRzdyVW1TSWhKU2V6bWVMRWNMcm02bSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpY2luZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764596670),
('odfREnR51MxLXDQiVKlEzzRr2SANxhKovvrLCBJ4',NULL,'85.203.45.196','','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMm5HVDI1a2JTbzZxTXdnSTg1WDF2OVFBVFh5bEhhbzBmVUlYY1d0diI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764591994),
('PchtC200Cirn4I8Dju8ui17B7THXrkdHeqmseuX3',NULL,'3.90.36.60','okhttp/5.3.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiY0tFNTd3VjY2VGxVdHJVdGJtVXNSVUhneVpPa21vTVVxZDVDVW0yNiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764623352),
('pitgz4T8ndVAvaEHv8e4eRhU8GYOXetsfgCBdBpq',NULL,'43.155.140.157','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVdCeTZXb2hWMmFkZmswamhJRENjOUh4T0JaWkE1THpGeDBqWHlnVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9nZHByIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1764672370),
('pVtppodffRuOzJGVJsbV7vXTQA0c25LAF0LN8Kph',NULL,'34.75.252.190','Mozilla/5.0 (compatible; CMS-Checker/1.0; +https://example.com)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOFduTDZzWTc5eHE5czk1Sk50bE16Y3I5S1dwVEZ5MjBxZ003dHdmWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764604570),
('qmNSjPXe4zo7BJHrv5DhfYy0McQGYBFDHusxpHdR',NULL,'43.130.101.151','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWk9XellOWlpOZ2Mya205R1JVeGZlZEhVVk1jMHlZdzluRUUySzNUNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9jb29raWUtcG9saWN5Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1764645426),
('r11pPfNNQODzvkZN6CrS6pbsVdLdzQyNfJjt07Hc',NULL,'98.82.129.133','Slackbot-LinkExpanding 1.0 (+https://api.slack.com/robots)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiN2daaHg1aTFWdmlxcm5LeWkyWXlidVBRMFVsbW5ud2FnWERla1ZBVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764661558),
('RRLq4WTu1eQwiQFaVqTV24Mgun7e13NHM62YEkAJ',NULL,'43.167.245.18','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ1dZbUhkdkNDRjd4U1FocXRocVJmQjVDd3cyWW1kb1pIODZJZ0MweSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764676559),
('skgwL32rzMKlqeI3sIBSJEptGQuCf1UpqFG7XZy7',NULL,'40.77.167.50','Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm) Chrome/116.0.1938.76 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiT0pLNXc3NEZpOHJMcVJZSEhTTFNZcjFvRUFIYm9Ka3gxc1RFMjd4NSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vdGVybXMtYW5kLWNvbmRpdGlvbnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764625179),
('T0zLIFOdKryPHLKilrpyZExXXj1Bp3iI94LWs0qf',NULL,'149.56.150.88','Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjhWNElnYVJJU2J2dE5iaEJrR2hkNXJvMTNaazY3SDNoUFVUS2lWNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764666115),
('t2i3jr5ViojmU3388HtnGJBa85Um5PUUgHsrl78t',NULL,'4.217.181.219','Mozilla/5.0 (iPhone; CPU iPhone OS 15_7_9 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.6.5 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoidFUwYkcxdUtrbE1md2dlaXN3d0R5T2hmbnRHcjBIbFZTQkhhN3F1SCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9wdWJsaWMvaW5kZXgucGhwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1764614248),
('trt3g5iUERkok4JNNOKkZdycUrDtynBynW51Qfuy',NULL,'138.197.216.237','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiYUw2T25XWXNzQXpMN3FEbUtQOHF5eW9rNGNKZnQ3dkhsbE9xbERHeiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764656839),
('tzGb43ct3z0VoLzWY6nZ1OtqU7uwLLZSaXDtzf15',NULL,'43.157.95.239','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoibmJibWdaeERpQjdXbm93clhQWHNlMHE4d09PYW9PTFdTaFdBMWlHcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9jb250YWN0Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1764650774),
('UDsGkj8PEsAw1GXX0SnOz3Lj5OnT70cfZcn3Eh1T',NULL,'147.78.2.87','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWXpteXowZ2poUmxmZjZtNld6OUg2NEtGSFJRTG5MeWNpSGtQSGVIWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764663190),
('v8eRwxXjOOlMl6Fia3iNctb40IS27dEYzYMuY7md',NULL,'54.221.16.217','Mozilla/5.0 (Linux; Android 10; POCO F1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.41 Mobile Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS3NkUVIwNUxDNVByQ2hianlJRUNpcjlndkJmTHoxeHJBMnBqMGY1YSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764665174),
('VN91GFfiKgb7utn9XLCEl9w6TW5rVevloCVjjUvs',NULL,'43.157.38.131','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoieTZON3B5MmRFeG9iWE9DeG1XM3BlTUpJWTJCOXlUM1N5Rm5tS0o1UyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly93d3cud2VidWl6LmNvbS9hYm91dCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764673574),
('VQHcHv2WeQBtG869HGomFhRn9TI0hchjCdWhlpZa',NULL,'18.208.174.226','Mozilla/5.0 (X11; Linux) KHTML/4.9.1 (like Gecko) Konqueror/4.9','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVWp4YW1YS3JhdGd2OEtKVUdTZEd3NGN3MjhDemg3VTU1NVVkckFpdyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764616490),
('VW9fUNC6wurp1ePCmDJ6UK0jnnyFLlhNKts0Z7wq',NULL,'58.49.233.126','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoibWkyRFQ0V0thSm96V1E1SmN5V0tRa0d6elpVbG9NNk1xbTdoRWNmUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764610592),
('whpOVGyHU1Ey961JfmmuWcX01MIrgapvrnmsKUEu',NULL,'54.237.29.105','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/100.0.4896.127 Safari/537.36 OPR/86.0.4363.59','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNjlwU1RUNkcyand0UVE4SjZLYWpRbGVqaVZSV3VNVjZ2RFVXc1FaWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764643505),
('wpCjjDg5ezsgZjJt1Jioauet47Khwv5eShfeuTeH',NULL,'76.50.206.22','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNDJXU2FJdGUyZDc1Ym5SUDZlUEs0eldmMWJVV0xNZ2tGQjg0Y3c4ayI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764656808),
('wpRvTzatRvIwOXyVYRHXrndvuk6ZAsis7QNBy7nT',NULL,'51.195.244.184','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTkdLWGI4dFozQ2QwQ0wzQzZZY24xRVNRYUVLWHBGZEhxV2JDQ29sVCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vZ2RwciI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764654893),
('xlK0rX9p2LoUsPhhRPJH7HPmAzgJDTt30rfxF41t',NULL,'99.80.127.142','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.80 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZkpBU0laZWdBbktxa3pmcm11dUtvT0c4RG80ZHpUdnZQd25qZXpJeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764651666),
('ypurN3B4sadDyqUkWZL5KRRHbzHfLlUbdCxbKyJM',NULL,'182.69.118.237','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOEtOY2JscUZyMlJrYTZrWmo3MTJnUk5jTHZLeHdJaU5ZcFBmQkhGNSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpY2luZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764657892),
('yUkpfGM7lSvxVbtPYBitPveVJOzvvo7icP6a2H1U',NULL,'43.131.39.179','Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoieGZOMDc5Z0l6QTZySXRWN09mM2loZkNlTE14eDdNVkpSNmRYTzRMUSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly93d3cud2VidWl6LmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764672328),
('YwQ2CtTiUqfyMSW93f8HYj12AL02w1nP3VHxwmT4',NULL,'66.249.70.132','Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.84 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiSjc5NzRWR3VncXFxQ2FoUFVKZjhheENneGdnbkp1SEtRTGFqTEllMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpY2luZyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1764612178),
('zsdqbwiEgKFDe88cU3t6AYE5paGKoqPxEGazQcKh',NULL,'198.244.226.157','Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)','YTozOntzOjY6Il90b2tlbiI7czo0MDoiS3NKNzBhTUxxN0NRVjNLUlc1T3J6c0UxSnpZOUQxNDNYWVlUcDR4YSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHBzOi8vd3d3LndlYnVpei5jb20vcHJpdmFjeS1wb2xpY3kiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764610412),
('ztgHk0zq85AQcOHMMSzvALG7sbPKgo2wPbBA3i6d',NULL,'185.108.129.214','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQTdleThtWWNHSkFvRkZNNEZiRTlCSjlkN1JCSFl3dUgzMDd4eEJRRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjI6Imh0dHBzOi8vd3d3LndlYnVpei5jb20iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1764672861);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-02 12:03:24
