-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 05 2010 г., 13:49
-- Версия сервера: 5.1.51
-- Версия PHP: 5.3.3-pl1-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `HSPHP_test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `read1`
--

DROP TABLE IF EXISTS `read1`;
CREATE TABLE `read1` (
  `key` int(11) NOT NULL,
  `date` date NOT NULL,
  `float` float NOT NULL,
  `varchar` varchar(40) NOT NULL,
  `text` text NOT NULL,
  `set` set('a','b','c') NOT NULL,
  `union` enum('a','b','c') NOT NULL,
  `null` int(11) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `read1`
--

INSERT INTO `read1` (`key`, `date`, `float`, `varchar`, `text`, `set`, `union`, `null`) VALUES
(42, '2010-10-29', 3.14159, 'variable length', 'some\r\nbig\r\ntext', 'a,c', 'b', NULL),
(12, '0000-00-00', 12345, '', '', '', '', NULL),
(1, '0000-00-00', 1, '', '', '', '', NULL),
(2, '0000-00-00', 2, '', '', '', '', NULL),
(3, '0000-00-00', 3, '', '', '', '', NULL),
(4, '0000-00-00', 4, '', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `write1`
--

DROP TABLE IF EXISTS `write1`;
CREATE TABLE `write1` (
  `k` int(11) NOT NULL,
  `v` varchar(40) NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `write1`
--

