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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='管理员角色';
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
  `name` varchar(255) NOT NULL COMMENT 'key',
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
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=utf8mb4 COMMENT='权限规则';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_admin_rules`
--

LOCK TABLES `wa_admin_rules` WRITE;
/*!40000 ALTER TABLE `wa_admin_rules` DISABLE KEYS */;
INSERT INTO `wa_admin_rules` VALUES (1,'数据库','Database',0,'LAYOUT','/database','ant-design:database-filled','2022-08-10 15:17:51','2022-08-13 10:31:19',NULL,0,1),(2,'所有表','plugin\\admin\\app\\controller\\database\\TableController',1,'/database/table/index','table','','2022-08-10 16:24:53','2022-08-13 10:54:32',NULL,0,1),(3,'表详情','TableView',1,'/database/table/View','table/view/:id',NULL,'2022-08-10 21:55:28','2022-08-24 22:36:10',NULL,1,1),(4,'权限管理','Auth',0,'LAYOUT','/auth','ant-design:setting-filled','2022-08-10 22:01:17','2022-08-13 10:30:33',NULL,0,1),(5,'账户管理','plugin\\admin\\app\\controller\\auth\\AdminController',4,'/auth/admin/index','admin',NULL,'2022-08-10 22:03:15','2022-08-13 11:09:30',NULL,0,1),(11,'用户管理','User',0,'LAYOUT','/user','ant-design:smile-filled','2022-08-11 09:46:04','2022-08-11 09:46:06',NULL,0,1),(12,'用户','plugin\\admin\\app\\controller\\user\\UserController',11,'/user/user/index','user',NULL,'2022-08-11 09:51:31','2022-08-13 22:15:24',NULL,0,1),(22,'角色管理','plugin\\admin\\app\\controller\\auth\\AdminRoleController',4,'/auth/admin-role/index','admin-role',NULL,'2022-08-13 11:16:30','2022-08-23 21:18:03',NULL,0,1),(23,'菜单管理','plugin\\admin\\app\\controller\\auth\\AdminRuleController',4,'/auth/admin-rule/index','admin-rule',NULL,'2022-08-13 11:50:25','2022-08-13 11:50:25',NULL,0,1),(66,'通用设置','Common',0,'LAYOUT','/common','ant-design:setting-filled','2022-08-14 16:18:29','2022-08-14 16:18:32',NULL,0,1),(67,'个人资料','plugin\\admin\\app\\controller\\user\\AccountController',66,'/common/account/index','account',NULL,'2022-08-14 16:21:44','2022-08-14 16:21:47',NULL,0,1),(148,'查询表','plugin\\admin\\app\\controller\\database\\TableController@show',2,'','',NULL,'2022-08-27 11:54:33','2022-08-27 15:37:42',NULL,0,0),(149,'查询记录','plugin\\admin\\app\\controller\\database\\TableController@select',2,'','',NULL,'2022-08-27 11:54:33','2022-08-27 15:37:42',NULL,0,0),(150,'插入记录','plugin\\admin\\app\\controller\\database\\TableController@insert',2,'','',NULL,'2022-08-27 11:54:33','2022-08-27 15:37:42',NULL,0,0),(151,'更新记录','plugin\\admin\\app\\controller\\database\\TableController@update',2,'','',NULL,'2022-08-27 11:54:33','2022-08-27 15:37:42',NULL,0,0),(152,'删除记录','plugin\\admin\\app\\controller\\database\\TableController@delete',2,'','',NULL,'2022-08-27 11:54:33','2022-08-27 15:37:42',NULL,0,0),(153,'创建表','plugin\\admin\\app\\controller\\database\\TableController@create',2,'','',NULL,'2022-08-27 11:54:33','2022-08-27 15:37:42',NULL,0,0),(154,'修改表','plugin\\admin\\app\\controller\\database\\TableController@modify',2,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(155,'表摘要','plugin\\admin\\app\\controller\\database\\TableController@schema',2,'','',NULL,'2022-08-27 11:54:33','2022-08-27 15:37:42',NULL,0,0),(156,'删除表','plugin\\admin\\app\\controller\\database\\TableController@drop',2,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(158,'删除','plugin\\admin\\app\\controller\\auth\\AdminController@delete',5,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(159,'查询','plugin\\admin\\app\\controller\\auth\\AdminController@select',5,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(160,'添加','plugin\\admin\\app\\controller\\auth\\AdminController@insert',5,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(161,'更新','plugin\\admin\\app\\controller\\auth\\AdminController@update',5,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(162,'摘要','plugin\\admin\\app\\controller\\auth\\AdminController@schema',5,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(163,'查询','plugin\\admin\\app\\controller\\user\\UserController@select',12,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(164,'添加','plugin\\admin\\app\\controller\\user\\UserController@insert',12,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(165,'更新','plugin\\admin\\app\\controller\\user\\UserController@update',12,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(166,'删除','plugin\\admin\\app\\controller\\user\\UserController@delete',12,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(167,'摘要','plugin\\admin\\app\\controller\\user\\UserController@schema',12,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(168,'更新','plugin\\admin\\app\\controller\\auth\\AdminRoleController@update',22,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(169,'查询','plugin\\admin\\app\\controller\\auth\\AdminRoleController@select',22,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(170,'添加','plugin\\admin\\app\\controller\\auth\\AdminRoleController@insert',22,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(171,'删除','plugin\\admin\\app\\controller\\auth\\AdminRoleController@delete',22,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(172,'摘要','plugin\\admin\\app\\controller\\auth\\AdminRoleController@schema',22,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(173,'获取权限树','plugin\\admin\\app\\controller\\auth\\AdminRuleController@tree',23,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(174,'添加','plugin\\admin\\app\\controller\\auth\\AdminRuleController@insert',23,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(175,'删除','plugin\\admin\\app\\controller\\auth\\AdminRuleController@delete',23,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(176,'一键生成菜单','plugin\\admin\\app\\controller\\auth\\AdminRuleController@create',23,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(177,'查询','plugin\\admin\\app\\controller\\auth\\AdminRuleController@select',23,'','',NULL,'2022-08-27 11:54:33','2022-08-30 20:51:25',NULL,0,0),(178,'更新','plugin\\admin\\app\\controller\\auth\\AdminRuleController@update',23,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0),(179,'摘要','plugin\\admin\\app\\controller\\auth\\AdminRuleController@schema',23,'','',NULL,'2022-08-27 11:54:33','2022-08-27 11:54:33',NULL,0,0);
/*!40000 ALTER TABLE `wa_admin_rules` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COMMENT='选项表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_options`
--

