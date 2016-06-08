--
-- База данных: `medvedbd`
--

-- --------------------------------------------------------

--
-- Структура таблицы `magaz`
--

CREATE TABLE `magaz` (
  `pk_magaz` int(11) NOT NULL,
  `name_magaz` varchar(100) NOT NULL,
  `adres_magaz` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `magaz`
--

INSERT INTO `magaz` (`pk_magaz`, `name_magaz`, `adres_magaz`) VALUES
(1, 'Детский мир', 'Ленина, 51'),
(2, 'Мир игрушек', 'Попова, 17');

-- --------------------------------------------------------

--
-- Структура таблицы `mishka`
--

CREATE TABLE `mishka` (
  `pk_mishka` int(11) NOT NULL,
  `fk_producer` int(11) NOT NULL,
  `name_mishka` varchar(100) NOT NULL,
  `color_mishka` varchar(100) NOT NULL,
  `weight_mishka` double NOT NULL,
  `price_mishka` double NOT NULL,
  `image_mishka` text NOT NULL,
  `description_mishka` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `mishka`
--

INSERT INTO `mishka` (`pk_mishka`, `fk_producer`, `name_mishka`, `color_mishka`, `weight_mishka`, `price_mishka`, `image_mishka`, `description_mishka`) VALUES
(1, 1, 'Добрый мишка', 'Темно-коричневый', 0.35, 900, 'images\\med2.jpg', 'Восхитительный медвежонок станет лучшим другом для Вашего ребенка. С этой мягкой и пушистой игрушкой не захочется расставаться. Отличный подарок и мальчику, и девочке.'),
(2, 2, 'Милый мишка', 'Светло-коричневый', 0.25, 750, 'images\\med1.jpg', 'Восхитительный медвежонок станет лучшим другом для Вашего ребенка. С этой мягкой и пушистой игрушкой не захочется расставаться. Отличный подарок и мальчику, и девочке.');

-- --------------------------------------------------------

--
-- Структура таблицы `mishkamagaz`
--

CREATE TABLE `mishkamagaz` (
  `pk_mishkamagaz` int(11) NOT NULL,
  `fk_mishka` int(11) NOT NULL,
  `fk_magaz` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `mishkamagaz`
--

INSERT INTO `mishkamagaz` (`pk_mishkamagaz`, `fk_mishka`, `fk_magaz`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `producer`
--

CREATE TABLE `producer` (
  `pk_producer` int(11) NOT NULL,
  `name_producer` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `producer`
--

INSERT INTO `producer` (`pk_producer`, `name_producer`) VALUES
(1, 'Teddy Bear'),
(2, 'ООО "Мягкие игрушки"'),
(3, 'Hasbro'),
(4, 'Astrel'),
(5, 'Children Toys');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `magaz`
--
ALTER TABLE `magaz`
  ADD PRIMARY KEY (`pk_magaz`),
  ADD UNIQUE KEY `pk_magaz` (`pk_magaz`);

--
-- Индексы таблицы `mishka`
--
ALTER TABLE `mishka`
  ADD PRIMARY KEY (`pk_mishka`),
  ADD UNIQUE KEY `pk_mishka` (`pk_mishka`),
  ADD KEY `fk_producer` (`fk_producer`);

--
-- Индексы таблицы `mishkamagaz`
--
ALTER TABLE `mishkamagaz`
  ADD PRIMARY KEY (`pk_mishkamagaz`),
  ADD KEY `fk_mishka` (`fk_mishka`),
  ADD KEY `fk_magaz` (`fk_magaz`);

--
-- Индексы таблицы `producer`
--
ALTER TABLE `producer`
  ADD PRIMARY KEY (`pk_producer`),
  ADD UNIQUE KEY `pk_producer` (`pk_producer`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `magaz`
--
ALTER TABLE `magaz`
  MODIFY `pk_magaz` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `mishka`
--
ALTER TABLE `mishka`
  MODIFY `pk_mishka` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `mishkamagaz`
--
ALTER TABLE `mishkamagaz`
  MODIFY `pk_mishkamagaz` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `producer`
--
ALTER TABLE `producer`
  MODIFY `pk_producer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `mishka`
--
ALTER TABLE `mishka`
  ADD CONSTRAINT `mishka_ibfk_1` FOREIGN KEY (`fk_producer`) REFERENCES `producer` (`pk_producer`);

--
-- Ограничения внешнего ключа таблицы `mishkamagaz`
--
ALTER TABLE `mishkamagaz`
  ADD CONSTRAINT `mishkamagaz_ibfk_1` FOREIGN KEY (`fk_mishka`) REFERENCES `mishka` (`pk_mishka`),
  ADD CONSTRAINT `mishkamagaz_ibfk_2` FOREIGN KEY (`fk_magaz`) REFERENCES `magaz` (`pk_magaz`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
