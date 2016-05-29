/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50712
Source Host           : 127.0.0.1:3306
Source Database       : classtable

Target Server Type    : MYSQL
Target Server Version : 50712
File Encoding         : 65001

Date: 2016-05-29 10:36:41
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lesson
-- ----------------------------
DROP TABLE IF EXISTS `lesson`;
CREATE TABLE `lesson` (
  `lessonid` int(15) NOT NULL AUTO_INCREMENT,
  `classid` int(15) NOT NULL,
  `lessonname` varchar(30) DEFAULT NULL,
  `teacher` varchar(10) DEFAULT NULL,
  `classroom` varchar(30) DEFAULT NULL,
  `method` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT '正常',
  `weeks` varchar(10) NOT NULL,
  `time` int(5) NOT NULL,
  `week` int(5) NOT NULL,
  `studentid` int(10) NOT NULL,
  `special` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`lessonid`),
  KEY `student` (`lessonid`,`studentid`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8;
