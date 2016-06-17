-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Июн 17 2016 г., 19:05
-- Версия сервера: 10.1.9-MariaDB
-- Версия PHP: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `diskrecord`
--

-- --------------------------------------------------------

--
-- Структура таблицы `disk`
--

CREATE TABLE `disk` (
  `pk_disk` int(11) NOT NULL,
  `fk_user` int(11) NOT NULL,
  `name_disk` varchar(100) NOT NULL,
  `description_disk` text NOT NULL,
  `type_disk` varchar(30) NOT NULL,
  `size_disk` int(11) NOT NULL,
  `status_disk` int(11) NOT NULL,
  `status_string_disk` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `disk`
--

INSERT INTO `disk` (`pk_disk`, `fk_user`, `name_disk`, `description_disk`, `type_disk`, `size_disk`, `status_disk`, `status_string_disk`) VALUES
(109, 32, 'disk', '', 'CD', 8157, 4, 'Записан');

-- --------------------------------------------------------

--
-- Структура таблицы `file`
--

CREATE TABLE `file` (
  `pk_file` int(11) NOT NULL,
  `fk_disk` int(11) NOT NULL,
  `name_file` text NOT NULL,
  `size_file` int(11) NOT NULL,
  `type_file` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `file`
--

INSERT INTO `file` (`pk_file`, `fk_disk`, `name_file`, `size_file`, `type_file`) VALUES
(117, 109, 'file.txt', 120, 'text/plain'),
(118, 109, 'xampp-control.log', 8037, 'application/octet-stream');

-- --------------------------------------------------------

--
-- Структура таблицы `queue`
--

CREATE TABLE `queue` (
  `pk_queue` int(11) NOT NULL,
  `status_queue` int(11) NOT NULL,
  `id_record` int(11) NOT NULL,
  `progon` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `queue`
--

INSERT INTO `queue` (`pk_queue`, `status_queue`, `id_record`, `progon`) VALUES
(1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `record`
--

CREATE TABLE `record` (
  `pk_record` int(11) NOT NULL,
  `fk_disk` int(11) NOT NULL,
  `date_record` datetime NOT NULL,
  `status_record` int(11) NOT NULL,
  `status_string_record` varchar(50) NOT NULL,
  `success_flag` int(11) NOT NULL,
  `error_flag` int(11) NOT NULL,
  `success_print_flag` int(11) NOT NULL,
  `error_print_flag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `record`
--

INSERT INTO `record` (`pk_record`, `fk_disk`, `date_record`, `status_record`, `status_string_record`, `success_flag`, `error_flag`, `success_print_flag`, `error_print_flag`) VALUES
(18, 109, '2016-06-17 23:54:00', 4, 'Записан', 0, 0, 0, 0),
(19, 109, '2016-06-17 23:57:06', 4, 'Записан', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `name_user` varchar(100) NOT NULL,
  `pk_user` int(11) NOT NULL,
  `login_user` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`name_user`, `pk_user`, `login_user`) VALUES
('Администратор', 32, 'Администратор'),
('Максим', 38, 'maksim.karkavin');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `disk`
--
ALTER TABLE `disk`
  ADD PRIMARY KEY (`pk_disk`),
  ADD KEY `pk_record` (`pk_disk`),
  ADD KEY `fk_user` (`fk_user`);

--
-- Индексы таблицы `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`pk_file`),
  ADD UNIQUE KEY `pk_file` (`pk_file`),
  ADD UNIQUE KEY `pk_file_2` (`pk_file`),
  ADD KEY `fk_disk` (`fk_disk`) USING BTREE;

--
-- Индексы таблицы `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`pk_queue`);

--
-- Индексы таблицы `record`
--
ALTER TABLE `record`
  ADD PRIMARY KEY (`pk_record`),
  ADD UNIQUE KEY `pk_record` (`pk_record`),
  ADD KEY `fk_disk` (`fk_disk`) USING BTREE;

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`pk_user`),
  ADD KEY `pk_user` (`pk_user`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `disk`
--
ALTER TABLE `disk`
  MODIFY `pk_disk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;
--
-- AUTO_INCREMENT для таблицы `file`
--
ALTER TABLE `file`
  MODIFY `pk_file` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;
--
-- AUTO_INCREMENT для таблицы `queue`
--
ALTER TABLE `queue`
  MODIFY `pk_queue` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `record`
--
ALTER TABLE `record`
  MODIFY `pk_record` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `pk_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `disk`
--
ALTER TABLE `disk`
  ADD CONSTRAINT `disk_ibfk_1` FOREIGN KEY (`fk_user`) REFERENCES `user` (`pk_user`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`fk_disk`) REFERENCES `disk` (`pk_disk`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `record`
--
ALTER TABLE `record`
  ADD CONSTRAINT `record_ibfk_1` FOREIGN KEY (`fk_disk`) REFERENCES `disk` (`pk_disk`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
