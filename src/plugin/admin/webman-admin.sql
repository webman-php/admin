-- MySQL dump 10.13  Distrib 5.7.21, for osx10.13 (x86_64)
--
-- Host: localhost    Database: webman_admin
-- ------------------------------------------------------
-- Server version	5.7.21

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `wa_admin_roles`
--

DROP TABLE IF EXISTS `wa_admin_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_admin_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `name` varchar(80) NOT NULL COMMENT 'character name',
  `rules` text COMMENT 'Permissions',
  `created_at` datetime NOT NULL COMMENT 'creation time',
  `updated_at` datetime NOT NULL COMMENT 'Update time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='Admin role';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_admin_roles`
--

LOCK TABLES `wa_admin_roles` WRITE;
/*!40000 ALTER TABLE `wa_admin_roles` DISABLE KEYS */;
INSERT INTO `wa_admin_roles` VALUES (1,'Super Admin','*','2022-08-13 16:15:01','2022-08-13 16:15:01');
/*!40000 ALTER TABLE `wa_admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_admin_rules`
--

DROP TABLE IF EXISTS `wa_admin_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_admin_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `title` varchar(255) NOT NULL COMMENT 'title',
  `name` varchar(255) NOT NULL COMMENT 'name(Globally Unique)',
  `pid` int(10) unsigned DEFAULT '0' COMMENT 'Super Menu',
  `component` varchar(255) DEFAULT 'LAYOUT' COMMENT 'Frontend Components',
  `path` varchar(255) NOT NULL COMMENT 'path',
  `icon` varchar(255) DEFAULT NULL COMMENT 'icon',
  `created_at` datetime NOT NULL COMMENT 'creation time',
  `updated_at` datetime NOT NULL COMMENT 'Update time',
  `frame_src` varchar(255) DEFAULT NULL COMMENT 'url',
  `hide_menu` tinyint(4) DEFAULT '0' COMMENT 'Hide menu',
  `is_menu` int(11) NOT NULL DEFAULT '1' COMMENT 'Whether menu',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Permission Rules';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wa_admins`
--

DROP TABLE IF EXISTS `wa_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(32) NOT NULL COMMENT 'username',
  `nickname` varchar(40) NOT NULL COMMENT 'Nick name',
  `password` varchar(255) NOT NULL COMMENT 'password',
  `avatar` varchar(255) DEFAULT '/app/admin/avatar.png' COMMENT 'avatar',
  `email` varchar(100) DEFAULT NULL COMMENT 'Mail',
  `mobile` varchar(16) DEFAULT NULL COMMENT 'cell phone',
  `created_at` datetime DEFAULT NULL COMMENT 'creation time',
  `updated_at` datetime DEFAULT NULL COMMENT 'Update time',
  `roles` varchar(255) DEFAULT NULL COMMENT 'Role',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Administrator table';
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `wa_options`
--

DROP TABLE IF EXISTS `wa_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '键',
  `value` longtext NOT NULL COMMENT '值',
  `created_at` datetime NOT NULL DEFAULT '2022-08-15 00:00:00' COMMENT 'creation time',
  `updated_at` datetime NOT NULL DEFAULT '2022-08-15 00:00:00' COMMENT 'Update time',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='Option List';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_options`
--

LOCK TABLES `wa_options` WRITE;
/*!40000 ALTER TABLE `wa_options` DISABLE KEYS */;
INSERT INTO `wa_options` VALUES (1,'table_form_schema_wa_users','{\"id\":{\"field\":\"id\",\"comment\":\"primary key\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":true,\"readonly\":false,\"searchable\":true,\"search_type\":\"General query\",\"control_args\":null},\"username\":{\"field\":\"username\",\"comment\":\"username\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"nickname\":{\"field\":\"nickname\",\"comment\":\"Nick name\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"password\":{\"field\":\"password\",\"comment\":\"password\",\"control\":\"Input\",\"form_show\":true,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"sex\":{\"field\":\"sex\",\"comment\":\"gender\",\"control\":\"Select\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":\"options:男:男,女:女\"},\"avatar\":{\"field\":\"avatar\",\"comment\":\"avatar\",\"control\":\"Upload\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"multiple:false; maxNumber:1\"},\"email\":{\"field\":\"email\",\"comment\":\"Mail\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"mobile\":{\"field\":\"mobile\",\"comment\":\"cell phone\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"level\":{\"field\":\"level\",\"comment\":\"grade\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"birthday\":{\"field\":\"birthday\",\"comment\":\"Birthday\",\"control\":\"DatePicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":\"showTime:false\"},\"money\":{\"field\":\"money\",\"comment\":\"balance\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"score\":{\"field\":\"score\",\"comment\":\"integral\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"last_time\":{\"field\":\"last_time\",\"comment\":\"Last Login Time\",\"control\":\"DatePicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"last_ip\":{\"field\":\"last_ip\",\"comment\":\"Last Loginip\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"join_time\":{\"field\":\"join_time\",\"comment\":\"Registration time\",\"control\":\"DatePicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"join_ip\":{\"field\":\"join_ip\",\"comment\":\"registerip\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"token\":{\"field\":\"token\",\"comment\":\"token\",\"control\":\"Input\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"creation time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"Update time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"role\":{\"field\":\"role\",\"comment\":\"Role\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":null},\"status\":{\"field\":\"status\",\"comment\":\"state\",\"control\":\"Select\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":\"options:normal:normal,dsiabled:dsiabled\"}}','2022-08-15 00:00:00','2022-09-20 10:24:52'),(2,'table_form_schema_wa_admin_roles','{\"id\":{\"field\":\"id\",\"comment\":\"primary key\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"character name\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"General query\",\"control_args\":null},\"rules\":{\"field\":\"rules\",\"comment\":\"Permissions\",\"control\":\"ApiTree\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":\"url:\\/app\\/admin\\/auth\\/adminrule\\/tree;multiple:true;checkable:true;checkStrictly:false\"},\"created_at\":{\"field\":\"created_at\",\"comment\":\"creation time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"between\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"Update time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":null}}','2022-08-15 00:00:00','2022-08-29 18:04:47'),(3,'table_form_schema_wa_admin_rules','{\"id\":{\"field\":\"id\",\"comment\":\"primary key\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":null},\"title\":{\"field\":\"title\",\"comment\":\"title\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"name(Globally Unique)\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"pid\":{\"field\":\"pid\",\"comment\":\"Super Menu\",\"control\":\"ApiTreeSelect\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"url:\\/app\\/admin\\/common\\/menu\\/tree\"},\"component\":{\"field\":\"component\",\"comment\":\"Frontend Components\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"path\":{\"field\":\"path\",\"comment\":\"path\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"icon\":{\"field\":\"icon\",\"comment\":\"icon\",\"control\":\"IconPicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"creation time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"Update time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":null},\"frame_src\":{\"field\":\"frame_src\",\"comment\":\"url\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"hide_menu\":{\"field\":\"hide_menu\",\"comment\":\"Hide menu\",\"control\":\"Switch\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"is_menu\":{\"field\":\"is_menu\",\"comment\":\"Whether menu\",\"control\":\"Select\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":\"options:0:否,1:是\"}}','2022-08-15 00:00:00','2022-09-14 16:47:06'),(4,'table_form_schema_wa_admins','{\"id\":{\"field\":\"id\",\"comment\":\"ID\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":true,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"username\":{\"field\":\"username\",\"comment\":\"username\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"nickname\":{\"field\":\"nickname\",\"comment\":\"Nick name\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"password\":{\"field\":\"password\",\"comment\":\"password\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"avatar\":{\"field\":\"avatar\",\"comment\":\"avatar\",\"control\":\"Upload\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"url:\\/app\\/admin\\/common\\/upload\\/avatar;maxNumber:1\"},\"email\":{\"field\":\"email\",\"comment\":\"Mail\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"mobile\":{\"field\":\"mobile\",\"comment\":\"cell phone\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"creation time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"between\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"Update time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"roles\":{\"field\":\"roles\",\"comment\":\"Role\",\"control\":\"ApiTreeSelect\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"url:\\/app\\/admin\\/auth\\/adminrole\\/select?format=tree&status=normal\"}}','2022-08-15 00:00:00','2022-08-29 18:03:29'),(5,'table_form_schema_wa_options','{\"id\":{\"field\":\"id\",\"comment\":null,\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":true,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"键\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"value\":{\"field\":\"value\",\"comment\":\"值\",\"control\":\"InputTextArea\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"creation time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"Update time\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"General query\",\"control_args\":null}}','2022-08-15 00:00:00','2022-08-30 20:46:56');
/*!40000 ALTER TABLE `wa_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_users`
--

DROP TABLE IF EXISTS `wa_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `username` varchar(32) NOT NULL COMMENT 'username',
  `nickname` varchar(40) NOT NULL COMMENT 'Nick name',
  `password` varchar(255) NOT NULL COMMENT 'password',
  `sex` enum('男','女') NOT NULL DEFAULT '男' COMMENT 'gender',
  `avatar` varchar(255) NOT NULL COMMENT 'avatar',
  `email` varchar(128) DEFAULT NULL COMMENT 'Mail',
  `mobile` varchar(16) DEFAULT NULL COMMENT 'cell phone',
  `level` tinyint(4) NOT NULL COMMENT 'grade',
  `birthday` date NOT NULL COMMENT 'Birthday',
  `money` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'balance',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT 'integral',
  `last_time` datetime DEFAULT NULL COMMENT 'Last Login Time',
  `last_ip` varchar(50) DEFAULT NULL COMMENT 'Last Loginip',
  `join_time` datetime DEFAULT NULL COMMENT 'Registration time',
  `join_ip` varchar(50) DEFAULT NULL COMMENT 'registerip',
  `token` varchar(50) DEFAULT NULL COMMENT 'token',
  `created_at` datetime DEFAULT NULL COMMENT 'creation time',
  `updated_at` datetime DEFAULT NULL COMMENT 'Update time',
  `role` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Role',
  `status` enum('normal','dsiabled') NOT NULL DEFAULT 'normal' COMMENT 'state',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='user table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_users`
--

LOCK TABLES `wa_users` WRITE;
/*!40000 ALTER TABLE `wa_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `wa_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-09-20 10:39:59
