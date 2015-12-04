-- phpMyAdmin SQL Dump
-- version 4.0.10
-- http://www.phpmyadmin.net
--
-- Host: sql.itcnet.ro
-- Generation Time: Dec 04, 2015 at 01:25 PM
-- Server version: 5.1.73
-- PHP Version: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `freeexactivewatch`
--

-- --------------------------------------------------------

--
-- Table structure for table `alerte`
--

CREATE TABLE IF NOT EXISTS `alerte` (
  `alerta_id` int(11) NOT NULL AUTO_INCREMENT,
  `alerta_nume` varchar(255) NOT NULL,
  `alerta_email` varchar(255) NOT NULL,
  `alerta_added_at` datetime NOT NULL,
  `alerta_added_by_ip` varchar(50) NOT NULL,
  `modified_at` datetime NOT NULL,
  PRIMARY KEY (`alerta_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `categ_id` int(11) NOT NULL AUTO_INCREMENT,
  `categ_name` varchar(150) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`categ_id`),
  KEY `visible` (`visible`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Table structure for table `conturi_admin`
--

CREATE TABLE IF NOT EXISTS `conturi_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nume` varchar(55) CHARACTER SET latin1 DEFAULT NULL,
  `prenume` varchar(55) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(55) CHARACTER SET latin1 DEFAULT NULL,
  `parola` varchar(55) CHARACTER SET latin1 DEFAULT NULL,
  `adresa_ip` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  `data` datetime DEFAULT NULL,
  `activ` tinyint(1) NOT NULL DEFAULT '1',
  `cont_tip` enum('superadmin','editor','contributor','') CHARACTER SET latin1 NOT NULL DEFAULT 'superadmin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime DEFAULT NULL,
  `ip` varchar(55) DEFAULT NULL,
  `query` text,
  `obs` varchar(255) DEFAULT NULL,
  `id_conturi` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1481 ;

-- --------------------------------------------------------

--
-- Table structure for table `mm_categs_alerte`
--

CREATE TABLE IF NOT EXISTS `mm_categs_alerte` (
  `alerta_id` int(11) NOT NULL,
  `categ_id` int(11) NOT NULL,
  UNIQUE KEY `mm_alerte_categ_idx` (`alerta_id`,`categ_id`),
  KEY `alerta_id` (`alerta_id`),
  KEY `categ_id` (`categ_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mm_categs_sesizari`
--

CREATE TABLE IF NOT EXISTS `mm_categs_sesizari` (
  `sesizare_id` int(11) NOT NULL,
  `categ_id` int(11) NOT NULL,
  UNIQUE KEY `mm_sesizare_categ_idx` (`sesizare_id`,`categ_id`),
  KEY `sesizare_id` (`sesizare_id`),
  KEY `categ_id` (`categ_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mm_cont_modul`
--

CREATE TABLE IF NOT EXISTS `mm_cont_modul` (
  `id_cont` int(11) NOT NULL,
  `id_modul` int(11) NOT NULL,
  `r` int(1) NOT NULL DEFAULT '0',
  `w` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mm_sesizari_embeds`
--

CREATE TABLE IF NOT EXISTS `mm_sesizari_embeds` (
  `sesizare_id` int(11) NOT NULL,
  `embed_sursa` text CHARACTER SET utf8 NOT NULL,
  `embed_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`embed_id`),
  KEY `sesizare_id` (`sesizare_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Table structure for table `mm_sesizari_images`
--

CREATE TABLE IF NOT EXISTS `mm_sesizari_images` (
  `sesizare_id` int(11) NOT NULL,
  `file_input` varchar(255) CHARACTER SET utf8 NOT NULL,
  `file_input_thumb` varchar(255) CHARACTER SET utf8 NOT NULL,
  `image_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`image_id`),
  KEY `sesizare_id` (`sesizare_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `mm_sesizari_linkuri`
--

CREATE TABLE IF NOT EXISTS `mm_sesizari_linkuri` (
  `sesizare_id` int(11) NOT NULL,
  `link_sursa` varchar(255) CHARACTER SET utf8 NOT NULL,
  `link_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`link_id`),
  KEY `sesizare_id` (`sesizare_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2107 ;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `module_slug` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) NOT NULL,
  `page_text` text NOT NULL,
  `page_date` datetime NOT NULL,
  `page_active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `sesizari`
--

CREATE TABLE IF NOT EXISTS `sesizari` (
  `sesizare_id` int(11) NOT NULL AUTO_INCREMENT,
  `sesizare_titlu` varchar(255) NOT NULL,
  `sesizare_descriere` text NOT NULL,
  `data_ora` datetime NOT NULL,
  `coord_lon` float(10,8) NOT NULL,
  `coord_lat` float(10,8) NOT NULL,
  `location_search` varchar(255) NOT NULL,
  `location_reverse` varchar(255) NOT NULL,
  `file_input` varchar(150) NOT NULL,
  `file_input_thumb` varchar(150) NOT NULL,
  `personal_nume` varchar(255) NOT NULL,
  `personal_prenume` varchar(255) NOT NULL,
  `personal_email` varchar(255) NOT NULL,
  `personal_telefon` varchar(100) NOT NULL,
  `added_at` datetime NOT NULL,
  `added_by_ip` varchar(50) NOT NULL,
  `validated` int(1) NOT NULL DEFAULT '0',
  `validation_code` varchar(32) NOT NULL,
  `modified_at` datetime NOT NULL,
  `change_log` text NOT NULL,
  `added_by_user_id` int(11) NOT NULL DEFAULT '0',
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`sesizare_id`),
  KEY `coord_lon` (`coord_lon`),
  KEY `data_ora` (`data_ora`),
  KEY `personal_email` (`personal_email`),
  KEY `coord_lat` (`coord_lat`),
  KEY `validation_code` (`validation_code`),
  KEY `validated` (`validated`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=648 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
