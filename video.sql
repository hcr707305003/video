/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : video

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-04-04 16:44:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for password
-- ----------------------------
DROP TABLE IF EXISTS `password`;
CREATE TABLE `password` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for son_stand
-- ----------------------------
DROP TABLE IF EXISTS `son_stand`;
CREATE TABLE `son_stand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '用户名称',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `host` varchar(124) NOT NULL DEFAULT '' COMMENT '子站域名',
  `code` varchar(124) NOT NULL DEFAULT '' COMMENT 'code',
  `codetime` int(11) NOT NULL DEFAULT '0' COMMENT 'code过期时间',
  `access_token` varchar(124) NOT NULL DEFAULT '' COMMENT 'token',
  `tokentime` int(11) NOT NULL DEFAULT '0' COMMENT 'token过期时间',
  `create_url` varchar(124) NOT NULL DEFAULT '' COMMENT '新增接口推送地址',
  `update_url` varchar(124) NOT NULL DEFAULT '' COMMENT '编辑接口推送地址',
  `down_url` varchar(124) DEFAULT '' COMMENT '下架接口',
  `code_url` varchar(124) NOT NULL DEFAULT '' COMMENT 'code的回调地址',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0(待审核) 5.审核通过-上架 -5.审核不通过(下架)',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5218 DEFAULT CHARSET=utf8mb4 COMMENT='子站';

-- ----------------------------
-- Table structure for tasks
-- ----------------------------
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '任务标题',
  `desc` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '任务内容',
  `task_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '任务状态 0--新任务;1--加入任务列表,5--已完成 -5取消',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `identifier` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '子站标识 比如 秋霞 qx -',
  `status` tinyint(3) NOT NULL COMMENT '状态码',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for vods
-- ----------------------------
DROP TABLE IF EXISTS `vods`;
CREATE TABLE `vods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '名称',
  `subname` varchar(255) DEFAULT NULL COMMENT '副标题',
  `enname` varchar(255) DEFAULT NULL COMMENT '英文名称',
  `letter` char(1) DEFAULT NULL COMMENT '首字母',
  `type_name` varchar(55) NOT NULL COMMENT '分类名称',
  `pic` varchar(255) NOT NULL COMMENT '封面',
  `lang` varchar(255) DEFAULT NULL COMMENT '语言',
  `area` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '地区',
  `score` float NOT NULL COMMENT '评分',
  `year` smallint(4) DEFAULT NULL COMMENT '年份',
  `last` int(11) NOT NULL COMMENT '更新时间',
  `state` int(8) DEFAULT NULL COMMENT '连载',
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `actor` varchar(255) DEFAULT NULL COMMENT '演员',
  `director` varchar(255) DEFAULT NULL COMMENT '导演',
  `playfrom` varchar(266) DEFAULT NULL COMMENT '要过滤的文字',
  `dd` mediumtext NOT NULL COMMENT '播放url',
  `des` text COMMENT '简介',
  `downfrom` varchar(255) DEFAULT NULL COMMENT '下载组',
  `downurl` mediumtext COMMENT '下载地址',
  `tuisong` varchar(255) DEFAULT '' COMMENT '推送标志',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0-待上架 | 5-上架 | -5下架',
  `vod_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0(待审核) 5(审核通过-上架) -5审核不通过(下架)',
  `created_at` timestamp(6) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp(6) NULL DEFAULT NULL COMMENT '修改时间',
  `deleted_at` timestamp(6) NULL DEFAULT NULL COMMENT '软删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=482 DEFAULT CHARSET=utf8mb4 COMMENT='视频资源表';

-- ----------------------------
-- Table structure for vods_task
-- ----------------------------
DROP TABLE IF EXISTS `vods_task`;
CREATE TABLE `vods_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `vods_id` int(11) NOT NULL COMMENT '视频id',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0-待发送, 5-已发送,',
  `created_at` timestamp(6) NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp(6) NULL DEFAULT NULL COMMENT '修改时间',
  `deleted_at` timestamp(6) NULL DEFAULT NULL COMMENT '软删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='视频任务关联表';
