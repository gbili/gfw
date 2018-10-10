
-- MySQL dump 10.11
--
-- Host: localhost    Database: minerengine
-- ------------------------------------------------------
-- Server version	5.0.88

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
-- Table structure for table `BAction`
--

DROP TABLE IF EXISTS `BAction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BAction` (
  `bActionId` int(10) unsigned NOT NULL auto_increment,
  `bParentActionId` int(10) unsigned NOT NULL default '0',
  `bId` int(10) unsigned NOT NULL default '0',
  `execRank` int(10) unsigned NOT NULL default '0',
  `inputParentRegexGroup` varchar(255) NOT NULL default '',
  `type` int(10) unsigned NOT NULL default '0',
  `useMatchAll` int(10) unsigned NOT NULL default '0',
  `isOpt` int(10) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bActionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BAction`
--

LOCK TABLES `BAction` WRITE;
/*!40000 ALTER TABLE `BAction` DISABLE KEYS */;
/*!40000 ALTER TABLE `BAction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BAction_r_InjectedBAction`
--

DROP TABLE IF EXISTS `BAction_r_InjectedBAction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BAction_r_InjectedBAction` (
  `bActionId` int(10) unsigned NOT NULL default '0',
  `injectedActionId` int(10) unsigned NOT NULL default '0',
  `inputGroup` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bActionId`, `injectedActionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BAction_r_InjectedBAction`
--

LOCK TABLES `BAction_r_InjectedBAction` WRITE;
/*!40000 ALTER TABLE `BAction_r_InjectedBAction` DISABLE KEYS */;
/*!40000 ALTER TABLE `BAction_r_InjectedBAction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Callable`
--

DROP TABLE IF EXISTS `Callable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Callable` (
  `bActionId` int(10) unsigned NOT NULL default '0',
  `methodName` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`bActionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BAction_Callable`
--

LOCK TABLES `BAction_Callable` WRITE;
/*!40000 ALTER TABLE `BAction_Callable` DISABLE KEYS */;
/*!40000 ALTER TABLE `BAction_Callable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BAction_Data`
--

DROP TABLE IF EXISTS `BAction_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BAction_Data` (
  `bActionId` int(10) unsigned NOT NULL default '0',
  `data` text NOT NULL,
  PRIMARY KEY  (`bActionId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BAction_Data`
--

LOCK TABLES `BAction_Data` WRITE;
/*!40000 ALTER TABLE `BAction_Data` DISABLE KEYS */;
/*!40000 ALTER TABLE `BAction_Data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Content`
--

DROP TABLE IF EXISTS `Content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Content` (
  `cId` int(10) unsigned NOT NULL auto_increment,
  `url` text NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`cId`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Content`
--

LOCK TABLES `Content` WRITE;
/*!40000 ALTER TABLE `Content` DISABLE KEYS */;
/*!40000 ALTER TABLE `Content` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `BAction_ErrorData`
--

DROP TABLE IF EXISTS `BAction_ErrorData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BAction_ErrorData` (
  `bNIGPActionId` int(10) unsigned NOT NULL default '0',
  `nIGPLastInputData` text NOT NULL,
  `errorTriggerActionId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bNIGPActionId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BAction_ErrorData`
--

LOCK TABLES `BAction_ErrorData` WRITE;
/*!40000 ALTER TABLE `BAction_ErrorData` DISABLE KEYS */;
/*!40000 ALTER TABLE `BAction_ErrorData` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BAction_RegexGroup_r_Callable_ParamNum`
--

DROP TABLE IF EXISTS `BAction_RegexGroup_r_Callable_ParamNum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BAction_RegexGroup_r_Callable_ParamNum` (
  `bActionId` int(10) unsigned NOT NULL default '0',
  `paramNum` int(10) unsigned NOT NULL default '0',
  `regexGroup` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bActionId`,`paramNum`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BAction_RegexGroup_r_Callable_ParamNum`
--

LOCK TABLES `BAction_RegexGroup_r_Callable_ParamNum` WRITE;
/*!40000 ALTER TABLE `BAction_RegexGroup_r_Callable_ParamNum` DISABLE KEYS */;
/*!40000 ALTER TABLE `BAction_RegexGroup_r_Callable_ParamNum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BAction_RegexGroup_r_Const`
--

DROP TABLE IF EXISTS `BAction_RegexGroup_r_Const`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BAction_RegexGroup_r_Const` (
  `bActionId` int(10) unsigned NOT NULL default '0',
  `regexGroup` varchar(255) NOT NULL default '',
  `const` int(10) unsigned NOT NULL default '0',
  `isOpt` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bActionId`,`regexGroup`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BAction_RegexGroup_r_Const`
--

LOCK TABLES `BAction_RegexGroup_r_Const` WRITE;
/*!40000 ALTER TABLE `BAction_RegexGroup_r_Const` DISABLE KEYS */;
/*!40000 ALTER TABLE `BAction_RegexGroup_r_Const` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BAction_RegexGroup_r_Callable`
--

DROP TABLE IF EXISTS `BAction_RegexGroup_r_Callable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BAction_RegexGroup_r_Callable` (
  `bAMMId` int(10) unsigned NOT NULL auto_increment,
  `bActionId` int(10) unsigned NOT NULL default '0',
  `methodId` int(10) unsigned NOT NULL default '0',
  `regexGroup` int(10) unsigned NOT NULL default '0',
  `interceptType` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bAMMId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BAction_RegexGroup_r_Callable`
--

LOCK TABLES `BAction_RegexGroup_r_Callable` WRITE;
/*!40000 ALTER TABLE `BAction_RegexGroup_r_Callable` DISABLE KEYS */;
/*!40000 ALTER TABLE `BAction_RegexGroup_r_Callable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Blueprint`
--

DROP TABLE IF EXISTS `Blueprint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Blueprint` (
  `bId` int(10) unsigned NOT NULL auto_increment,
  `host` varchar(255) NOT NULL default '',
  `newInstanceGeneratingPointActionId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Blueprint`
--

LOCK TABLES `Blueprint` WRITE;
/*!40000 ALTER TABLE `Blueprint` DISABLE KEYS */;
/*!40000 ALTER TABLE `Blueprint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Blueprint_CMPaths`
--

DROP TABLE IF EXISTS `Blueprint_CMPaths`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Blueprint_CMPaths` (
  `bId` int(10) unsigned NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `pathType` int(10) unsigned NOT NULL default '0',
  `classType` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`bId`,`pathType`,`classType`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Blueprint_CMPaths`
--

LOCK TABLES `Blueprint_CMPaths` WRITE;
/*!40000 ALTER TABLE `Blueprint_CMPaths` DISABLE KEYS */;
/*!40000 ALTER TABLE `Blueprint_CMPaths` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Blueprint_Callable`
--

DROP TABLE IF EXISTS `Blueprint_Callable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Blueprint_Callable` (
  `methodId` int(10) unsigned NOT NULL auto_increment,
  `bId` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`methodId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Blueprint_Callable`
--

LOCK TABLES `Blueprint_Callable` WRITE;
/*!40000 ALTER TABLE `Blueprint_Callable` DISABLE KEYS */;
/*!40000 ALTER TABLE `Blueprint_Callable` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-11-26  4:28:14
