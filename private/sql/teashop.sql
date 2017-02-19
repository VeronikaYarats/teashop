-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Фев 20 2017 г., 02:12
-- Версия сервера: 5.5.43-0ubuntu0.14.04.1
-- Версия PHP: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `veroshop`
--

-- --------------------------------------------------------

--
-- Структура таблицы `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL COMMENT 'Ключ',
  `page_title` varchar(100) NOT NULL COMMENT 'Title страницы',
  `name` varchar(100) NOT NULL COMMENT 'Название статьи',
  `contents` varchar(5000) NOT NULL COMMENT 'Содержание статьи',
  `public` tinyint(1) NOT NULL COMMENT 'Опубликовать',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица статей' AUTO_INCREMENT=112 ;

-- --------------------------------------------------------

--
-- Структура таблицы `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `files` varchar(500) DEFAULT NULL,
  `alt` varchar(500) DEFAULT NULL,
  `title` varchar(500) NOT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `original_filename` varchar(50) DEFAULT NULL,
  `enable` tinyint(3) DEFAULT '1',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=130 ;

-- --------------------------------------------------------

--
-- Структура таблицы `object_images`
--

DROP TABLE IF EXISTS `object_images`;
CREATE TABLE `object_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_type` enum('articles','products') DEFAULT NULL,
  `obj_id` int(50) DEFAULT NULL,
  `img_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `img_id` (`img_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=154 ;

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Название продукта',
  `product_category_id` int(11) NOT NULL COMMENT 'Категория продукта',
  `country` varchar(100) NOT NULL COMMENT 'Страна производитель',
  `weight` int(11) NOT NULL COMMENT 'Масса нетто',
  `price` int(11) NOT NULL COMMENT 'Цена',
  `public` tinyint(1) NOT NULL COMMENT 'Опубликовать',
  `trade_mark` varchar(100) NOT NULL COMMENT 'торговая марка',
  PRIMARY KEY (`id`),
  KEY `product_category_id` (`product_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица продуктов' AUTO_INCREMENT=123 ;

-- --------------------------------------------------------

--
-- Структура таблицы `product_category`
--

DROP TABLE IF EXISTS `product_category`;
CREATE TABLE `product_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL COMMENT 'Название категории(чай, кофе, экскаватор)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица категории продуктов: чай, кофе, экскаватор...' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Структура таблицы `product_properties`
--

DROP TABLE IF EXISTS `product_properties`;
CREATE TABLE `product_properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_category_id` int(11) NOT NULL COMMENT 'ID категории продукта',
  `name` varchar(100) NOT NULL COMMENT 'Название свойства продукта',
  `type` enum('string','integer','enum') NOT NULL COMMENT 'Тип принимаемого значения для свойства продукта',
  PRIMARY KEY (`id`),
  KEY `product_category_id` (`product_category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица свойств категорий продукутов' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Структура таблицы `product_properties_values`
--

DROP TABLE IF EXISTS `product_properties_values`;
CREATE TABLE `product_properties_values` (
  `product_id` int(11) NOT NULL COMMENT 'id продукта',
  `property_id` int(11) NOT NULL COMMENT 'id свойства',
  `value` varchar(100) NOT NULL COMMENT 'значение свойства для продукта ',
  KEY `product_id` (`product_id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица значений свойств продуктов';

-- --------------------------------------------------------

--
-- Структура таблицы `product_property_enum`
--

DROP TABLE IF EXISTS `product_property_enum`;
CREATE TABLE `product_property_enum` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL COMMENT 'id свойства',
  `variant` varchar(100) NOT NULL COMMENT 'вариант значения свойства',
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Таблица возможных значений свойств с типом enum' AUTO_INCREMENT=10 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `object_images`
--
ALTER TABLE `object_images`
  ADD CONSTRAINT `object_images_ibfk_1` FOREIGN KEY (`img_id`) REFERENCES `images` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_category_id`) REFERENCES `product_category` (`id`) ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `product_properties`
--
ALTER TABLE `product_properties`
  ADD CONSTRAINT `product_properties_ibfk_1` FOREIGN KEY (`product_category_id`) REFERENCES `product_category` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `product_properties_values`
--
ALTER TABLE `product_properties_values`
  ADD CONSTRAINT `product_properties_values_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `product_properties_values_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `product_properties` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `product_property_enum`
--
ALTER TABLE `product_property_enum`
  ADD CONSTRAINT `product_property_enum_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `product_properties` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
