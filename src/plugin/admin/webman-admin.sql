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
-- Table structure for table `upload`
--

DROP TABLE IF EXISTS `upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) NOT NULL COMMENT '名字',
  `url` varchar(255) NOT NULL COMMENT 'url',
  `admin_id` int(11) DEFAULT NULL COMMENT '管理员id',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `file_size` int(11) NOT NULL COMMENT '文件大小',
  `mime_type` varchar(255) NOT NULL COMMENT 'mime类型',
  `image_width` int(11) DEFAULT NULL COMMENT '图片宽度',
  `image_height` int(11) DEFAULT NULL COMMENT '图片高度',
  `ext` varchar(255) NOT NULL COMMENT '扩展名',
  `storage` varchar(255) NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `created_at` date DEFAULT NULL COMMENT '上传时间',
  `category` varchar(255) DEFAULT NULL COMMENT '类别',
  `updated_at` date DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件';
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员角色';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_admin_roles`
--

LOCK TABLES `wa_admin_roles` WRITE;
/*!40000 ALTER TABLE `wa_admin_roles` DISABLE KEYS */;
INSERT INTO `wa_admin_roles` VALUES (1,'超级管理员','*','2022-08-13 16:15:01','2022-11-29 16:45:36');
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
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `key` varchar(255) NOT NULL COMMENT '标识',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '上级菜单',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `href` varchar(255) DEFAULT NULL COMMENT 'url',
  `type` int(11) NOT NULL DEFAULT '1' COMMENT '类型',
  `weight` int(11) DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限规则';
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
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='选项表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_options`
--

