-- phpMyAdmin SQL Dump
-- version 4.1.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 22, 2015 at 05:20 AM
-- Server version: 5.0.96-community
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `phpmysql_cart_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `cash_counter_feed`
--

CREATE TABLE IF NOT EXISTS `cash_counter_feed` (
  `cash_counter_feed_id` int(11) NOT NULL auto_increment,
  `cash_amount` double(10,2) default NULL,
  `user_id` int(11) default NULL,
  `feed_time` datetime default NULL,
  PRIMARY KEY  (`cash_counter_feed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(11) NOT NULL auto_increment,
  `category_name` varchar(255) NOT NULL,
  `category_description` varchar(255) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_description`) VALUES
(1, 'cat a', ''),
(2, 'cat b', ''),
(3, 'cat c', ''),
(4, 'cat d', '');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) NOT NULL,
  `order_date` datetime default NULL,
  `order_total` double(10,2) default NULL,
  `user_id` int(11) default NULL,
  PRIMARY KEY  (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE IF NOT EXISTS `order_details` (
  `order_detail_id` int(11) NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) default NULL,
  `no_of_products` int(11) default NULL,
  PRIMARY KEY  (`order_detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `product_id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `product_description` text NOT NULL,
  `feature_image` text NOT NULL,
  `created_date` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY  (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `category_id`, `product_name`, `price`, `product_description`, `feature_image`, `created_date`, `status`, `qty`) VALUES
(1, 2, 'poduct 1', 234.32, 'Sed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.\n\nSed sapien sapien, vulputate ac varius vitae, rutrum ul', 'p1.jpg', '2015-01-05', 0, 5),
(3, 3, 'poduct 3', 100, 'Sed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.\n\nSed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.', 'p4.jpg', '2015-01-17', 0, 21),
(4, 4, 'poduct 4', 100.98, 'Sed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.\n\nSed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.', 'p5.jpg', '2015-01-09', 0, 12),
(5, 1, 'poduct 15', 560, 'Sed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.\n\nSed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.', 'p1.jpg', '2015-01-05', 0, 20),
(6, 1, 'poduct 12', 234, 'Sed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.\n\nSed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.', 'p2.jpg', '2015-01-06', 0, 15),
(8, 1, 'poduct 14', 121, 'Sed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.\n\nSed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.', 'p5.jpg', '2015-01-09', 0, 32),
(10, 2, 'poduct 212', 440, 'Sed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.\n\nSed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.', 'p2.jpg', '2015-01-06', 0, 10),
(11, 2, 'poduct 313', 420, 'Sed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.\n\nSed sapien sapien, vulputate ac varius vitae, rutrum ultrices odio. Morbi vel tortor enim. Praesent lobortis gravida pretium. Vestibulum faucibus pellentesque metus, nec convallis mauris congue sed.', 'p4.jpg', '2015-01-17', 0, 21),
(14, 4, 'lorem ipsum', 12, 'lrem ipsum', '3.08773243326E+15_aaaa.jpg', '2015-01-13', 1, 12),
(15, 3, 'qwe', 21, 'qweq', '3.915180432E+15_aaaa.jpg', '2015-01-13', 1, 2133),
(16, 4, 'ABC', 110, 'adasdas', '2.90399708994E+15_aaaa.jpg', '2015-01-13', 1, 12),
(17, 4, 'test', 3234, 'sdsd', '9.95180824071E+14_ad.jpg', '2015-01-13', 1, 230),
(18, 4, 'ewrwer', 23, 'werwe', '1.33808746953E+14_aaaa.jpg', '2015-01-13', 1, 432423),
(19, 4, 'qw', 23, 'wr', '3.26230944837E+15_aaaa.jpg', '2015-01-13', 1, 234);

-- --------------------------------------------------------

--
-- Table structure for table `product_history`
--

CREATE TABLE IF NOT EXISTS `product_history` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `new_qty` int(11) NOT NULL,
  `add_type` enum('+','-') NOT NULL,
  `added_date` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `product_history`
--

INSERT INTO `product_history` (`id`, `user_id`, `product_id`, `new_qty`, `add_type`, `added_date`) VALUES
(1, 1, 10, 5, '-', '2015-01-14 03:31:54'),
(2, 1, 8, 20, '+', '2015-01-14 03:38:29'),
(3, 1, 17, 1, '-', '2015-01-14 03:40:47'),
(4, 1, 17, 3, '-', '2015-01-14 03:41:21'),
(5, 1, 5, 10, '+', '2015-01-14 04:53:55'),
(6, 1, 5, 5, '-', '2015-01-14 04:54:07'),
(7, 1, 5, 10, '+', '2015-01-14 04:54:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `is_admin` int(1) NOT NULL default '0',
  `session` varchar(32) NOT NULL,
  `created` date NOT NULL,
  `status` int(3) NOT NULL,
  `last_login` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `is_admin`, `session`, `created`, `status`, `last_login`) VALUES
(1, 'gayan', '202cb962ac59075b964b07152d234b70', 'gayan@123.com', 1, '', '2015-01-05', 1, '2015-01-21 21:07:47'),
(2, 'teran', '202cb962ac59075b964b07152d234b70', 'email2me@gmail.com', 1, '', '2015-01-14', 1, '2015-01-16 12:46:54'),
(3, 'saman', '202cb962ac59075b964b07152d234b70', '', 0, '', '0000-00-00', 0, '2015-01-16 07:12:26');

-- --------------------------------------------------------

--
-- Table structure for table `user_login_history`
--

CREATE TABLE IF NOT EXISTS `user_login_history` (
  `user_login_history_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `user_login_time` datetime default NULL,
  `login_type` enum('OUT','LOG') default 'LOG',
  `user_logout_time` datetime default NULL,
  `logout_method` enum('AWAY','NULL','OFF') default 'NULL',
  PRIMARY KEY  (`user_login_history_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `user_login_history`
--

INSERT INTO `user_login_history` (`user_login_history_id`, `user_id`, `user_login_time`, `login_type`, `user_logout_time`, `logout_method`) VALUES
(1, 1, '2015-01-14 03:30:00', 'LOG', NULL, 'NULL'),
(2, 2, '2015-01-14 05:00:23', 'LOG', NULL, 'NULL'),
(3, NULL, NULL, 'OUT', '2015-01-16 07:12:13', 'OFF'),
(4, 3, '2015-01-16 07:12:26', 'LOG', NULL, 'NULL'),
(5, 2, '2015-01-16 07:17:44', 'LOG', NULL, 'NULL'),
(6, 2, '2015-01-16 07:47:55', 'LOG', NULL, 'NULL'),
(7, 2, '2015-01-16 12:46:54', 'LOG', NULL, 'NULL'),
(8, 1, '2015-01-19 10:50:53', 'LOG', NULL, 'NULL'),
(9, 1, '2015-01-20 11:19:57', 'LOG', NULL, 'NULL'),
(10, 1, '2015-01-21 09:07:47', 'LOG', NULL, 'NULL');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
