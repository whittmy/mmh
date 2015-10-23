/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : mmh_ota_mgr

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2015-10-23 16:38:33
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `ug_app`
-- ----------------------------
DROP TABLE IF EXISTS `ug_app`;
CREATE TABLE `ug_app` (
  `appID` int(11) NOT NULL AUTO_INCREMENT,
  `modelID` int(11) NOT NULL,
  `cur_ver` varchar(15) NOT NULL DEFAULT '',
  `target_ver` varchar(15) DEFAULT '',
  `appSize` varchar(20) NOT NULL,
  `hashCode` char(64) NOT NULL DEFAULT '',
  `isforce` tinyint(4) NOT NULL,
  `url` varchar(512) NOT NULL DEFAULT '',
  `intro` varchar(200) NOT NULL DEFAULT '',
  `tm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`appID`),
  KEY `modelID` (`modelID`),
  KEY `ver` (`cur_ver`),
  KEY `tm` (`tm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ug_app
-- ----------------------------

-- ----------------------------
-- Table structure for `ug_chip`
-- ----------------------------
DROP TABLE IF EXISTS `ug_chip`;
CREATE TABLE `ug_chip` (
  `chipID` int(11) NOT NULL AUTO_INCREMENT,
  `chip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`chipID`),
  KEY `chip` (`chip`(3))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ug_chip
-- ----------------------------

-- ----------------------------
-- Table structure for `ug_customer`
-- ----------------------------
DROP TABLE IF EXISTS `ug_customer`;
CREATE TABLE `ug_customer` (
  `oemID` int(11) NOT NULL AUTO_INCREMENT,
  `customer` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`oemID`),
  KEY `customer` (`customer`(2)) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ug_customer
-- ----------------------------

-- ----------------------------
-- Table structure for `ug_model`
-- ----------------------------
DROP TABLE IF EXISTS `ug_model`;
CREATE TABLE `ug_model` (
  `modelID` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(15) NOT NULL DEFAULT '',
  `oemID` int(11) NOT NULL,
  `chipID` int(11) NOT NULL,
  PRIMARY KEY (`modelID`),
  KEY `model` (`model`(2)),
  KEY `oemID` (`oemID`),
  KEY `chipID` (`chipID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ug_model
-- ----------------------------
