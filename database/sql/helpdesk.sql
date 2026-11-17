-- =====================================================================
-- HelpDesk – IT Support Ticketing System
-- Skrip SQL lengkap (struktur + data awal) untuk MySQL 8.x
--
-- Cara pakai:
--   mysql -u root -p < database/sql/helpdesk.sql
-- atau impor lewat phpMyAdmin / HeidiSQL.
--
-- Akun demo (password semua: password)
--   admin@helpdesk.test     (Administrator)
--   tech1@helpdesk.test     (Technician)
--   tech2@helpdesk.test     (Technician)
--   student1@helpdesk.test  (Requester)
--   student2@helpdesk.test  (Requester)
--
-- Skrip ini dihasilkan dari migration + seeder Laravel
-- (php artisan migrate:fresh --seed), sehingga identik dengan hasilnya.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `helpdesk` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `helpdesk`;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Hardware','Kerusakan perangkat keras: PC, laptop, printer, proyektor.',1,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(2,'Software','Instalasi, lisensi, atau error pada aplikasi.',1,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(3,'Network','Gangguan koneksi internet, Wi-Fi, atau jaringan lokal.',1,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(4,'Account & Access','Masalah login, reset password, hak akses sistem informasi.',1,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(5,'Laboratory Equipment','Perangkat dan fasilitas laboratorium komputer.',1,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(6,'Other','Permasalahan lain yang tidak termasuk kategori di atas.',1,'2026-11-17 02:36:59','2026-11-17 02:36:59');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_ticket_id_foreign` (`ticket_id`),
  KEY `comments_user_id_foreign` (`user_id`),
  CONSTRAINT `comments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,1,2,'Kabel VGA sudah diganti.','2026-11-17 02:36:59','2026-11-17 02:36:59'),(2,3,3,'Password sudah direset, silakan cek email kampus.','2026-11-17 02:36:59','2026-11-17 02:36:59'),(3,4,2,'Access point lantai 2 sudah di-restart dan firmware diperbarui.','2026-11-17 02:36:59','2026-11-17 02:36:59'),(4,5,2,'Lampu proyektor kemungkinan habis, sedang dicek stok.','2026-11-17 02:36:59','2026-11-17 02:36:59'),(5,5,4,'Terima kasih, kelas praktikum besok pagi butuh proyektor ini.','2026-11-17 02:36:59','2026-11-17 02:36:59');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_11_17_000001_create_categories_table',1),(5,'2026_11_17_000002_create_tickets_table',1),(6,'2026_11_17_000003_create_comments_table',1),(7,'2026_11_17_000004_create_ticket_activities_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ticket_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_value` text COLLATE utf8mb4_unicode_ci,
  `new_value` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ticket_activities_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_activities_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_activities_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ticket_activities` WRITE;
/*!40000 ALTER TABLE `ticket_activities` DISABLE KEYS */;
INSERT INTO `ticket_activities` VALUES (1,1,4,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(2,1,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-11-17 02:36:59'),(3,1,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-17 02:36:59'),(4,1,2,'commented',NULL,NULL,'Comment added','2026-11-17 02:36:59'),(5,1,2,'status_changed','In Progress','Resolved','Ticket resolved','2026-11-17 02:36:59'),(6,1,4,'status_changed','Resolved','Closed','Ticket closed','2026-11-17 02:36:59'),(7,2,5,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(8,2,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-11-17 02:36:59'),(9,2,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-17 02:36:59'),(10,2,3,'status_changed','In Progress','Resolved','Ticket resolved','2026-11-17 02:36:59'),(11,2,5,'status_changed','Resolved','Closed','Ticket closed','2026-11-17 02:36:59'),(12,3,4,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(13,3,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-11-17 02:36:59'),(14,3,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-17 02:36:59'),(15,3,3,'commented',NULL,NULL,'Comment added','2026-11-17 02:36:59'),(16,3,3,'status_changed','In Progress','Resolved','Ticket resolved','2026-11-17 02:36:59'),(17,4,5,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(18,4,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-11-17 02:36:59'),(19,4,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-17 02:36:59'),(20,4,2,'commented',NULL,NULL,'Comment added','2026-11-17 02:36:59'),(21,4,2,'status_changed','In Progress','Resolved','Ticket resolved','2026-11-17 02:36:59'),(22,5,4,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(23,5,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-11-17 02:36:59'),(24,5,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-17 02:36:59'),(25,5,2,'commented',NULL,NULL,'Comment added','2026-11-17 02:36:59'),(26,6,5,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(27,6,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-11-17 02:36:59'),(28,6,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-17 02:36:59'),(29,7,4,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(30,7,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-11-17 02:36:59'),(31,8,5,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(32,8,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-11-17 02:36:59'),(33,9,4,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(34,10,5,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(35,11,4,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(36,12,5,'created',NULL,'Open','Ticket created','2026-11-17 02:36:59'),(37,5,4,'commented',NULL,NULL,'Comment added','2026-11-17 02:36:59'),(38,5,2,'priority_changed','High','Critical','Priority changed from High to Critical','2026-11-17 02:36:59');
/*!40000 ALTER TABLE `ticket_activities` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `technician_id` bigint unsigned DEFAULT NULL,
  `category_id` bigint unsigned NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` enum('open','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_ticket_number_unique` (`ticket_number`),
  KEY `tickets_user_id_foreign` (`user_id`),
  KEY `tickets_technician_id_foreign` (`technician_id`),
  KEY `tickets_category_id_foreign` (`category_id`),
  KEY `tickets_priority_index` (`priority`),
  KEY `tickets_status_index` (`status`),
  CONSTRAINT `tickets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `tickets_technician_id_foreign` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,'TCK-20261117-00001',4,2,1,'Monitor PC Lab 2 berkedip terus','Laporan demo #1: Monitor PC Lab 2 berkedip terus. Mohon segera dicek oleh tim IT.','medium','closed',NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59','2026-11-17 02:36:59','2026-11-17 02:36:59'),(2,'TCK-20261117-00002',5,3,2,'Microsoft Office minta aktivasi ulang','Laporan demo #2: Microsoft Office minta aktivasi ulang. Mohon segera dicek oleh tim IT.','low','closed',NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59','2026-11-17 02:36:59','2026-11-17 02:36:59'),(3,'TCK-20261117-00003',4,3,4,'Tidak bisa login SIAKAD','Laporan demo #3: Tidak bisa login SIAKAD. Mohon segera dicek oleh tim IT.','high','resolved',NULL,'2026-11-17 02:36:59',NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(4,'TCK-20261117-00004',5,2,3,'Wi-Fi perpustakaan sering terputus','Laporan demo #4: Wi-Fi perpustakaan sering terputus. Mohon segera dicek oleh tim IT.','high','resolved',NULL,'2026-11-17 02:36:59',NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(5,'TCK-20261117-00005',4,2,5,'Proyektor Lab Jaringan tidak menyala','Laporan demo #5: Proyektor Lab Jaringan tidak menyala. Mohon segera dicek oleh tim IT.','critical','in_progress',NULL,NULL,NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(6,'TCK-20261117-00006',5,3,2,'Android Studio error saat build','Laporan demo #6: Android Studio error saat build. Mohon segera dicek oleh tim IT.','medium','in_progress',NULL,NULL,NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(7,'TCK-20261117-00007',4,3,3,'Kabel LAN meja 14 Lab 1 tidak terdeteksi','Laporan demo #7: Kabel LAN meja 14 Lab 1 tidak terdeteksi. Mohon segera dicek oleh tim IT.','medium','open',NULL,NULL,NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(8,'TCK-20261117-00008',5,2,1,'Keyboard PC 07 beberapa tombol macet','Laporan demo #8: Keyboard PC 07 beberapa tombol macet. Mohon segera dicek oleh tim IT.','low','open',NULL,NULL,NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(9,'TCK-20261117-00009',4,NULL,4,'Email kampus penuh, tidak bisa menerima pesan','Laporan demo #9: Email kampus penuh, tidak bisa menerima pesan. Mohon segera dicek oleh tim IT.','medium','open',NULL,NULL,NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(10,'TCK-20261117-00010',5,NULL,5,'AC Lab Multimedia bocor di atas meja','Laporan demo #10: AC Lab Multimedia bocor di atas meja. Mohon segera dicek oleh tim IT.','high','open',NULL,NULL,NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(11,'TCK-20261117-00011',4,NULL,6,'Permintaan instalasi software statistik (SPSS)','Laporan demo #11: Permintaan instalasi software statistik (SPSS). Mohon segera dicek oleh tim IT.','low','open',NULL,NULL,NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(12,'TCK-20261117-00012',5,NULL,3,'Internet lambat di gedung B','Laporan demo #12: Internet lambat di gedung B. Mohon segera dicek oleh tim IT.','critical','open',NULL,NULL,NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59');
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','technician','requester') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'requester',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@helpdesk.test','2026-11-17 02:36:59','$2y$12$whiW8Q.UouvaWANAaMTrw.IuFGXZE10ZwlUj8oJnwWWWcW7Rmlidy','admin',NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(2,'Budi Teknisi','tech1@helpdesk.test','2026-11-17 02:36:59','$2y$12$whiW8Q.UouvaWANAaMTrw.IuFGXZE10ZwlUj8oJnwWWWcW7Rmlidy','technician',NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(3,'Sari Teknisi','tech2@helpdesk.test','2026-11-17 02:36:59','$2y$12$whiW8Q.UouvaWANAaMTrw.IuFGXZE10ZwlUj8oJnwWWWcW7Rmlidy','technician',NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(4,'Andi Mahasiswa','student1@helpdesk.test','2026-11-17 02:36:59','$2y$12$whiW8Q.UouvaWANAaMTrw.IuFGXZE10ZwlUj8oJnwWWWcW7Rmlidy','requester',NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59'),(5,'Rina Mahasiswa','student2@helpdesk.test','2026-11-17 02:36:59','$2y$12$whiW8Q.UouvaWANAaMTrw.IuFGXZE10ZwlUj8oJnwWWWcW7Rmlidy','requester',NULL,'2026-11-17 02:36:59','2026-11-17 02:36:59');
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


-- Tabel pendukung Laravel (struktur saja, tanpa data)

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

