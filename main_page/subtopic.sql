-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 30 2017 г., 00:12
-- Версия сервера: 5.7.14
-- Версия PHP: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `altyn_bilim`
--

-- --------------------------------------------------------

--
-- Структура таблицы `subtopic`
--

CREATE TABLE `subtopic` (
  `id` int(4) NOT NULL,
  `subtopic_num` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `topic_num` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `subtopic_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `subtopic`
--

INSERT INTO `subtopic` (`id`, `subtopic_num`, `topic_num`, `subtopic_name`) VALUES
(1, 'S_T5944286341a91299428524', 'T5944284bc2e31890275127', 'Центрге тартқаш күш'),
(2, 'S_T5944287e50caa185632691', 'T5944284bc2e31890275127', 'Үдеу'),
(3, 'S_T5948f9161dfa3576498714', 'T5948f902123d4580302478', 'Екі айнымалы теңдеулер жүйесі'),
(4, 'S_T594a9b2ca3ce6103707370', 'T59443bef07fb1589126832', 'Вектор');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `subtopic`
--
ALTER TABLE `subtopic`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subtopic_num` (`subtopic_num`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `subtopic`
--
ALTER TABLE `subtopic`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