LOCK TABLES `wa_options` WRITE;
/*!40000 ALTER TABLE `wa_options` DISABLE KEYS */;
INSERT INTO `wa_options` VALUES (1,'table_form_schema_wa_users','{\"id\":{\"field\":\"id\",\"_field_id\":\"0\",\"comment\":\"主键\",\"control\":\"inputNumber\",\"control_args\":\"\",\"list_show\":true,\"enable_sort\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false},\"username\":{\"field\":\"username\",\"_field_id\":\"1\",\"comment\":\"用户名\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"nickname\":{\"field\":\"nickname\",\"_field_id\":\"2\",\"comment\":\"昵称\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"password\":{\"field\":\"password\",\"_field_id\":\"3\",\"comment\":\"密码\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"search_type\":\"normal\",\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"sex\":{\"field\":\"sex\",\"_field_id\":\"4\",\"comment\":\"性别\",\"control\":\"select\",\"control_args\":\"url:\\/app\\/admin\\/dict\\/get\\/sex\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"avatar\":{\"field\":\"avatar\",\"_field_id\":\"5\",\"comment\":\"头像\",\"control\":\"uploadImage\",\"control_args\":\"url:\\/app\\/admin\\/upload\\/avatar\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"email\":{\"field\":\"email\",\"_field_id\":\"6\",\"comment\":\"邮箱\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"mobile\":{\"field\":\"mobile\",\"_field_id\":\"7\",\"comment\":\"手机\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"level\":{\"field\":\"level\",\"_field_id\":\"8\",\"comment\":\"等级\",\"control\":\"inputNumber\",\"control_args\":\"\",\"form_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"list_show\":false,\"enable_sort\":false},\"birthday\":{\"field\":\"birthday\",\"_field_id\":\"9\",\"comment\":\"生日\",\"control\":\"datePicker\",\"control_args\":\"\",\"form_show\":true,\"searchable\":true,\"search_type\":\"between\",\"list_show\":false,\"enable_sort\":false},\"money\":{\"field\":\"money\",\"_field_id\":\"10\",\"comment\":\"余额\",\"control\":\"inputNumber\",\"control_args\":\"\",\"form_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"list_show\":false,\"enable_sort\":false},\"score\":{\"field\":\"score\",\"_field_id\":\"11\",\"comment\":\"积分\",\"control\":\"inputNumber\",\"control_args\":\"\",\"form_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"list_show\":false,\"enable_sort\":false},\"last_time\":{\"field\":\"last_time\",\"_field_id\":\"12\",\"comment\":\"登录时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"form_show\":true,\"searchable\":true,\"search_type\":\"between\",\"list_show\":false,\"enable_sort\":false},\"last_ip\":{\"field\":\"last_ip\",\"_field_id\":\"13\",\"comment\":\"登录ip\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"list_show\":false,\"enable_sort\":false},\"join_time\":{\"field\":\"join_time\",\"_field_id\":\"14\",\"comment\":\"注册时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"form_show\":true,\"searchable\":true,\"search_type\":\"between\",\"list_show\":false,\"enable_sort\":false},\"join_ip\":{\"field\":\"join_ip\",\"_field_id\":\"15\",\"comment\":\"注册ip\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"list_show\":false,\"enable_sort\":false},\"token\":{\"field\":\"token\",\"_field_id\":\"16\",\"comment\":\"token\",\"control\":\"input\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"created_at\":{\"field\":\"created_at\",\"_field_id\":\"17\",\"comment\":\"创建时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"search_type\":\"between\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"updated_at\":{\"field\":\"updated_at\",\"_field_id\":\"18\",\"comment\":\"更新时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"search_type\":\"between\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"role\":{\"field\":\"role\",\"_field_id\":\"19\",\"comment\":\"角色\",\"control\":\"inputNumber\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"status\":{\"field\":\"status\",\"_field_id\":\"20\",\"comment\":\"禁用\",\"control\":\"switch\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false}}','2022-08-15 00:00:00','2022-12-04 15:13:01'),(2,'table_form_schema_wa_admin_roles','{\"id\":{\"field\":\"id\",\"_field_id\":\"0\",\"comment\":\"主键\",\"control\":\"inputNumber\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"name\":{\"field\":\"name\",\"_field_id\":\"1\",\"comment\":\"角色名\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"rules\":{\"field\":\"rules\",\"_field_id\":\"2\",\"comment\":\"权限\",\"control\":\"treeSelectMulti\",\"control_args\":\"url:\\/app\\/admin\\/admin-rule\\/get?type=0,1,2\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"created_at\":{\"field\":\"created_at\",\"_field_id\":\"3\",\"comment\":\"创建时间\",\"control\":\"datePicker\",\"control_args\":\"\",\"list_show\":true,\"searchable\":true,\"search_type\":\"between\",\"form_show\":false,\"enable_sort\":false},\"updated_at\":{\"field\":\"updated_at\",\"_field_id\":\"4\",\"comment\":\"更新时间\",\"control\":\"datePicker\",\"control_args\":\"\",\"list_show\":true,\"search_type\":\"normal\",\"form_show\":false,\"enable_sort\":false,\"searchable\":false}}','2022-08-15 00:00:00','2022-11-29 21:50:11'),(3,'table_form_schema_wa_admin_rules','{\"id\":{\"field\":\"id\",\"_field_id\":\"0\",\"comment\":\"主键\",\"control\":\"inputNumber\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"title\":{\"field\":\"title\",\"_field_id\":\"1\",\"comment\":\"标题\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"icon\":{\"field\":\"icon\",\"_field_id\":\"2\",\"comment\":\"图标\",\"control\":\"iconPicker\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"key\":{\"field\":\"key\",\"_field_id\":\"3\",\"comment\":\"标识\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"pid\":{\"field\":\"pid\",\"_field_id\":\"4\",\"comment\":\"上级菜单\",\"control\":\"treeSelect\",\"control_args\":\"url:\\/app\\/admin\\/admin-rule\\/select?format=tree&type=0,1\",\"form_show\":true,\"search_type\":\"normal\",\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"created_at\":{\"field\":\"created_at\",\"_field_id\":\"5\",\"comment\":\"创建时间\",\"control\":\"datePicker\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"updated_at\":{\"field\":\"updated_at\",\"_field_id\":\"6\",\"comment\":\"更新时间\",\"control\":\"iconPicker\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"href\":{\"field\":\"href\",\"_field_id\":\"7\",\"comment\":\"url\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"type\":{\"field\":\"type\",\"_field_id\":\"8\",\"comment\":\"类型\",\"control\":\"select\",\"control_args\":\"data:0:目录,1:菜单,2:权限\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"weight\":{\"field\":\"weight\",\"_field_id\":\"9\",\"comment\":\"排序\",\"control\":\"inputNumber\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false}}','2022-08-15 00:00:00','2022-11-29 21:27:37'),(4,'table_form_schema_wa_admins','{\"id\":{\"field\":\"id\",\"_field_id\":\"0\",\"comment\":\"ID\",\"control\":\"inputNumber\",\"control_args\":\"\",\"list_show\":true,\"search_type\":\"normal\",\"form_show\":false,\"enable_sort\":false,\"searchable\":false},\"username\":{\"field\":\"username\",\"_field_id\":\"1\",\"comment\":\"用户名\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"nickname\":{\"field\":\"nickname\",\"_field_id\":\"2\",\"comment\":\"昵称\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"password\":{\"field\":\"password\",\"_field_id\":\"3\",\"comment\":\"密码\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"search_type\":\"normal\",\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"avatar\":{\"field\":\"avatar\",\"_field_id\":\"4\",\"comment\":\"头像\",\"control\":\"uploadImage\",\"control_args\":\"url:\\/app\\/admin\\/upload\\/avatar\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"email\":{\"field\":\"email\",\"_field_id\":\"5\",\"comment\":\"邮箱\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"mobile\":{\"field\":\"mobile\",\"_field_id\":\"6\",\"comment\":\"手机\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"created_at\":{\"field\":\"created_at\",\"_field_id\":\"7\",\"comment\":\"创建时间\",\"control\":\"datePicker\",\"control_args\":\"\",\"searchable\":true,\"search_type\":\"between\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false},\"updated_at\":{\"field\":\"updated_at\",\"_field_id\":\"8\",\"comment\":\"更新时间\",\"control\":\"datePicker\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"roles\":{\"field\":\"roles\",\"_field_id\":\"9\",\"comment\":\"角色\",\"control\":\"treeSelectMulti\",\"control_args\":\"url:\\/app\\/admin\\/admin-role\\/select?format=select\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false}}','2022-08-15 00:00:00','2022-12-01 11:58:41'),(5,'table_form_schema_wa_options','{\"id\":{\"field\":\"id\",\"comment\":null,\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":true,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"键\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"value\":{\"field\":\"value\",\"comment\":\"值\",\"control\":\"InputTextArea\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null}}','2022-08-15 00:00:00','2022-08-30 20:46:56'),(12,'table_form_schema_aa','{\"id\":{\"field\":\"id\",\"_field_id\":\"0\",\"comment\":\"主键\",\"control\":\"inputNumber\",\"control_args\":\"\",\"enable_sort\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false},\"created_at\":{\"field\":\"created_at\",\"_field_id\":\"1\",\"comment\":\"创建时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"enable_sort\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false},\"updated_at\":{\"field\":\"updated_at\",\"_field_id\":\"2\",\"comment\":\"更新时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false},\"bb\":{\"field\":\"bb\",\"_field_id\":\"3\",\"comment\":\"bb\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"tree1\":{\"field\":\"tree1\",\"_field_id\":\"4\",\"comment\":\"\",\"control\":\"treeSelect\",\"control_args\":\"url:\\/app\\/admin\\/admin-rule\\/get?type=0,1,2\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"tree2\":{\"field\":\"tree2\",\"_field_id\":\"5\",\"comment\":\"\",\"control\":\"treeSelectMulti\",\"control_args\":\"url:\\/app\\/admin\\/admin-rule\\/get?type=0,1,2\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"icon\":{\"field\":\"icon\",\"_field_id\":\"6\",\"comment\":\"\",\"control\":\"iconPicker\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"file\":{\"field\":\"file\",\"_field_id\":\"7\",\"comment\":\"\",\"control\":\"upload\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"select1\":{\"field\":\"select1\",\"_field_id\":\"8\",\"comment\":\"\",\"control\":\"select\",\"control_args\":\"url:\\/app\\/admin\\/admin-role\\/select?format=select&a=123\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"select2\":{\"field\":\"select2\",\"_field_id\":\"9\",\"comment\":\"\",\"control\":\"select\",\"control_args\":\"url:\\/app\\/admin\\/admin-role\\/select?format=select\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"img\":{\"field\":\"img\",\"_field_id\":\"10\",\"comment\":\"\",\"control\":\"uploadImage\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"state\":{\"field\":\"state\",\"_field_id\":\"11\",\"comment\":\"\",\"control\":\"switch\",\"control_args\":\"lay-text:ON|OFF\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false}}','2022-08-15 00:00:00','2022-12-02 22:13:44'),(13,'table_form_schema_dict','{\"id\":{\"field\":\"id\",\"_field_id\":\"0\",\"comment\":\"主键\",\"control\":\"inputNumber\",\"control_args\":\"\",\"enable_sort\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false},\"created_at\":{\"field\":\"created_at\",\"_field_id\":\"1\",\"comment\":\"创建时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"enable_sort\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false},\"updated_at\":{\"field\":\"updated_at\",\"_field_id\":\"2\",\"comment\":\"更新时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false},\"bb\":{\"field\":\"bb\",\"_field_id\":\"3\",\"comment\":\"bb\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"tree1\":{\"field\":\"tree1\",\"_field_id\":\"4\",\"comment\":\"\",\"control\":\"treeSelect\",\"control_args\":\"url:\\/app\\/admin\\/admin-rule\\/get?type=0,1,2\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"tree2\":{\"field\":\"tree2\",\"_field_id\":\"5\",\"comment\":\"\",\"control\":\"treeSelectMulti\",\"control_args\":\"url:\\/app\\/admin\\/admin-rule\\/get?type=0,1,2\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"icon\":{\"field\":\"icon\",\"_field_id\":\"6\",\"comment\":\"\",\"control\":\"iconPicker\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"file\":{\"field\":\"file\",\"_field_id\":\"7\",\"comment\":\"\",\"control\":\"upload\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"select1\":{\"field\":\"select1\",\"_field_id\":\"8\",\"comment\":\"\",\"control\":\"select\",\"control_args\":\"url:\\/app\\/admin\\/admin-role\\/select?format=select&a=123\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"select2\":{\"field\":\"select2\",\"_field_id\":\"9\",\"comment\":\"\",\"control\":\"select\",\"control_args\":\"url:\\/app\\/admin\\/admin-role\\/select?format=select\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"img\":{\"field\":\"img\",\"_field_id\":\"10\",\"comment\":\"\",\"control\":\"uploadImage\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"state\":{\"field\":\"state\",\"_field_id\":\"11\",\"comment\":\"\",\"control\":\"switch\",\"control_args\":\"lay-text:ON|OFF\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false}}','2022-08-15 00:00:00','2022-08-15 00:00:00'),(18,'dict_sex','[{\"value\":\"0\",\"name\":\"女\"},{\"value\":\"1\",\"name\":\"男\"}]','2022-12-04 15:04:40','2022-12-04 15:04:40'),(19,'dict_status','[{\"value\":\"0\",\"name\":\"正常\"},{\"value\":\"1\",\"name\":\"禁用\"}]','2022-12-04 15:05:09','2022-12-04 15:05:09'),(22,'table_form_schema_test','{\"id\":{\"field\":\"id\",\"_field_id\":\"0\",\"comment\":\"主键\",\"control\":\"inputNumber\",\"control_args\":\"\",\"enable_sort\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false},\"created_at\":{\"field\":\"created_at\",\"_field_id\":\"1\",\"comment\":\"创建时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"enable_sort\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false},\"updated_at\":{\"field\":\"updated_at\",\"_field_id\":\"2\",\"comment\":\"更新时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false},\"bb\":{\"field\":\"bb\",\"_field_id\":\"3\",\"comment\":\"bb\",\"control\":\"input\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"tree1\":{\"field\":\"tree1\",\"_field_id\":\"4\",\"comment\":\"\",\"control\":\"treeSelect\",\"control_args\":\"url:\\/app\\/admin\\/admin-rule\\/get?type=0,1,2\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"tree2\":{\"field\":\"tree2\",\"_field_id\":\"5\",\"comment\":\"\",\"control\":\"treeSelectMulti\",\"control_args\":\"url:\\/app\\/admin\\/admin-rule\\/get?type=0,1,2\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"icon\":{\"field\":\"icon\",\"_field_id\":\"6\",\"comment\":\"\",\"control\":\"iconPicker\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"file\":{\"field\":\"file\",\"_field_id\":\"7\",\"comment\":\"\",\"control\":\"upload\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"select1\":{\"field\":\"select1\",\"_field_id\":\"8\",\"comment\":\"\",\"control\":\"select\",\"control_args\":\"url:\\/app\\/admin\\/admin-role\\/select?format=select&a=123\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"select2\":{\"field\":\"select2\",\"_field_id\":\"9\",\"comment\":\"\",\"control\":\"select\",\"control_args\":\"url:\\/app\\/admin\\/admin-role\\/select?format=select\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"img\":{\"field\":\"img\",\"_field_id\":\"10\",\"comment\":\"\",\"control\":\"uploadImage\",\"control_args\":\"\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"state\":{\"field\":\"state\",\"_field_id\":\"11\",\"comment\":\"\",\"control\":\"switch\",\"control_args\":\"lay-text:ON|OFF\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false}}','2022-08-15 00:00:00','2022-08-15 00:00:00'),(23,'table_form_schema_upload','{\"id\":{\"field\":\"id\",\"_field_id\":\"0\",\"comment\":\"主键\",\"control\":\"inputNumber\",\"control_args\":\"\",\"list_show\":true,\"enable_sort\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false},\"name\":{\"field\":\"name\",\"_field_id\":\"1\",\"comment\":\"名字\",\"control\":\"input\",\"control_args\":\"\",\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"enable_sort\":false},\"url\":{\"field\":\"url\",\"_field_id\":\"2\",\"comment\":\"url\",\"control\":\"upload\",\"control_args\":\"url:\\/app\\/admin\\/upload\\/attachment\",\"form_show\":true,\"list_show\":true,\"search_type\":\"normal\",\"enable_sort\":false,\"searchable\":false},\"admin_id\":{\"field\":\"admin_id\",\"_field_id\":\"3\",\"comment\":\"管理员id\",\"control\":\"inputNumber\",\"control_args\":\"\",\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false},\"user_id\":{\"field\":\"user_id\",\"_field_id\":\"4\",\"comment\":\"用户id\",\"control\":\"inputNumber\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"file_size\":{\"field\":\"file_size\",\"_field_id\":\"5\",\"comment\":\"文件大小\",\"control\":\"inputNumber\",\"control_args\":\"\",\"list_show\":true,\"search_type\":\"normal\",\"form_show\":false,\"enable_sort\":false,\"searchable\":false},\"mime_type\":{\"field\":\"mime_type\",\"_field_id\":\"6\",\"comment\":\"mime类型\",\"control\":\"inputNumber\",\"control_args\":\"\",\"list_show\":true,\"search_type\":\"normal\",\"form_show\":false,\"enable_sort\":false,\"searchable\":false},\"image_width\":{\"field\":\"image_width\",\"_field_id\":\"7\",\"comment\":\"图片宽度\",\"control\":\"inputNumber\",\"control_args\":\"\",\"list_show\":true,\"search_type\":\"normal\",\"form_show\":false,\"enable_sort\":false,\"searchable\":false},\"image_height\":{\"field\":\"image_height\",\"_field_id\":\"8\",\"comment\":\"图片高度\",\"control\":\"inputNumber\",\"control_args\":\"\",\"list_show\":true,\"search_type\":\"normal\",\"form_show\":false,\"enable_sort\":false,\"searchable\":false},\"ext\":{\"field\":\"ext\",\"_field_id\":\"9\",\"comment\":\"扩展名\",\"control\":\"input\",\"control_args\":\"\",\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"form_show\":false,\"enable_sort\":false},\"storage\":{\"field\":\"storage\",\"_field_id\":\"10\",\"comment\":\"存储位置\",\"control\":\"input\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false},\"created_at\":{\"field\":\"created_at\",\"_field_id\":\"11\",\"comment\":\"上传时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"searchable\":true,\"search_type\":\"between\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false},\"category\":{\"field\":\"category\",\"_field_id\":\"12\",\"comment\":\"类别\",\"control\":\"select\",\"control_args\":\"url:\\/app\\/admin\\/dict\\/get\\/upload\",\"form_show\":true,\"list_show\":true,\"searchable\":true,\"search_type\":\"normal\",\"enable_sort\":false},\"updated_at\":{\"field\":\"updated_at\",\"_field_id\":\"13\",\"comment\":\"更新时间\",\"control\":\"dateTimePicker\",\"control_args\":\"\",\"search_type\":\"normal\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"searchable\":false}}','2022-08-15 00:00:00','2022-12-04 19:28:38'),(24,'dict_upload','[{\"value\":\"1\",\"name\":\"分类1\"},{\"value\":\"2\",\"name\":\"分类2\"},{\"value\":\"3\",\"name\":\"分类3\"}]','2022-12-04 16:24:13','2022-12-04 16:24:13'),(25,'system_config','{\n	\"logo\": {\n		\"title\": \"Webman Admin\",\n		\"image\": \"/app/admin/admin/images/logo.png\"\n	},\n	\"menu\": {\n		\"data\": \"/app/admin/admin-rule/get\",\n		\"method\": \"GET\",\n		\"accordion\": true,\n		\"collapse\": false,\n		\"control\": false,\n		\"controlWidth\": 500,\n		\"select\": \"0\",\n		\"async\": true\n	},\n	\"tab\": {\n		\"enable\": true,\n		\"keepState\": true,\n		\"session\": true,\n		\"preload\": false,\n		\"max\": \"30\",\n		\"index\": {\n			\"id\": \"1\",\n			\"href\": \"/app/admin/table/index\",\n			\"title\": \"首页\"\n		}\n	},\n	\"theme\": {\n		\"defaultColor\": \"1\",\n		\"defaultMenu\": \"light-theme\",\n		\"defaultHeader\": \"light-theme\",\n		\"allowCustom\": true,\n		\"banner\": false\n	},\n	\"colors\": [\n		{\n			\"id\": \"1\",\n			\"color\": \"#36b368\",\n			\"second\": \"#f0f9eb\"\n		},\n		{\n			\"id\": \"2\",\n			\"color\": \"#2d8cf0\",\n			\"second\": \"#ecf5ff\"\n		},\n		{\n			\"id\": \"3\",\n			\"color\": \"#f6ad55\",\n			\"second\": \"#fdf6ec\"\n		},\n		{\n			\"id\": \"4\",\n			\"color\": \"#f56c6c\",\n			\"second\": \"#fef0f0\"\n		},\n		{\n			\"id\": \"5\",\n			\"color\": \"#3963bc\",\n			\"second\": \"#ecf5ff\"\n		}\n	],\n	\"other\": {\n		\"keepLoad\": \"500\",\n		\"autoHead\": false,\n		\"footer\": false\n	},\n	\"header\": {\n		\"message\": false\n	}\n}','2022-12-05 14:49:01','2022-12-05 14:49:01');
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
  `sex` enum('0','1') NOT NULL DEFAULT '1' COMMENT '性别',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `email` varchar(128) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机',
  `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '等级',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `money` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '余额',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
  `last_time` datetime DEFAULT NULL COMMENT '登录时间',
  `last_ip` varchar(50) DEFAULT NULL COMMENT '登录ip',
  `join_time` datetime DEFAULT NULL COMMENT '注册时间',
  `join_ip` varchar(50) DEFAULT NULL COMMENT '注册ip',
  `token` varchar(50) DEFAULT NULL COMMENT 'token',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `role` tinyint(4) NOT NULL DEFAULT '1' COMMENT '角色',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '禁用',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `mobile` (`mobile`),
  KEY `email` (`email`)
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

-- Dump completed on 2022-12-05 15:03:51