LOCK TABLES `wa_options` WRITE;
/*!40000 ALTER TABLE `wa_options` DISABLE KEYS */;
INSERT INTO `wa_options` VALUES (30,'table_form_schema_wa_users','{\"id\":{\"field\":\"id\",\"comment\":\"主键\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":true,\"readonly\":false,\"searchable\":true,\"search_type\":\"普通查询\",\"control_args\":null},\"username\":{\"field\":\"username\",\"comment\":\"用户名\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"nickname\":{\"field\":\"nickname\",\"comment\":\"昵称\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"password\":{\"field\":\"password\",\"comment\":\"密码\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"sex\":{\"field\":\"sex\",\"comment\":\"性别\",\"control\":\"Select\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":\"options:男:男,女:女\"},\"avatar\":{\"field\":\"avatar\",\"comment\":\"头像\",\"control\":\"Upload\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"email\":{\"field\":\"email\",\"comment\":\"邮箱\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"mobile\":{\"field\":\"mobile\",\"comment\":\"手机\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"level\":{\"field\":\"level\",\"comment\":\"等级\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":true,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"birthday\":{\"field\":\"birthday\",\"comment\":\"生日\",\"control\":\"DatePicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"money\":{\"field\":\"money\",\"comment\":\"余额\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"score\":{\"field\":\"score\",\"comment\":\"积分\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":true,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"last_time\":{\"field\":\"last_time\",\"comment\":\"上次登录时间\",\"control\":\"DatePicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"last_ip\":{\"field\":\"last_ip\",\"comment\":\"上次登录ip\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"join_time\":{\"field\":\"join_time\",\"comment\":\"注册时间\",\"control\":\"DatePicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"join_ip\":{\"field\":\"join_ip\",\"comment\":\"注册ip\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"token\":{\"field\":\"token\",\"comment\":\"token\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"role\":{\"field\":\"role\",\"comment\":\"角色\",\"control\":\"InputNumber\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null}}','2022-08-15 00:00:00','2022-08-30 21:49:22'),(31,'table_form_schema_wa_admin_roles','{\"id\":{\"field\":\"id\",\"comment\":\"主键\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"角色名\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"普通查询\",\"control_args\":null},\"rules\":{\"field\":\"rules\",\"comment\":\"权限\",\"control\":\"ApiTree\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":\"url:\\/app\\/admin\\/auth\\/adminrule\\/tree;multiple:true;checkable:true;checkStrictly:false\"},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"between\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null}}','2022-08-15 00:00:00','2022-08-29 18:04:47'),(38,'table_form_schema_wa_admin_rules','{\"id\":{\"field\":\"id\",\"comment\":\"主键\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"title\":{\"field\":\"title\",\"comment\":\"标题\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"key\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"pid\":{\"field\":\"pid\",\"comment\":\"上级菜单\",\"control\":\"ApiTreeSelect\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"component\":{\"field\":\"component\",\"comment\":\"前端组件\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"path\":{\"field\":\"path\",\"comment\":\"路径\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"icon\":{\"field\":\"icon\",\"comment\":\"图标\",\"control\":\"IconPicker\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"status\":{\"field\":\"status\",\"comment\":\"状态\",\"control\":\"Select\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"options:normal:正常,disabled:禁用\"},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"frame_src\":{\"field\":\"frame_src\",\"comment\":\"url\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"hide_menu\":{\"field\":\"hide_menu\",\"comment\":\"隐藏菜单\",\"control\":\"Switch\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"is_menu\":{\"field\":\"is_menu\",\"comment\":\"是否菜单\",\"control\":\"Select\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":\"options:0:否,1:是\"}}','2022-08-15 00:00:00','2022-08-29 16:55:27'),(39,'table_form_schema_wa_admins','{\"id\":{\"field\":\"id\",\"comment\":\"ID\",\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":true,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"username\":{\"field\":\"username\",\"comment\":\"用户名\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"nickname\":{\"field\":\"nickname\",\"comment\":\"昵称\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"password\":{\"field\":\"password\",\"comment\":\"密码\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"avatar\":{\"field\":\"avatar\",\"comment\":\"头像\",\"control\":\"Upload\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"url:\\/app\\/admin\\/common\\/upload\\/avatar;maxNumber:1\"},\"email\":{\"field\":\"email\",\"comment\":\"邮箱\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"mobile\":{\"field\":\"mobile\",\"comment\":\"手机\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":true,\"search_type\":\"between\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"roles\":{\"field\":\"roles\",\"comment\":\"角色\",\"control\":\"ApiTreeSelect\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":\"url:\\/app\\/admin\\/auth\\/adminrole\\/select?format=tree&status=normal\"}}','2022-08-15 00:00:00','2022-08-29 18:03:29'),(40,'table_form_schema_wa_options','{\"id\":{\"field\":\"id\",\"comment\":null,\"control\":\"InputNumber\",\"form_show\":false,\"list_show\":true,\"enable_sort\":false,\"readonly\":true,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"name\":{\"field\":\"name\",\"comment\":\"键\",\"control\":\"Input\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"value\":{\"field\":\"value\",\"comment\":\"值\",\"control\":\"InputTextArea\",\"form_show\":true,\"list_show\":true,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"normal\",\"control_args\":null},\"created_at\":{\"field\":\"created_at\",\"comment\":\"创建时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null},\"updated_at\":{\"field\":\"updated_at\",\"comment\":\"更新时间\",\"control\":\"DatePicker\",\"form_show\":false,\"list_show\":false,\"enable_sort\":false,\"readonly\":false,\"searchable\":false,\"search_type\":\"普通查询\",\"control_args\":null}}','2022-08-15 00:00:00','2022-08-30 20:46:56');
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
  `email` varchar(128) NOT NULL COMMENT '邮箱',
  `mobile` varchar(16) NOT NULL COMMENT '手机',
  `level` tinyint(4) NOT NULL COMMENT '等级',
  `birthday` date NOT NULL COMMENT '生日',
  `money` int(10) unsigned NOT NULL COMMENT '余额',
  `score` int(11) NOT NULL COMMENT '积分',
  `last_time` datetime NOT NULL COMMENT '上次登录时间',
  `last_ip` varchar(50) NOT NULL COMMENT '上次登录ip',
  `join_time` datetime NOT NULL COMMENT '注册时间',
  `join_ip` varchar(50) NOT NULL COMMENT '注册ip',
  `token` varchar(50) DEFAULT NULL COMMENT 'token',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `role` tinyint(4) DEFAULT NULL COMMENT '角色',
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

-- Dump completed on 2022-08-31 15:09:32
