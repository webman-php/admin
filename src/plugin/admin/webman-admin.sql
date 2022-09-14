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
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(80) NOT NULL COMMENT '角色名',
  `rules` text COMMENT '权限',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='管理员角色';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_admin_roles`
--

LOCK TABLES `wa_admin_roles` WRITE;
/*!40000 ALTER TABLE `wa_admin_roles` DISABLE KEYS */;
INSERT INTO `wa_admin_roles` VALUES (1,'超级管理员','*','2022-08-13 16:15:01','2022-08-13 16:15:01');
/*!40000 ALTER TABLE `wa_admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_admin_rules`
--

DROP TABLE IF EXISTS `wa_admin_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_admin_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `name` varchar(255) NOT NULL COMMENT 'name(全局唯一)',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '上级菜单',
  `component` varchar(255) DEFAULT 'LAYOUT' COMMENT '前端组件',
  `path` varchar(255) NOT NULL COMMENT '路径',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `frame_src` varchar(255) DEFAULT NULL COMMENT 'url',
  `hide_menu` tinyint(4) DEFAULT '0' COMMENT '隐藏菜单',
  `is_menu` int(11) NOT NULL DEFAULT '1' COMMENT '是否菜单',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='权限规则';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wa_admins`
--

DROP TABLE IF EXISTS `wa_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `nickname` varchar(40) NOT NULL COMMENT '昵称',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `avatar` varchar(255) DEFAULT '/app/admin/avatar.png' COMMENT '头像',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `roles` varchar(255) DEFAULT NULL COMMENT '角色',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';
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
  `created_at` datetime NOT NULL DEFAULT '2022-08-15 00:00:00' COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT '2022-08-15 00:00:00' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='选项表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_options`
--

