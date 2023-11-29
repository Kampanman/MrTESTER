-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql1029.db.sakura.ne.jp
-- 生成日時: 2023 年 11 月 30 日 00:25
-- サーバのバージョン： 5.7.40-log
-- PHP のバージョン: 8.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `empower-util_mydb`
--

--
-- テーブルの構造 `mt_accounts`
--
DROP TABLE IF EXISTS `mt_accounts`;
CREATE TABLE `mt_accounts` (
  `id` varchar(12) NOT NULL COMMENT 'アカウントID',
  `name` varchar(32) NOT NULL COMMENT 'アカウント名',
  `login_id` varchar(256) NOT NULL COMMENT 'ログインID',
  `password` text NOT NULL COMMENT 'パスワード',
  `authority` int(2) NOT NULL DEFAULT '0' COMMENT '権限',
  `comment` text COMMENT 'ユーザーの個別コメント',
  `is_stopped` int(2) DEFAULT '0' COMMENT '利用停止フラグ',
  `created_at` datetime NOT NULL COMMENT '登録日',
  `updated_at` datetime NOT NULL COMMENT '更新日'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- テーブルのデータのダンプ `mt_accounts`
--

INSERT INTO `mt_accounts` (`id`, `name`, `login_id`, `password`, `authority`, `comment`, `is_stopped`, `created_at`, `updated_at`) VALUES
('D53SueHDihiu', 'カンパンマン', 'kampanman.newsoul@mymail.com', '$2y$10$jyMSrsB3JSVu6AwkSlc3i.gfsOTjl2d/n/o2OX9iz5n6fGh4yY7Py', 2, 'パスワードは、「Kampan1234」。', 0, '2023-11-30 00:15:00', '2023-11-30 00:15:00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
