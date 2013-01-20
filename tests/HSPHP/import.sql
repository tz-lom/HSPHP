SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

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
(4, '0000-00-00', 4, '', '', '', '', NULL),
(100, '0000-00-00', 0, '', '', '', '', NULL),
(10001, '2012-01-20', 1 , 'text with special chars', CONCAT(CHAR(0),CHAR(1),CHAR(2),CHAR(3),CHAR(4),CHAR(5),CHAR(6),CHAR(7),CHAR(8),CHAR(9),CHAR(10),CHAR(11),CHAR(12),CHAR(13),CHAR(14),CHAR(15)), '', '', NULL);

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

