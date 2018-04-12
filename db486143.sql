-- phpMyAdmin SQL Dump
-- version 2.11.11.3
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.3
-- Erstellungszeit: 12. April 2018 um 20:33
-- Server Version: 5.6.19
-- PHP-Version: 4.4.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `db486143`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_cw_events`
--

DROP TABLE IF EXISTS `wp_cw_events`;
CREATE TABLE IF NOT EXISTS `wp_cw_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_start` int(11) NOT NULL,
  `event_end` int(11) NOT NULL,
  `event_day` int(11) NOT NULL,
  `event_name` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `event_subtext` text COLLATE utf8mb4_unicode_520_ci,
  `event_description` text COLLATE utf8mb4_unicode_520_ci,
  `event_color` text COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci AUTO_INCREMENT=40 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_cw_kurse`
--

DROP TABLE IF EXISTS `wp_cw_kurse`;
CREATE TABLE IF NOT EXISTS `wp_cw_kurse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `beauty_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `beschreibung` text COLLATE utf8mb4_unicode_520_ci,
  `max_teilnehmer` int(11) DEFAULT NULL,
  `bild` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `show_front` int(1) DEFAULT NULL,
  `is_open` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_cw_options`
--

DROP TABLE IF EXISTS `wp_cw_options`;
CREATE TABLE IF NOT EXISTS `wp_cw_options` (
  `register_enabled` int(1) NOT NULL,
  `shirt_enabled` int(1) NOT NULL,
  `teilnahme_preis` int(3) NOT NULL,
  `text_closed` text COLLATE utf8mb4_unicode_520_ci,
  `text_shirt` text COLLATE utf8mb4_unicode_520_ci,
  `text_email` text COLLATE utf8mb4_unicode_520_ci,
  `cw_start` date DEFAULT NULL,
  `register_start` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_cw_shirt`
--

DROP TABLE IF EXISTS `wp_cw_shirt`;
CREATE TABLE IF NOT EXISTS `wp_cw_shirt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `size` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `preis` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_cw_user`
--

DROP TABLE IF EXISTS `wp_cw_user`;
CREATE TABLE IF NOT EXISTS `wp_cw_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vorname` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `nachname` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `str` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `plz` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `ort` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `geb` date NOT NULL,
  `schule` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `essen` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `sonstiges` text COLLATE utf8mb4_unicode_520_ci,
  `gotit` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `regdate` datetime DEFAULT NULL,
  `payed` int(1) DEFAULT NULL,
  `shirt_payed` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci AUTO_INCREMENT=79 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_cw_user_kurs`
--

DROP TABLE IF EXISTS `wp_cw_user_kurs`;
CREATE TABLE IF NOT EXISTS `wp_cw_user_kurs` (
  `user_id` int(11) NOT NULL,
  `kurs_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_cw_user_shirt`
--

DROP TABLE IF EXISTS `wp_cw_user_shirt`;
CREATE TABLE IF NOT EXISTS `wp_cw_user_shirt` (
  `user_id` int(11) NOT NULL,
  `shirt_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
