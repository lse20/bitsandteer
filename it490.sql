-- MySQL dump 10.13  Distrib 5.7.21, for Linux (x86_64)
--
-- Host: localhost    Database: it490
-- ------------------------------------------------------
-- Server version	5.7.21-0ubuntu0.16.04.1

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
-- Table structure for table `Doctors`
--

DROP TABLE IF EXISTS `Doctors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Doctors` (
  `username` varchar(10) NOT NULL,
  `password` varchar(20) DEFAULT NULL,
  `license` int(20) NOT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `gender` varchar(1) DEFAULT NULL,
  `specialization` varchar(30) DEFAULT NULL,
  `rating` int(2) DEFAULT NULL,
  `review` varchar(500) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone` int(15) DEFAULT NULL,
  `location` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `license` (`license`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Doctors`
--

LOCK TABLES `Doctors` WRITE;
/*!40000 ALTER TABLE `Doctors` DISABLE KEYS */;
INSERT INTO `Doctors` VALUES ('100001','test',10,'Gregory','House','Gregory House','M','Bullshit',1,'Worst Doctor Ever.\" Really.\" Really.Fuck you, asshole.','ghouse@deepshit.com',2337002,'Jail'),('rich','pass',12345678,'Rich','Doza','Rich Doza','m','Lolicology',0,'hes a lolicon','iamaloli@loli.con',2,'Japan');
/*!40000 ALTER TABLE `Doctors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patientRecords`
--

DROP TABLE IF EXISTS `patientRecords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patientRecords` (
  `username` varchar(10) NOT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `height` varchar(3) DEFAULT NULL,
  `weight` varchar(3) DEFAULT NULL,
  `sex` varchar(1) DEFAULT NULL,
  `diagnosis` varchar(60) DEFAULT NULL,
  `drNote` varchar(100) DEFAULT NULL,
  `doctor` int(20) DEFAULT NULL,
  `prescription` varchar(40) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patientRecords`
--

LOCK TABLES `patientRecords` WRITE;
/*!40000 ALTER TABLE `patientRecords` DISABLE KEYS */;
INSERT INTO `patientRecords` VALUES ('1','Robert','Baratheon','Robert Baratheon',NULL,36,'6\'6','300','M','Too fat for his armor','Needs to drink lessFuck you, asshole.Fuck you, asshole.',12345678,'exercise',NULL),('2','','','Ned Stark',NULL,2,'6\'2','','','','',10,'',NULL);
/*!40000 ALTER TABLE `patientRecords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `n1` varchar(10) DEFAULT NULL,
  `n2` varchar(10) DEFAULT NULL,
  `n3` varchar(10) DEFAULT NULL,
  `n4` varchar(10) DEFAULT NULL,
  `n5` varchar(10) DEFAULT NULL,
  `n6` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `test`
--

LOCK TABLES `test` WRITE;
/*!40000 ALTER TABLE `test` DISABLE KEYS */;
INSERT INTO `test` VALUES ('alice','bob','carol','doug','eliza','frank');
/*!40000 ALTER TABLE `test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uAuth`
--

DROP TABLE IF EXISTS `uAuth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uAuth` (
  `user` varchar(20) NOT NULL,
  `pass` varchar(20) NOT NULL,
  `type` enum('p','d') NOT NULL,
  `login` int(7) DEFAULT NULL,
  `failedLog` int(7) DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uAuth`
--

LOCK TABLES `uAuth` WRITE;
/*!40000 ALTER TABLE `uAuth` DISABLE KEYS */;
INSERT INTO `uAuth` VALUES ('1','test','p',NULL,NULL),('ben','pass','p',NULL,NULL),('bobbyb','openfield','p',NULL,NULL),('ghouse','edgelord','d',NULL,NULL),('nstark','honor4life','p',NULL,NULL),('rich','pass','d',NULL,NULL);
/*!40000 ALTER TABLE `uAuth` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-04-24 19:57:02
