--
-- Table structure for table `Country`
--

DROP TABLE IF EXISTS `Country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Country` (
  `countryId` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`countryId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Country`
--

LOCK TABLES `Country` WRITE;
/*!40000 ALTER TABLE `Country` DISABLE KEYS */;
INSERT INTO `Country` VALUES (4,'France','france'),(5,'Australia','australia'),(6,'Usa','usa'),(7,'Canada','canada'),(8,'Austria','austria'),(9,'Germany','germany'),(10,'Netherlands','netherlands'),(11,'Switzerland','switzerland'),(12,'Uk','uk'),(13,'Argentina','argentina'),(14,'Belgium','belgium'),(15,'Spain','spain'),(16,'Italy','italy'),(17,'Portugal','portugal');
/*!40000 ALTER TABLE `Country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DirtyCountry`
-- When a country string can not be normalized it should
-- be added to this table
-- 

DROP TABLE IF EXISTS `DirtyCountry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DirtyCountry` (
  `dirtyCountryId` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`dirtyCountryId`)
) ENGINE=MyISAM AUTO_INCREMENT=200 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DirtyCountry`
--

LOCK TABLES `DirtyCountry` WRITE;
/*!40000 ALTER TABLE `DirtyCountry` DISABLE KEYS */;
/*!40000 ALTER TABLE `DirtyCountry` ENABLE KEYS */;
UNLOCK TABLES;



--
-- Table structure for table `Country_Matcher`
--

DROP TABLE IF EXISTS `Country_Matcher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Country_Matcher` (
  `priority` int(10) unsigned NOT NULL auto_increment,
  `countryId` int(10) unsigned NOT NULL default '0',
  `regex` text NOT NULL,
  PRIMARY KEY  (`priority`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Country_Matcher`
--

LOCK TABLES `Country_Matcher` WRITE;
/*!40000 ALTER TABLE `Country_Matcher` DISABLE KEYS */;
INSERT INTO `Country_Matcher` VALUES (18,4,'/Fran\\p{L}\\p{M}*(?:ia|[ea]|kreich)/iu'),(19,5,'/Austr\\p{L}\\p{M}*li(?:a|en)/iu'),(20,6,'/u.?s.?a.?|e.?e.?u.?u.?|Vereinigte[- _.]Staa?ten|Estados[- _.]Un\\p{L}\\p{M}*dos.*?(?:Am\\p{L}\\p{M}*rica)?|United[- _.]states.*?(?:America)?|\\p{L}\\p{M}*tats[-_. ]unis[-_. ]d?.?Am\\p{L}\\p{M}*rique|Stati[ -_.]Uniti(?:[ -_.]d.america)?/iu'),(21,7,'/[CK]anad\\p{L}\\p{M}*/iu'),(22,8,'/\\p{L}\\p{M}*e?sterreich|Au(?:stria|triche)/iu'),(23,9,'/German(?:y|ia)|Aleman[ih]a|Allemagne|Deutschland/iu'),(24,10,'/Pays-Bas|Pa\\p{L}\\p{M}*ses-Ba(?:j|ix)os|Paesi-Bassi|Niederlande|Netherlands/iu'),(25,11,'/Switzerland|Suisse|Suiza|Svizzera|Schweiz|Su\\p{L}\\p{M}*\\p{L}\\p{M}*a/iu'),(26,12,'/[UV].?K.?|United Kingdom|Britain|Royaume-Uni|Re[ig]no Uni[dt]o|Vereinigtes K(?:\\p{L}\\p{M}*|oe)nigreich|Gr\\p{L}\\p{M}*n(?:de)?[ -]Breta(?:gne|\\p{L}\\p{M}*a)|Great Britain/iu'),(27,13,'/Argentin(?:[ae]|ien)/iu'),(28,14,'/B\\p{L}\\p{M}*lgi(?:um|que|ca|en)/iu'),(29,15,'/Espa\\p{L}\\p{M}*[ae]|Spa(?:nien|gna)|Spain/iu'),(30,16,'/(?:It\\p{L}\\p{M}*l[yi](?:a|(?:en))?)/iu'),(31,17,'/Portugal|Portogallo/iu');

/*!40000 ALTER TABLE `Country_Matcher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Country_r_LangISOs`
--

DROP TABLE IF EXISTS `Country_r_LangISOs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Country_r_LangISOs` (
  `countryId` int(10) unsigned NOT NULL default '0',
  `langISOId` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`countryId`,`langISOId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Country_r_LangISOs`
--

LOCK TABLES `Country_r_LangISOs` WRITE;
/*!40000 ALTER TABLE `Country_r_LangISOs` DISABLE KEYS */;
INSERT INTO `Country_r_LangISOs` VALUES (4,38),(5,29),(6,29),(7,29),(7,38),(8,26),(9,26),(10,89),(11,26),(11,38),(11,57),(12,29),(13,31),(14,38),(15,21),(15,31),(15,33),(15,42),(16,57),(17,96);
/*!40000 ALTER TABLE `Country_r_LangISOs` ENABLE KEYS */;
UNLOCK TABLES;
