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
INSERT INTO `categories` VALUES (1,'Hardware','Kerusakan perangkat keras: PC, laptop, printer, proyektor.',1,'2026-11-17 02:24:33','2026-11-17 02:24:33'),(2,'Software','Instalasi, lisensi, atau error pada aplikasi.',1,'2026-11-17 02:24:33','2026-11-17 02:24:33'),(3,'Network','Gangguan koneksi internet, Wi-Fi, atau jaringan lokal.',1,'2026-11-17 02:24:33','2026-11-17 02:24:33'),(4,'Account & Access','Masalah login, reset password, hak akses sistem informasi.',1,'2026-11-17 02:24:33','2026-11-17 02:24:33'),(5,'Laboratory Equipment','Perangkat dan fasilitas laboratorium komputer.',1,'2026-11-17 02:24:33','2026-11-17 02:24:33'),(6,'Other','Permasalahan lain yang tidak termasuk kategori di atas.',1,'2026-11-17 02:24:33','2026-11-17 02:24:33');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,1,2,'Kabel VGA sudah diganti.','2026-09-20 21:30:00','2026-09-20 21:30:00'),(2,3,2,'Server captive portal sudah di-restart.','2026-09-28 07:44:00','2026-09-28 07:44:00'),(3,5,3,'10 mouse baru sudah dipasang.','2026-10-09 05:58:00','2026-10-09 05:58:00'),(4,7,3,'Python 3.12 sudah diinstal di semua PC.','2026-10-15 20:12:00','2026-10-15 20:12:00'),(5,8,2,'Switch utama mati, sudah diganti unit cadangan.','2026-10-20 12:19:00','2026-10-20 12:19:00'),(6,9,3,'Password sudah direset, silakan cek email kampus.','2026-10-26 07:26:00','2026-10-26 07:26:00'),(7,10,2,'Access point lantai 2 sudah di-restart dan firmware diperbarui.','2026-11-01 12:33:00','2026-11-01 12:33:00'),(8,12,2,'Power supply rusak, sudah diganti.','2026-11-09 10:47:00','2026-11-09 10:47:00'),(9,13,2,'Lampu proyektor kemungkinan habis, sedang dicek stok.','2026-11-12 10:54:00','2026-11-12 10:54:00'),(10,13,4,'Terima kasih, kelas praktikum besok pagi butuh proyektor ini.','2026-11-13 02:24:33','2026-11-13 02:24:33');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_11_17_000001_create_categories_table',1),(5,'2026_11_17_000002_create_tickets_table',1),(6,'2026_11_17_000003_create_comments_table',1),(7,'2026_11_17_000004_create_ticket_activities_table',1),(8,'2026_11_18_000001_add_attachment_name_to_tickets_table',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ticket_activities` WRITE;
/*!40000 ALTER TABLE `ticket_activities` DISABLE KEYS */;
INSERT INTO `ticket_activities` VALUES (1,1,4,'created',NULL,'Open','Ticket created','2026-09-20 01:00:00'),(2,1,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-09-20 01:30:00'),(3,1,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-09-20 11:30:00'),(4,1,2,'commented',NULL,NULL,'Comment added','2026-09-20 21:30:00'),(5,1,2,'status_changed','In Progress','Resolved','Ticket resolved','2026-09-21 07:30:00'),(6,1,4,'status_changed','Resolved','Closed','Ticket closed','2026-09-22 01:30:00'),(7,2,5,'created',NULL,'Open','Ticket created','2026-09-23 02:07:00'),(8,2,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-09-23 02:37:00'),(9,2,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-09-23 17:37:00'),(10,2,3,'status_changed','In Progress','Resolved','Ticket resolved','2026-09-24 08:37:00'),(11,2,5,'status_changed','Resolved','Closed','Ticket closed','2026-09-25 02:37:00'),(12,3,4,'created',NULL,'Open','Ticket created','2026-09-28 03:14:00'),(13,3,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-09-28 03:44:00'),(14,3,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-09-28 05:44:00'),(15,3,2,'commented',NULL,NULL,'Comment added','2026-09-28 07:44:00'),(16,3,2,'status_changed','In Progress','Resolved','Ticket resolved','2026-09-28 09:44:00'),(17,3,4,'status_changed','Resolved','Closed','Ticket closed','2026-09-29 03:44:00'),(18,4,5,'created',NULL,'Open','Ticket created','2026-10-02 04:21:00'),(19,4,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-10-02 04:51:00'),(20,4,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-10-02 05:51:00'),(21,4,3,'status_changed','In Progress','Resolved','Ticket resolved','2026-10-02 06:51:00'),(22,4,5,'status_changed','Resolved','Closed','Ticket closed','2026-10-03 00:51:00'),(23,5,4,'created',NULL,'Open','Ticket created','2026-10-07 05:28:00'),(24,5,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-10-07 05:58:00'),(25,5,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-10-08 05:58:00'),(26,5,3,'commented',NULL,NULL,'Comment added','2026-10-09 05:58:00'),(27,5,3,'status_changed','In Progress','Resolved','Ticket resolved','2026-10-10 05:58:00'),(28,5,4,'status_changed','Resolved','Closed','Ticket closed','2026-10-10 23:58:00'),(29,6,5,'created',NULL,'Open','Ticket created','2026-10-11 06:35:00'),(30,6,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-10-11 07:05:00'),(31,6,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-10-11 10:05:00'),(32,6,2,'status_changed','In Progress','Resolved','Ticket resolved','2026-10-11 13:05:00'),(33,6,5,'status_changed','Resolved','Closed','Ticket closed','2026-10-12 07:05:00'),(34,7,4,'created',NULL,'Open','Ticket created','2026-10-15 07:42:00'),(35,7,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-10-15 08:12:00'),(36,7,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-10-15 14:12:00'),(37,7,3,'commented',NULL,NULL,'Comment added','2026-10-15 20:12:00'),(38,7,3,'status_changed','In Progress','Resolved','Ticket resolved','2026-10-16 02:12:00'),(39,7,4,'status_changed','Resolved','Closed','Ticket closed','2026-10-16 20:12:00'),(40,8,5,'created',NULL,'Open','Ticket created','2026-10-20 08:49:00'),(41,8,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-10-20 09:19:00'),(42,8,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-10-20 10:49:00'),(43,8,2,'commented',NULL,NULL,'Comment added','2026-10-20 12:19:00'),(44,8,2,'status_changed','In Progress','Resolved','Ticket resolved','2026-10-20 13:49:00'),(45,9,4,'created',NULL,'Open','Ticket created','2026-10-26 01:56:00'),(46,9,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-10-26 02:26:00'),(47,9,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-10-26 04:56:00'),(48,9,3,'commented',NULL,NULL,'Comment added','2026-10-26 07:26:00'),(49,9,3,'status_changed','In Progress','Resolved','Ticket resolved','2026-10-26 09:56:00'),(50,10,5,'created',NULL,'Open','Ticket created','2026-11-01 02:03:00'),(51,10,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-11-01 02:33:00'),(52,10,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-01 07:33:00'),(53,10,2,'commented',NULL,NULL,'Comment added','2026-11-01 12:33:00'),(54,10,2,'status_changed','In Progress','Resolved','Ticket resolved','2026-11-01 17:33:00'),(55,11,4,'created',NULL,'Open','Ticket created','2026-11-05 03:10:00'),(56,11,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-11-05 03:40:00'),(57,11,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-05 15:40:00'),(58,11,3,'status_changed','In Progress','Resolved','Ticket resolved','2026-11-06 03:40:00'),(59,12,5,'created',NULL,'Open','Ticket created','2026-11-09 04:17:00'),(60,12,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-11-09 04:47:00'),(61,12,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-09 07:47:00'),(62,12,2,'commented',NULL,NULL,'Comment added','2026-11-09 10:47:00'),(63,12,2,'status_changed','In Progress','Resolved','Ticket resolved','2026-11-09 13:47:00'),(64,13,4,'created',NULL,'Open','Ticket created','2026-11-12 05:24:00'),(65,13,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-11-12 05:54:00'),(66,13,2,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-12 09:54:00'),(67,13,2,'commented',NULL,NULL,'Comment added','2026-11-12 10:54:00'),(68,14,5,'created',NULL,'Open','Ticket created','2026-11-13 06:31:00'),(69,14,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-11-13 07:01:00'),(70,14,3,'status_changed','Open','In Progress','Status changed to In Progress','2026-11-13 10:01:00'),(71,15,4,'created',NULL,'Open','Ticket created','2026-11-14 07:38:00'),(72,15,1,'assigned',NULL,'Sari Teknisi','Ticket assigned to Sari Teknisi','2026-11-14 08:08:00'),(73,16,5,'created',NULL,'Open','Ticket created','2026-11-15 08:45:00'),(74,16,1,'assigned',NULL,'Budi Teknisi','Ticket assigned to Budi Teknisi','2026-11-15 09:15:00'),(75,17,4,'created',NULL,'Open','Ticket created','2026-11-15 01:52:00'),(76,18,5,'created',NULL,'Open','Ticket created','2026-11-16 02:59:00'),(77,19,4,'created',NULL,'Open','Ticket created','2026-11-16 03:06:00'),(78,20,5,'created',NULL,'Open','Ticket created','2026-11-17 02:24:33'),(79,13,4,'commented',NULL,NULL,'Comment added','2026-11-13 02:24:33'),(80,13,2,'priority_changed','High','Critical','Priority changed from High to Critical','2026-11-13 02:24:33');
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
  `attachment_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  KEY `tickets_created_at_index` (`created_at`),
  CONSTRAINT `tickets_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `tickets_technician_id_foreign` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,'TCK-20260920-00001',4,2,1,'Monitor PC Lab 2 berkedip terus','Laporan demo #1: Monitor PC Lab 2 berkedip terus. Mohon segera dicek oleh tim IT.','medium','closed',NULL,NULL,'2026-09-21 07:30:00','2026-09-22 01:30:00','2026-09-20 01:00:00','2026-09-22 01:30:00'),(2,'TCK-20260923-00002',5,3,2,'Microsoft Office minta aktivasi ulang','Laporan demo #2: Microsoft Office minta aktivasi ulang. Mohon segera dicek oleh tim IT.','low','closed',NULL,NULL,'2026-09-24 08:37:00','2026-09-25 02:37:00','2026-09-23 02:07:00','2026-09-25 02:37:00'),(3,'TCK-20260928-00003',4,2,3,'Wi-Fi gedung A tidak bisa login portal','Laporan demo #3: Wi-Fi gedung A tidak bisa login portal. Mohon segera dicek oleh tim IT.','high','closed',NULL,NULL,'2026-09-28 09:44:00','2026-09-29 03:44:00','2026-09-28 03:14:00','2026-09-29 03:44:00'),(4,'TCK-20261002-00004',5,3,4,'Lupa password e-learning','Laporan demo #4: Lupa password e-learning. Mohon segera dicek oleh tim IT.','medium','closed',NULL,NULL,'2026-10-02 06:51:00','2026-10-03 00:51:00','2026-10-02 04:21:00','2026-10-03 00:51:00'),(5,'TCK-20261007-00005',4,3,5,'Mouse Lab 3 banyak yang rusak','Laporan demo #5: Mouse Lab 3 banyak yang rusak. Mohon segera dicek oleh tim IT.','low','closed',NULL,NULL,'2026-10-10 05:58:00','2026-10-10 23:58:00','2026-10-07 05:28:00','2026-10-10 23:58:00'),(6,'TCK-20261011-00006',5,2,1,'Printer lantai 1 paper jam','Laporan demo #6: Printer lantai 1 paper jam. Mohon segera dicek oleh tim IT.','medium','closed',NULL,NULL,'2026-10-11 13:05:00','2026-10-12 07:05:00','2026-10-11 06:35:00','2026-10-12 07:05:00'),(7,'TCK-20261015-00007',4,3,2,'Python tidak terinstal di PC Lab 1','Laporan demo #7: Python tidak terinstal di PC Lab 1. Mohon segera dicek oleh tim IT.','high','closed',NULL,NULL,'2026-10-16 02:12:00','2026-10-16 20:12:00','2026-10-15 07:42:00','2026-10-16 20:12:00'),(8,'TCK-20261020-00008',5,2,3,'Internet putus total di Lab Jaringan','Laporan demo #8: Internet putus total di Lab Jaringan. Mohon segera dicek oleh tim IT.','critical','resolved',NULL,NULL,'2026-10-20 13:49:00',NULL,'2026-10-20 08:49:00','2026-10-20 13:49:00'),(9,'TCK-20261026-00009',4,3,4,'Tidak bisa login SIAKAD','Laporan demo #9: Tidak bisa login SIAKAD. Mohon segera dicek oleh tim IT.','high','resolved',NULL,NULL,'2026-10-26 09:56:00',NULL,'2026-10-26 01:56:00','2026-10-26 09:56:00'),(10,'TCK-20261101-00010',5,2,3,'Wi-Fi perpustakaan sering terputus','Laporan demo #10: Wi-Fi perpustakaan sering terputus. Mohon segera dicek oleh tim IT.','high','resolved',NULL,NULL,'2026-11-01 17:33:00',NULL,'2026-11-01 02:03:00','2026-11-01 17:33:00'),(11,'TCK-20261105-00011',4,3,6,'Permintaan akun VPN untuk penelitian','Laporan demo #11: Permintaan akun VPN untuk penelitian. Mohon segera dicek oleh tim IT.','low','resolved',NULL,NULL,'2026-11-06 03:40:00',NULL,'2026-11-05 03:10:00','2026-11-06 03:40:00'),(12,'TCK-20261109-00012',5,2,1,'PC dosen di ruang 204 tidak menyala','Laporan demo #12: PC dosen di ruang 204 tidak menyala. Mohon segera dicek oleh tim IT.','high','resolved',NULL,NULL,'2026-11-09 13:47:00',NULL,'2026-11-09 04:17:00','2026-11-09 13:47:00'),(13,'TCK-20261112-00013',4,2,5,'Proyektor Lab Jaringan tidak menyala','Laporan demo #13: Proyektor Lab Jaringan tidak menyala. Mohon segera dicek oleh tim IT.','critical','in_progress',NULL,NULL,NULL,NULL,'2026-11-12 05:24:00','2026-11-13 02:24:33'),(14,'TCK-20261113-00014',5,3,2,'Android Studio error saat build','Laporan demo #14: Android Studio error saat build. Mohon segera dicek oleh tim IT.','medium','in_progress',NULL,NULL,NULL,NULL,'2026-11-13 06:31:00','2026-11-13 10:01:00'),(15,'TCK-20261114-00015',4,3,3,'Kabel LAN meja 14 Lab 1 tidak terdeteksi','Laporan demo #15: Kabel LAN meja 14 Lab 1 tidak terdeteksi. Mohon segera dicek oleh tim IT.','medium','open',NULL,NULL,NULL,NULL,'2026-11-14 07:38:00','2026-11-14 08:08:00'),(16,'TCK-20261115-00016',5,2,1,'Keyboard PC 07 beberapa tombol macet','Laporan demo #16: Keyboard PC 07 beberapa tombol macet. Mohon segera dicek oleh tim IT.','low','open',NULL,NULL,NULL,NULL,'2026-11-15 08:45:00','2026-11-15 09:15:00'),(17,'TCK-20261115-00017',4,NULL,4,'Email kampus penuh, tidak bisa menerima pesan','Laporan demo #17: Email kampus penuh, tidak bisa menerima pesan. Mohon segera dicek oleh tim IT.','medium','open',NULL,NULL,NULL,NULL,'2026-11-15 01:52:00','2026-11-15 01:52:00'),(18,'TCK-20261116-00018',5,NULL,5,'AC Lab Multimedia bocor di atas meja','Laporan demo #18: AC Lab Multimedia bocor di atas meja. Mohon segera dicek oleh tim IT.','high','open',NULL,NULL,NULL,NULL,'2026-11-16 02:59:00','2026-11-16 02:59:00'),(19,'TCK-20261116-00019',4,NULL,6,'Permintaan instalasi software statistik (SPSS)','Laporan demo #19: Permintaan instalasi software statistik (SPSS). Mohon segera dicek oleh tim IT.','low','open',NULL,NULL,NULL,NULL,'2026-11-16 03:06:00','2026-11-16 03:06:00'),(20,'TCK-20261117-00020',5,NULL,3,'Internet lambat di gedung B','Laporan demo #20: Internet lambat di gedung B. Mohon segera dicek oleh tim IT.','critical','open',NULL,NULL,NULL,NULL,'2026-11-17 02:24:33','2026-11-17 02:24:33');
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
INSERT INTO `users` VALUES (1,'Administrator','admin@helpdesk.test','2026-11-17 02:24:33','$2y$12$HZgISwfv0ui9q2P/TzoXX.geMHIdwdLgsbZBOr0nFC3nYxK503CRW','admin',NULL,'2026-11-17 02:24:33','2026-11-17 02:24:33'),(2,'Budi Teknisi','tech1@helpdesk.test','2026-11-17 02:24:33','$2y$12$HZgISwfv0ui9q2P/TzoXX.geMHIdwdLgsbZBOr0nFC3nYxK503CRW','technician',NULL,'2026-11-17 02:24:33','2026-11-17 02:24:33'),(3,'Sari Teknisi','tech2@helpdesk.test','2026-11-17 02:24:33','$2y$12$HZgISwfv0ui9q2P/TzoXX.geMHIdwdLgsbZBOr0nFC3nYxK503CRW','technician',NULL,'2026-11-17 02:24:33','2026-11-17 02:24:33'),(4,'Andi Mahasiswa','student1@helpdesk.test','2026-11-17 02:24:33','$2y$12$HZgISwfv0ui9q2P/TzoXX.geMHIdwdLgsbZBOr0nFC3nYxK503CRW','requester',NULL,'2026-11-17 02:24:33','2026-11-17 02:24:33'),(5,'Rina Mahasiswa','student2@helpdesk.test','2026-11-17 02:24:33','$2y$12$HZgISwfv0ui9q2P/TzoXX.geMHIdwdLgsbZBOr0nFC3nYxK503CRW','requester',NULL,'2026-11-17 02:24:33','2026-11-17 02:24:33');
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

