/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50634
 Source Host           : localhost:3306
 Source Schema         : sample

 Target Server Type    : MySQL
 Target Server Version : 50634
 File Encoding         : 65001

 Date: 16/07/2018 17:22:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for bio
-- ----------------------------
DROP TABLE IF EXISTS `bio`;
CREATE TABLE `bio`  (
  `bio_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  PRIMARY KEY (`bio_id`) USING BTREE,
  INDEX `idx_user_id`(`user_id`) USING BTREE,
  INDEX `idx_bio_id`(`bio_id`) USING BTREE,
  CONSTRAINT `fkey_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `lname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE,
  INDEX `idx_user_id`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
