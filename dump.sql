-- MySQL dump 10.11
--
-- Host: localhost    Database: ranking
-- ------------------------------------------------------
-- Server version	5.0.51a-24+lenny5

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `clients` (
  `clients_id` int(11) NOT NULL auto_increment,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY  (`clients_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'Default client');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients_has_domains`
--

DROP TABLE IF EXISTS `clients_has_domains`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `clients_has_domains` (
  `clients_id` int(11) NOT NULL,
  `domains_id` int(11) NOT NULL,
  PRIMARY KEY  (`domains_id`),
  KEY `clients_id` (`clients_id`,`domains_id`),
  CONSTRAINT `clients_has_domains_ibfk_1` FOREIGN KEY (`clients_id`) REFERENCES `clients` (`clients_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `clients_has_domains_ibfk_2` FOREIGN KEY (`domains_id`) REFERENCES `domains` (`domains_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `clients_has_domains`
--

LOCK TABLES `clients_has_domains` WRITE;
/*!40000 ALTER TABLE `clients_has_domains` DISABLE KEYS */;
INSERT INTO `clients_has_domains` VALUES (1,1);
/*!40000 ALTER TABLE `clients_has_domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `domains` (
  `domains_id` int(11) NOT NULL auto_increment,
  `domain` varchar(100) NOT NULL,
  `pages_to_crawl` int(5) NOT NULL default '1',
  `domain_min_sleep` int(5) NOT NULL,
  `domain_max_sleep` int(5) NOT NULL,
  `keyword_min_sleep` int(5) NOT NULL,
  `keyword_max_sleep` int(5) NOT NULL,
  PRIMARY KEY  (`domains_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
INSERT INTO `domains` VALUES (1,'www.webmug.de',1,10,60,2,5);
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `domains_has_keywords`
--

DROP TABLE IF EXISTS `domains_has_keywords`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `domains_has_keywords` (
  `domains_id` int(11) NOT NULL,
  `keywords_id` int(11) NOT NULL,
  KEY `keywords_id` (`keywords_id`),
  KEY `domains_id` (`domains_id`),
  CONSTRAINT `domains_has_keywords_ibfk_1` FOREIGN KEY (`domains_id`) REFERENCES `domains` (`domains_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `domains_has_keywords_ibfk_2` FOREIGN KEY (`keywords_id`) REFERENCES `keywords` (`keywords_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `domains_has_keywords`
--

LOCK TABLES `domains_has_keywords` WRITE;
/*!40000 ALTER TABLE `domains_has_keywords` DISABLE KEYS */;
INSERT INTO `domains_has_keywords` VALUES (1,1);
/*!40000 ALTER TABLE `domains_has_keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `keywords`
--

DROP TABLE IF EXISTS `keywords`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `keywords` (
  `keywords_id` int(11) NOT NULL auto_increment,
  `keyword` varchar(200) NOT NULL,
  `prio` int(6) NOT NULL,
  PRIMARY KEY  (`keywords_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `keywords`
--

LOCK TABLES `keywords` WRITE;
/*!40000 ALTER TABLE `keywords` DISABLE KEYS */;
INSERT INTO `keywords` VALUES (1,'webmug',100);
/*!40000 ALTER TABLE `keywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `results` (
  `date` date NOT NULL,
  `keywords_id` varchar(11) NOT NULL,
  `pos_google` varchar(6) NOT NULL,
  `pos_bing` varchar(6) NOT NULL,
  UNIQUE KEY `uniq` (`date`,`keywords_id`,`pos_google`,`pos_bing`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `results`
--