LOCK TABLES `wa_options` WRITE;
/*!40000 ALTER TABLE `wa_options` DISABLE KEYS */;
INSERT INTO `wa_options` VALUES (1,'table_form_schema_wa_users','{\"id\":{\"field\":\"id\",\"comment\":\"主键\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":true,\"readonly\":false,\"searchable\":true,\"search_type\":\"普通查询\",\"control_args\":null},\"username\":{\"field\":\"username\",\"comment\":\"用户名\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"nickname\":{\"field\":\"nickname\",\"comment\":\"昵称\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"password\":{\"field\":\"password\",\"comment\":\"密码\",\"control\":\"Input\",\"form_show\":true,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"sex\":{\"field\":\"sex\",\"comment\":\"性别\",\"control\":\"Select\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":\"options:男:男,女:女\"},\"avatar\":{\"field\":\"avatar\",\"comment\":\"头像\",\"control\":\"Upload\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"multiple:false; maxNumber:1\"},\"email\":{\"field\":\"email\",\"comment\":\"邮箱\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"mobile\":{\"field\":\"mobile\",\"comment\":\"手机\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"level\":{\"field\":\"level\",\"comment\":\"等级\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"birthday\":{\"field\":\"birthday\",\"comment\":\"生日\",\"control\":\"DatePicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":\"showTime:false\"},\"money\":{\"field\":\"money\",\"comment\":\"余额\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"score\":{\"field\":\"score\",\"comment\":\"积分\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"last_time\":{\"field\":\"last_time\",\"comment\":\"上次登录时间\",\"control\":\"DatePicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"last_ip\":{\"field\":\"last_ip\",\"comment\":\"上次登录ip\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"join_time\":{\"field\":\"join_time\",\"comment\":\"注册时间\",\"control\":\"DatePicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"join_ip\":{\"field\":\"join_ip\",\"comment\":\"注册ip\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"token\":{\"field\":\"token\",\"comment\":\"token\",\"control\":\"Input\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"role\":{\"field\":\"role\",\"comment\":\"角色\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null}}','2022-08-15 00:00:00','2022-09-14 17:06:40'),(2,'table_form_schema_wa_admin_roles','{\"id\":{\"field\":\"id\",\"comment\":\"主键\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"角色名\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"普通查询\",\"control_args\":null},\"rules\":{\"field\":\"rules\",\"comment\":\"权限\",\"control\":\"ApiTree\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":\"url:\\/app\\/admin\\/auth\\/adminrule\\/tree;multiple:true;checkable:true;checkStrictly:false\"},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"between\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null}}','2022-08-15 00:00:00','2022-08-29 18:04:47'),(3,'table_form_schema_wa_admin_rules','{\"id\":{\"field\":\"id\",\"comment\":\"主键\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"title\":{\"field\":\"title\",\"comment\":\"标题\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"name(全局唯一)\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"pid\":{\"field\":\"pid\",\"comment\":\"上级菜单\",\"control\":\"ApiTreeSelect\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"url:\\/app\\/admin\\/common\\/menu\\/tree\"},\"component\":{\"field\":\"component\",\"comment\":\"前端组件\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"path\":{\"field\":\"path\",\"comment\":\"路径\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"icon\":{\"field\":\"icon\",\"comment\":\"图标\",\"control\":\"IconPicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"frame_src\":{\"field\":\"frame_src\",\"comment\":\"url\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"hide_menu\":{\"field\":\"hide_menu\",\"comment\":\"隐藏菜单\",\"control\":\"Switch\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"is_menu\":{\"field\":\"is_menu\",\"comment\":\"是否菜单\",\"control\":\"Select\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":\"options:0:否,1:是\"}}','2022-08-15 00:00:00','2022-09-14 16:47:06'),(4,'table_form_schema_wa_admins','{\"id\":{\"field\":\"id\",\"comment\":\"ID\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":true,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"username\":{\"field\":\"username\",\"comment\":\"用户名\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"nickname\":{\"field\":\"nickname\",\"comment\":\"昵称\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"password\":{\"field\":\"password\",\"comment\":\"密码\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"avatar\":{\"field\":\"avatar\",\"comment\":\"头像\",\"control\":\"Upload\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"url:\\/app\\/admin\\/common\\/upload\\/avatar;maxNumber:1\"},\"email\":{\"field\":\"email\",\"comment\":\"邮箱\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"mobile\":{\"field\":\"mobile\",\"comment\":\"手机\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"between\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"roles\":{\"field\":\"roles\",\"comment\":\"角色\",\"control\":\"ApiTreeSelect\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"url:\\/app\\/admin\\/auth\\/adminrole\\/select?format=tree&status=normal\"}}','2022-08-15 00:00:00','2022-08-29 18:03:29'),(5,'table_form_schema_wa_options','{\"id\":{\"field\":\"id\",\"comment\":null,\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":true,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"键\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"value\":{\"field\":\"value\",\"comment\":\"值\",\"control\":\"InputTextArea\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null}}','2022-08-15 00:00:00','2022-08-30 20:46:56');
/*!40000 ALTER TABLE `wa_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_users`
--

DROP TABLE IF EXISTS `wa_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wa_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `nickname` varchar(40) NOT NULL COMMENT '昵称',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `sex` enum('男','女') NOT NULL DEFAULT '男' COMMENT '性别',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `email` varchar(128) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机',
  `level` tinyint(4) NOT NULL COMMENT '等级',
  `birthday` date NOT NULL COMMENT '生日',
  `money` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '余额',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `last_time` datetime DEFAULT NULL COMMENT '上次登录时间',
  `last_ip` varchar(50) DEFAULT NULL COMMENT '上次登录ip',
  `join_time` datetime DEFAULT NULL COMMENT '注册时间',
  `join_ip` varchar(50) DEFAULT NULL COMMENT '注册ip',
  `token` varchar(50) DEFAULT NULL COMMENT 'token',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `role` tinyint(4) NOT NULL DEFAULT '1' COMMENT '角色',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';
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

-- Dump completed on 2022-09-14 18:34:14
