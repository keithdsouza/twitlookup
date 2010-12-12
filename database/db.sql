-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: techie.kissgeeks.com
-- Generation Time: Dec 12, 2010 at 01:32 PM
-- Server version: 5.1.39
-- PHP Version: 5.2.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `twitterlookup`
--

-- --------------------------------------------------------

--
-- Table structure for table `internal_status`
--

DROP TABLE IF EXISTS `internal_status`;
CREATE TABLE IF NOT EXISTS `internal_status` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_queries`
--

DROP TABLE IF EXISTS `twitter_queries`;
CREATE TABLE IF NOT EXISTS `twitter_queries` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `int_user_id` int(4) NOT NULL,
  `status_id` bigint(20) NOT NULL,
  `message` varchar(255) NOT NULL,
  `status` smallint(2) NOT NULL,
  `replied_date` varchar(255) DEFAULT NULL,
  `direct_message` smallint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `status_id` (`status_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=450 ;

-- --------------------------------------------------------

--
-- Table structure for table `twitter_users`
--

DROP TABLE IF EXISTS `twitter_users`;
CREATE TABLE IF NOT EXISTS `twitter_users` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) DEFAULT NULL,
  `screen_name` varchar(255) DEFAULT NULL,
  `blocked` tinyint(1) DEFAULT '0',
  `create_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=240 ;