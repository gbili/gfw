--
-- Table structure for table `SourceHost`
--

DROP TABLE IF EXISTS `SourceHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SourceHost` (
  `sourceHostId` int(10) unsigned NOT NULL auto_increment,
  `host` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`sourceHostId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SourceHost`
--

LOCK TABLES `SourceHost` WRITE;
/*!40000 ALTER TABLE `SourceHost` DISABLE KEYS */;
INSERT INTO `SourceHost` VALUES (4,'megaupload.com'),(5,'depositfiles.com'),(6,'free.fr'),(7,'gigaup.fr'),(8,'hotfile.com'),(9,'miroriii.com'),(10,'rapidshare.com'),(11,'Db.to'),(12,'terafiles.net'),(13,'uploading.com');
/*!40000 ALTER TABLE `SourceHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SourceQuality`
--

DROP TABLE IF EXISTS `SourceQuality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SourceQuality` (
  `sourceQualityId` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`sourceQualityId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SourceQuality`
--

LOCK TABLES `SourceQuality` WRITE;
/*!40000 ALTER TABLE `SourceQuality` DISABLE KEYS */;
/*!40000 ALTER TABLE `SourceQuality` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SourceType`
--

DROP TABLE IF EXISTS `SourceType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SourceType` (
  `sourceTypeId` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `registrationRequired` int(10) NOT NULL default '0',
  `costFree` int(10) NOT NULL default '1',
  PRIMARY KEY  (`sourceTypeId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SourceType`
--

LOCK TABLES `SourceType` WRITE;
/*!40000 ALTER TABLE `SourceType` DISABLE KEYS */;
/*!40000 ALTER TABLE `SourceType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SourceValidator`
--

DROP TABLE IF EXISTS `SourceValidator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SourceValidator` (
  `sourceHostId` int(10) unsigned NOT NULL default '0',
  `regex` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`sourceHostId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SourceValidator`
--

LOCK TABLES `SourceValidator` WRITE;
/*!40000 ALTER TABLE `SourceValidator` DISABLE KEYS */;
/*!40000 ALTER TABLE `SourceValidator` ENABLE KEYS */;
UNLOCK TABLES;
