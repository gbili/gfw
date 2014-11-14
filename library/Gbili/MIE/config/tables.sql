--
-- Table structure for table `MIE`
--

DROP TABLE IF EXISTS `MIE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MIE` (
  `mIEId` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default 'N/A',
  `countryId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`mIEId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MIE`
--

LOCK TABLES `MIE` WRITE;
/*!40000 ALTER TABLE `MIE` DISABLE KEYS */;
/*!40000 ALTER TABLE `MIE` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MIERole`
--

DROP TABLE IF EXISTS `MIERole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MIERole` (
  `mIERoleId` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`mIERoleId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MIERole`
--

LOCK TABLES `MIERole` WRITE;
/*!40000 ALTER TABLE `MIERole` DISABLE KEYS */;
INSERT INTO `MIERole` (`name`,`slug`) VALUES ('Actor','actor'),('Producer','producer'),('Director','director');
/*!40000 ALTER TABLE `MIERole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MIE_Description`
--

DROP TABLE IF EXISTS `MIE_Description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MIE_Description` (
  `mIEId` int(10) unsigned NOT NULL auto_increment,
  `description` text NOT NULL,
  `langISOId` varchar(255) NOT NULL default '',
  `url` varchar(255) default NULL,
  PRIMARY KEY  (`mIEId`,`langISOId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MIE_Description`
--

LOCK TABLES `MIE_Description` WRITE;
/*!40000 ALTER TABLE `MIE_Description` DISABLE KEYS */;
/*!40000 ALTER TABLE `MIE_Description` ENABLE KEYS */;
UNLOCK TABLES;
