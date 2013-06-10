--
-- Table structure for table `Genre`
--

DROP TABLE IF EXISTS `Genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Genre` (
  `genreId` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`genreId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Genre`
--

LOCK TABLES `Genre` WRITE;
/*!40000 ALTER TABLE `Genre` DISABLE KEYS */;
/*!40000 ALTER TABLE `Genre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VideoEntity`
--

DROP TABLE IF EXISTS `VideoEntity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VideoEntity` (
  `videoEntityId` int(10) unsigned NOT NULL auto_increment,
  `vETitleId` int(10) unsigned NOT NULL default '0',
  `vESharedInfoId` int(10) unsigned NOT NULL default '0',
  `langId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`videoEntityId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VideoEntity`
--

LOCK TABLES `VideoEntity` WRITE;
/*!40000 ALTER TABLE `VideoEntity` DISABLE KEYS */;
/*!40000 ALTER TABLE `VideoEntity` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Table structure for table `VideoEntity_Title`
--

DROP TABLE IF EXISTS `VideoEntity_Title`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VideoEntity_Title` (
  `vETitleId` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`vETitleId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VideoEntity_Title`
--

LOCK TABLES `VideoEntity_Title` WRITE;
/*!40000 ALTER TABLE `VideoEntity_Title` DISABLE KEYS */;
/*!40000 ALTER TABLE `VideoEntity_Title` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VideoEntity_SharedInfo`
--

DROP TABLE IF EXISTS `VideoEntity_SharedInfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VideoEntity_SharedInfo` (
  `vESharedInfoId` int(10) unsigned NOT NULL auto_increment,
  `date` varchar(255) NOT NULL default '',
  `countryId` int(10) NOT NULL default '0',
  `timeLengthHHMMSS` int(10) NOT NULL default '0',
  `originalTitleId` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`vESharedInfoId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VideoEntity_SharedInfo`
--

LOCK TABLES `VideoEntity_SharedInfo` WRITE;
/*!40000 ALTER TABLE `VideoEntity_SharedInfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `VideoEntity_SharedInfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VideoEntity_SharedInfo_r_Image`
--

DROP TABLE IF EXISTS `VideoEntity_SharedInfo_r_Image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VideoEntity_SharedInfo_r_Image` (
  `vESharedInfoId` int(10) unsigned NOT NULL default 0,
  `imageId` int(10) unsigned NOT NULL default 0,
  `isRecycled` int(10) unsigned NOT NULL default 0,
  PRIMARY KEY  (`vESharedInfoId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VideoEntity_SharedInfo_r_Image`
--

LOCK TABLES `VideoEntity_SharedInfo_r_Image` WRITE;
/*!40000 ALTER TABLE `VideoEntity_SharedInfo_r_Image` DISABLE KEYS */;
/*!40000 ALTER TABLE `VideoEntity_SharedInfo_r_Image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VideoEntity_SharedInfo_r_Genre`
--

DROP TABLE IF EXISTS `VideoEntity_SharedInfo_r_Genre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VideoEntity_SharedInfo_r_Genre` (
  `vESharedInfoId` int(10) unsigned NOT NULL default 0,
  `genreId` int(10) unsigned NOT NULL default 0,
  PRIMARY KEY  (`vESharedInfoId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VideoEntity_SharedInfo_r_Genre`
--

LOCK TABLES `VideoEntity_SharedInfo_r_Genre` WRITE;
/*!40000 ALTER TABLE `VideoEntity_SharedInfo_r_Genre` DISABLE KEYS */;
/*!40000 ALTER TABLE `VideoEntity_SharedInfo_r_Genre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VideoEntity_SharedInfo_Participant`
--

DROP TABLE IF EXISTS `VideoEntity_SharedInfo_Participant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VideoEntity_SharedInfo_Participant` (
  `participantId` int(10) unsigned NOT NULL auto_increment,
  `vESharedInfoId` int(10) unsigned NOT NULL default 0,
  `mIEId` int(10) unsigned NOT NULL default 0,
  `mIERoleId` int(10) unsigned NOT NULL default 0,
  PRIMARY KEY  (`participantId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VideoEntity_SharedInfo_Participant`
--

LOCK TABLES `VideoEntity_SharedInfo_Participant` WRITE;
/*!40000 ALTER TABLE `VideoEntity_SharedInfo_Participant` DISABLE KEYS */;
/*!40000 ALTER TABLE `VideoEntity_SharedInfo_Participant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VideoEntity_Sources`
--

DROP TABLE IF EXISTS `VideoEntity_Sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VideoEntity_Sources` (
  `sourceId` int(10) unsigned NOT NULL auto_increment,
  `videoEntityId` int(10) unsigned NOT NULL default '0',
  `sourceHostId` int(10) unsigned NOT NULL default '0',
  `sourceTypeId` int(10) unsigned NOT NULL default '0',
  `sourceQualityId` int(10) unsigned NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`sourceId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VideoEntity_Sources`
--

LOCK TABLES `VideoEntity_Sources` WRITE;
/*!40000 ALTER TABLE `VideoEntity_Sources` DISABLE KEYS */;
/*!40000 ALTER TABLE `VideoEntity_Sources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VideoEntity_Synopsis`
--

DROP TABLE IF EXISTS `VideoEntity_Synopsis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VideoEntity_Synopsis` (
  `videoEntityId` int(10) unsigned NOT NULL auto_increment,
  `synopsis` text NOT NULL,
  PRIMARY KEY  (`videoEntityId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VideoEntity_Synopsis`
--

LOCK TABLES `VideoEntity_Synopsis` WRITE;
/*!40000 ALTER TABLE `VideoEntity_Synopsis` DISABLE KEYS */;
/*!40000 ALTER TABLE `VideoEntity_Synopsis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VideoEntity_r_Countries`
--

DROP TABLE IF EXISTS `VideoEntity_r_Countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VideoEntity_r_Countries` (
  `videoEntityId` int(10) unsigned NOT NULL default '0',
  `countryId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`videoEntityId`,`countryId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VideoEntity_r_Countries`
--

LOCK TABLES `VideoEntity_r_Countries` WRITE;
/*!40000 ALTER TABLE `VideoEntity_r_Countries` DISABLE KEYS */;
/*!40000 ALTER TABLE `VideoEntity_r_Countries` ENABLE KEYS */;
UNLOCK TABLES;
