-- phpMyAdmin SQL Dump
-- version 2.11.4-rc1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 08, 2011 at 06:13 AM
-- Server version: 5.0.27
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `incog_mobility`
--

-- --------------------------------------------------------

--
-- Table structure for table `provider`
--

CREATE TABLE IF NOT EXISTS `providers` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `provider`
--

INSERT INTO `provider` (`id`, `name`, `contact_email`) VALUES
(1, 'Crazy Hazim''s Discount Donkey Rides', 'hazim@chddr.com');

-- --------------------------------------------------------

--
-- Table structure for table `provider_type`
--

CREATE TABLE IF NOT EXISTS `providers_types` (
  `id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `provider_type`
--

INSERT INTO `provider_type` (`id`, `provider_id`, `type_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE IF NOT EXISTS `requests` (
  `id` int(11) NOT NULL auto_increment,
  `rider_id` int(11) NOT NULL,
  `zip` int(11) NOT NULL,
  `detail` text,
  `audio_url` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `request`
--


-- --------------------------------------------------------

--
-- Table structure for table `rider`
--

CREATE TABLE IF NOT EXISTS `riders` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `notes` text,
  `default_zip` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `rider`
--

INSERT INTO `riders` (`id`, `name`, `phone`, `notes`, `default_zip`) VALUES
(1, 'Daphne Morehead', '918-987-6543', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rider_type`
--

CREATE TABLE IF NOT EXISTS `riders_types` (
  `id` int(11) NOT NULL,
  `rider_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rider_type`
--

INSERT INTO `riders_types` (`id`, `rider_id`, `type_id`) VALUES
(0, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `type`
--

INSERT INTO `types` (`id`, `name`) VALUES
(0, 'Veteran');
