-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: gudang
-- ------------------------------------------------------
-- Server version	8.0.30

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

--
-- Table structure for table `cache`
--

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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
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
-- Table structure for table `hutang`
--

DROP TABLE IF EXISTS `hutang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hutang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `no_faktur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` bigint unsigned NOT NULL,
  `pembayaran` decimal(15,2) DEFAULT NULL,
  `kekurangan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `jatuh_tempo` date DEFAULT NULL,
  `status` enum('Belum Lunas','Lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hutang`
--

LOCK TABLES `hutang` WRITE;
/*!40000 ALTER TABLE `hutang` DISABLE KEYS */;
INSERT INTO `hutang` VALUES (1,'CASH','2025-01-08','0125001',27000,0.00,27000.00,'2025-02-14','Belum Lunas',NULL,NULL);
/*!40000 ALTER TABLE `hutang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hutang_histories`
--

DROP TABLE IF EXISTS `hutang_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hutang_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hutang_id` bigint unsigned NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `pembayaran` decimal(15,2) DEFAULT NULL,
  `kekurangan` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hutang_histories_hutang_id_foreign` (`hutang_id`),
  CONSTRAINT `hutang_histories_hutang_id_foreign` FOREIGN KEY (`hutang_id`) REFERENCES `hutang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hutang_histories`
--

LOCK TABLES `hutang_histories` WRITE;
/*!40000 ALTER TABLE `hutang_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `hutang_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_pembelian`
--

DROP TABLE IF EXISTS `item_pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_pembelian` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pembelian_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `qty` int NOT NULL,
  `harga` decimal(20,2) NOT NULL,
  `diskon` decimal(5,2) NOT NULL DEFAULT '0.00',
  `jumlah` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_pembelian_pembelian_id_foreign` (`pembelian_id`),
  KEY `item_pembelian_produk_id_foreign` (`produk_id`),
  CONSTRAINT `item_pembelian_pembelian_id_foreign` FOREIGN KEY (`pembelian_id`) REFERENCES `pembelian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_pembelian_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_pembelian`
--

LOCK TABLES `item_pembelian` WRITE;
/*!40000 ALTER TABLE `item_pembelian` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_pembelian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_penjualan`
--

DROP TABLE IF EXISTS `item_penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_penjualan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `penjualan_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `qty` int NOT NULL,
  `harga` decimal(20,2) NOT NULL,
  `diskon` decimal(5,2) NOT NULL DEFAULT '0.00',
  `jumlah` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_penjualan_penjualan_id_foreign` (`penjualan_id`),
  KEY `item_penjualan_produk_id_foreign` (`produk_id`),
  CONSTRAINT `item_penjualan_penjualan_id_foreign` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_penjualan_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_penjualan`
--

LOCK TABLES `item_penjualan` WRITE;
/*!40000 ALTER TABLE `item_penjualan` DISABLE KEYS */;
INSERT INTO `item_penjualan` VALUES (11,2,4,1,8000.00,0.00,8000,'2025-01-15 12:26:52','2025-01-15 12:26:52'),(12,3,4,1,20000.00,0.00,20000,'2025-01-15 12:42:15','2025-01-15 12:42:15'),(13,4,4,1,10000.00,0.00,10000,'2025-01-15 12:52:19','2025-01-15 12:52:19'),(14,11,4,1,20000.00,0.00,20000,'2025-01-15 17:21:23','2025-01-15 17:21:23'),(15,12,4,1,10000.00,0.00,10000,'2025-01-15 17:24:29','2025-01-15 17:24:29');
/*!40000 ALTER TABLE `item_penjualan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laporan_keuntungan`
--

DROP TABLE IF EXISTS `laporan_keuntungan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laporan_keuntungan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `total_transaksi` int unsigned NOT NULL,
  `total_modal` decimal(15,2) DEFAULT NULL,
  `total_penjualan` decimal(15,2) DEFAULT NULL,
  `total_keuntungan` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laporan_keuntungan`
--

LOCK TABLES `laporan_keuntungan` WRITE;
/*!40000 ALTER TABLE `laporan_keuntungan` DISABLE KEYS */;
INSERT INTO `laporan_keuntungan` VALUES (1,'2025-01-15',1,15000.00,0.00,-15000.00,'2025-01-15 12:00:27','2025-01-15 12:00:27'),(2,'2025-01-15',1,3700.00,8000.00,4300.00,'2025-01-15 12:26:52','2025-01-15 12:26:52'),(3,'2025-01-15',1,3700.00,20000.00,16300.00,'2025-01-15 12:42:15','2025-01-15 12:42:15'),(4,'2025-01-15',1,3700.00,10000.00,6300.00,'2025-01-15 12:52:19','2025-01-15 12:52:19'),(5,'2025-01-16',1,3700.00,20000.00,16300.00,'2025-01-15 17:21:23','2025-01-15 17:21:23'),(6,'2025-01-16',1,3700.00,10000.00,6300.00,'2025-01-15 17:24:29','2025-01-15 17:24:29');
/*!40000 ALTER TABLE `laporan_keuntungan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laporan_keuntungan_detail`
--

DROP TABLE IF EXISTS `laporan_keuntungan_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laporan_keuntungan_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `laporan_keuntungan_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned DEFAULT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` int NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `harga_jual` decimal(15,2) NOT NULL,
  `keuntungan` decimal(15,2) NOT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `kode_produk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `laporan_keuntungan_detail_laporan_keuntungan_id_foreign` (`laporan_keuntungan_id`),
  KEY `laporan_keuntungan_detail_produk_id_foreign` (`produk_id`),
  CONSTRAINT `laporan_keuntungan_detail_laporan_keuntungan_id_foreign` FOREIGN KEY (`laporan_keuntungan_id`) REFERENCES `laporan_keuntungan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `laporan_keuntungan_detail_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laporan_keuntungan_detail`
--

LOCK TABLES `laporan_keuntungan_detail` WRITE;
/*!40000 ALTER TABLE `laporan_keuntungan_detail` DISABLE KEYS */;
INSERT INTO `laporan_keuntungan_detail` VALUES (1,1,NULL,'KB Y6018',4,3750.00,0.00,-15000.00,NULL,NULL,NULL,'2025-01-15 12:00:27','2025-01-15 12:00:27'),(2,2,4,'KBY001600',1,3700.00,8000.00,4300.00,NULL,NULL,NULL,'2025-01-15 12:26:52','2025-01-15 12:26:52'),(3,3,4,'KBY001600',1,3700.00,20000.00,16300.00,NULL,NULL,NULL,'2025-01-15 12:42:15','2025-01-15 12:42:15'),(4,4,4,'KBY001600',1,3700.00,10000.00,6300.00,NULL,NULL,NULL,'2025-01-15 12:52:19','2025-01-15 12:52:19'),(5,5,4,'KBY001600',1,3700.00,20000.00,16300.00,NULL,NULL,NULL,'2025-01-15 17:21:23','2025-01-15 17:21:23'),(6,6,4,'KBY001600',1,3700.00,10000.00,6300.00,NULL,NULL,NULL,'2025-01-15 17:24:29','2025-01-15 17:24:29');
/*!40000 ALTER TABLE `laporan_keuntungan_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (59,'0001_01_01_000000_create_users_table',1),(60,'2025_01_01_000001_penjualan',1),(61,'2025_01_01_000003_produk',1),(62,'2025_01_01_000005_pembelian',1),(63,'2025_01_01_000006_item_penjualan',1),(64,'2025_01_01_000007_item_pembelian',1),(65,'2025_01_01_214855_hutang',1),(66,'2025_01_01_214855_piutang',1),(67,'2025_01_03_045216_create_cache_table',1),(68,'2025_01_12_154928_create_laporan_keuntungan_table',1),(69,'2025_01_12_154947_create_laporan_keuntungan_detail_table',1),(70,'2025_01_14_213310_create_piutang_histories_table',1),(71,'2025_01_15_070846_create_hutang_histories_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

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

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembelian`
--

DROP TABLE IF EXISTS `pembelian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembelian` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_faktur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `supplier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(20,2) NOT NULL DEFAULT '0.00',
  `ppn` decimal(20,2) DEFAULT NULL,
  `total_harga` decimal(20,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembelian_no_faktur_unique` (`no_faktur`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembelian`
--

LOCK TABLES `pembelian` WRITE;
/*!40000 ALTER TABLE `pembelian` DISABLE KEYS */;
INSERT INTO `pembelian` VALUES (1,'0125001','2025-01-08','CASH',27000.00,0.00,27000.00,'2025-01-15 11:58:52','2025-01-15 11:58:52');
/*!40000 ALTER TABLE `pembelian` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penjualan`
--

DROP TABLE IF EXISTS `penjualan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penjualan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_faktur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `penerima` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_barang` int NOT NULL DEFAULT '0',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ppn` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_harga` decimal(20,2) NOT NULL DEFAULT '0.00',
  `keuntungan` decimal(20,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penjualan_no_faktur_unique` (`no_faktur`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penjualan`
--

LOCK TABLES `penjualan` WRITE;
/*!40000 ALTER TABLE `penjualan` DISABLE KEYS */;
INSERT INTO `penjualan` VALUES (1,'12001','2025-01-15','CASH METRO','METRO',4,253000.00,25300.00,278300.00,0.00,'2025-01-15 12:00:27','2025-01-15 12:11:41'),(2,'0210000','2025-01-15','CASH METRO','METRO',1,8000.00,880.00,8880.00,0.00,'2025-01-15 12:26:52','2025-01-15 12:26:52'),(3,'02100001','2025-01-15','CASH METRO','METRO',1,20000.00,2200.00,22200.00,0.00,'2025-01-15 12:42:15','2025-01-15 12:42:15'),(4,'021000012','2025-01-15','CASH METRO','METRO',1,10000.00,1100.00,11100.00,0.00,'2025-01-15 12:52:19','2025-01-15 12:52:19'),(11,'0210000113','2025-01-16','CASH METRO','METRO',1,20000.00,2200.00,22200.00,0.00,'2025-01-15 17:21:23','2025-01-15 17:21:23'),(12,'021000011378','2025-01-16','CASH METRO','METRO',1,10000.00,1100.00,11100.00,0.00,'2025-01-15 17:24:29','2025-01-15 17:24:29');
/*!40000 ALTER TABLE `penjualan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `piutang`
--

DROP TABLE IF EXISTS `piutang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `piutang` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `no_faktur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `pembayaran` decimal(15,2) NOT NULL DEFAULT '0.00',
  `kekurangan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('Belum Lunas','Lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Lunas',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `piutang`
--

LOCK TABLES `piutang` WRITE;
/*!40000 ALTER TABLE `piutang` DISABLE KEYS */;
INSERT INTO `piutang` VALUES (1,'CASH METRO','2025-01-15','12001',278300.00,278300.00,0.00,'Lunas','2025-01-15 12:00:27','2025-01-15 12:11:41'),(2,'CASH METRO','2025-01-15','0210000',8880.00,0.00,8880.00,'Belum Lunas','2025-01-15 12:26:52','2025-01-15 12:26:52'),(3,'CASH METRO','2025-01-15','02100001',22200.00,22200.00,0.00,'Lunas','2025-01-15 12:42:15','2025-01-15 12:44:29'),(4,'CASH METRO','2025-01-15','021000012',11100.00,5000.00,6100.00,'Belum Lunas','2025-01-15 12:52:19','2025-01-15 12:52:19'),(5,'CASH METRO','2025-01-16','0210000113',22200.00,0.00,22200.00,'Belum Lunas','2025-01-15 17:21:23','2025-01-15 17:21:23'),(6,'CASH METRO','2025-01-16','021000011378',11100.00,5000.00,6100.00,'Belum Lunas','2025-01-15 17:24:29','2025-01-15 17:24:46');
/*!40000 ALTER TABLE `piutang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `piutang_histories`
--

DROP TABLE IF EXISTS `piutang_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `piutang_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `piutang_id` bigint unsigned NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `pembayaran` decimal(15,2) DEFAULT NULL,
  `kekurangan` decimal(15,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `piutang_histories_piutang_id_foreign` (`piutang_id`),
  CONSTRAINT `piutang_histories_piutang_id_foreign` FOREIGN KEY (`piutang_id`) REFERENCES `piutang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `piutang_histories`
--

LOCK TABLES `piutang_histories` WRITE;
/*!40000 ALTER TABLE `piutang_histories` DISABLE KEYS */;
INSERT INTO `piutang_histories` VALUES (1,3,22200.00,0.00,22200.00,'Belum Lunas','2025-01-15 12:43:15','2025-01-15 12:43:15'),(2,3,22200.00,1000.00,21200.00,'Belum Lunas','2025-01-15 12:43:39','2025-01-15 12:43:39'),(3,3,22200.00,2000.00,20200.00,'Belum Lunas','2025-01-15 12:44:29','2025-01-15 12:44:29'),(4,6,11100.00,0.00,11100.00,'Belum Lunas','2025-01-15 17:24:46','2025-01-15 17:24:46');
/*!40000 ALTER TABLE `piutang_histories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produk`
--

DROP TABLE IF EXISTS `produk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produk` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `harga_beli` decimal(20,2) NOT NULL,
  `harga_jual` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produk_kode_unique` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produk`
--

LOCK TABLES `produk` WRITE;
/*!40000 ALTER TABLE `produk` DISABLE KEYS */;
INSERT INTO `produk` VALUES (4,'KB006','CN','KBY001600',4,3700.00,5000,'2025-01-15 12:25:28','2025-01-15 17:24:29');
/*!40000 ALTER TABLE `produk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

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
  KEY `1` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`),
  CONSTRAINT `1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('cZyUatloFg700bM9hqX1iRpINeJjgamYHcRKGFxZ',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSUpKcFVod2xMcjAzVk5Qdk5mS3FvdWpCWnNWQ0VXcnZLZ2JvQ2Y3SyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9kdWsiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1736965233);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@example.com',NULL,'$2y$12$K0LZWMlAX4EHP.RNWEsIAeWwJrqoHsGU/Xa9PQxUVKn.3MDygo/HC','admin',NULL,'2025-01-15 11:49:05','2025-01-15 11:49:05');
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

-- Dump completed on 2025-01-16 23:16:14
