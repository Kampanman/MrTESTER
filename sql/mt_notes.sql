-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql1029.db.sakura.ne.jp
-- 生成日時: 2023 年 11 月 30 日 00:30
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
-- テーブルの構造 `mt_notes`
--
DROP TABLE IF EXISTS `mt_notes`;
CREATE TABLE `mt_notes` (
  `id` varchar(16) NOT NULL COMMENT 'ノートID',
  `title` varchar(64) NOT NULL COMMENT 'ノートタイトル',
  `tags` varchar(32) DEFAULT NULL COMMENT 'ノートの分類タグ',
  `url` text COMMENT '関連リンクURL',
  `note` text NOT NULL COMMENT 'ノート本文',
  `last_viewed_at` datetime NOT NULL COMMENT '最終閲覧日',
  `created_at` datetime NOT NULL COMMENT '登録日',
  `created_user_id` varchar(12) NOT NULL COMMENT '登録者ID',
  `updated_at` datetime NOT NULL COMMENT '更新日'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `mt_notes`
--
ALTER TABLE `mt_notes`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
