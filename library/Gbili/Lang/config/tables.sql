--
-- Table structure for table `LangDirty`
--

DROP TABLE IF EXISTS `LangDirty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LangDirty` (
  `langDirtyId` int(10) unsigned NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`langDirtyId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LangDirty`
--

LOCK TABLES `LangDirty` WRITE;
/*!40000 ALTER TABLE `LangDirty` DISABLE KEYS */;
INSERT INTO `LangDirty` VALUES (147,'franzosish'),(148,'romanch'),(149,'romanch'),(150,'romanch'),(151,'romanch'),(152,'romanch'),(153,'romanch'),(154,'romanch'),(155,'romanch'),(156,'romanch'),(157,'romanch'),(158,'romanch'),(159,'romanch'),(160,'romanch'),(161,'romanch'),(162,'or'),(163,'or'),(164,'or'),(165,'or'),(166,'or'),(167,'assameses');
/*!40000 ALTER TABLE `LangDirty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LangISO`
--

DROP TABLE IF EXISTS `LangISO`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LangISO` (
  `langISOId` int(10) unsigned NOT NULL auto_increment,
  `value` varchar(255) NOT NULL default '',
  `slug` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`langISOId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LangISO`
--

LOCK TABLES `LangISO` WRITE;
/*!40000 ALTER TABLE `LangISO` DISABLE KEYS */;
INSERT INTO `LangISO` VALUES (4,'or','oriya'),(5,'as','assamese'),(6,'aa','afar'),(7,'ab','abkhazian'),(8,'af','afrikaans'),(9,'am','amharic'),(10,'ar','arabic'),(11,'ay','aymara'),(12,'az','azerbaijani'),(13,'ba','bashkir'),(14,'be','byelorussian'),(15,'bg','bulgarian'),(16,'bh','bihari'),(17,'bi','bislama'),(18,'bn','bengali'),(19,'bo','tibetan'),(20,'br','breton'),(21,'ca','catalan'),(22,'co','corsican'),(23,'cs','czech'),(24,'cy','welsh'),(25,'da','danish'),(26,'de','german'),(27,'dz','bhutani'),(28,'el','greek'),(29,'en','english'),(30,'eo','esperanto'),(31,'es','spanish'),(32,'et','estonian'),(33,'eu','basque'),(34,'fa','persian'),(35,'fi','finnish'),(36,'fj','fiji'),(37,'fo','faroese'),(38,'fr','french'),(39,'fy','frisian'),(40,'ga','irish'),(41,'gd','scots'),(42,'gl','galician'),(43,'gn','guarani'),(44,'gu','gujarati'),(45,'gv','manx'),(46,'ha','hausa'),(47,'he','hebrew'),(48,'hi','hindi'),(49,'hr','croatian'),(50,'hu','hungarian'),(51,'hy','armenian'),(52,'ia','interlingua'),(53,'id','indonesian'),(54,'ie','interlingue'),(55,'ik','inupiak'),(56,'is','icelandic'),(57,'it','italian'),(58,'iu','inuktitut'),(59,'ja','japanese'),(60,'jw','javanese'),(61,'ka','georgian'),(62,'kk','kazakh'),(63,'kl','greenlandic'),(64,'km','cambodian'),(65,'kn','kannada'),(66,'ko','korean'),(67,'ks','kashmiri'),(68,'ku','kurdish'),(69,'kw','cornish'),(70,'ky','kirghiz'),(71,'la','latin'),(72,'lb','luxemburgish'),(73,'ln','lingala'),(74,'lo','laothian'),(75,'lt','lithuanian'),(76,'lv','latvian'),(77,'mg','malagasy'),(78,'mi','maori'),(79,'mk','macedonian'),(80,'ml','malayalam'),(81,'mn','mongolian'),(82,'mo','moldavian'),(83,'mr','marathi'),(84,'ms','malay'),(85,'mt','maltese'),(86,'my','burmese'),(87,'na','nauru'),(88,'ne','nepali'),(89,'nl','dutch'),(90,'no','norwegian'),(91,'oc','occitan'),(92,'om','oromo'),(93,'pa','punjabi'),(94,'pl','polish'),(95,'ps','pashto'),(96,'pt','portuguese'),(97,'qu','quechua'),(98,'rm','rhaeto'),(99,'rn','kirundi'),(100,'ro','romanian'),(101,'ru','russian'),(102,'rw','kinyarwanda'),(103,'sa','sanskrit'),(104,'sd','sindhi'),(105,'se','northern'),(106,'sg','sangho'),(107,'sh','serbo'),(108,'si','singhalese'),(109,'sk','slovak'),(110,'sl','slovenian'),(111,'sm','samoan'),(112,'sn','shona'),(113,'so','somali'),(114,'sq','albanian'),(115,'sr','serbian'),(116,'ss','siswati'),(117,'st','sesotho'),(118,'su','sundanese'),(119,'sv','swedish'),(120,'sw','swahili'),(121,'ta','tamil'),(122,'te','telugu'),(123,'tg','tajik'),(124,'th','thai'),(125,'ti','tigrinya'),(126,'tk','turkmen'),(127,'tl','tagalog'),(128,'tn','setswana'),(129,'to','tonga'),(130,'tr','turkish'),(131,'ts','tsonga'),(132,'tt','tatar'),(133,'tw','twi'),(134,'ug','uigur'),(135,'uk','ukrainian'),(136,'ur','urdu'),(137,'uz','uzbek'),(138,'vi','vietnamese'),(139,'vo','volapuk'),(140,'wo','wolof'),(141,'xh','xhosa'),(142,'yi','yiddish'),(143,'yo','yoruba'),(144,'za','zhuang'),(145,'zh','chinese'),(146,'zu','zulu');
/*!40000 ALTER TABLE `LangISO` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LangISO_Matcher`
--

DROP TABLE IF EXISTS `LangISO_Matcher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LangISO_Matcher` (
  `priority` int(10) unsigned NOT NULL auto_increment,
  `langISOId` int(10) unsigned NOT NULL default '0',
  `regex` text NOT NULL,
  PRIMARY KEY  (`priority`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LangISO_Matcher`
--

LOCK TABLES `LangISO_Matcher` WRITE;
/*!40000 ALTER TABLE `LangISO_Matcher` DISABLE KEYS */;
INSERT INTO `LangISO_Matcher` VALUES (318,4,'/oriya/ui'),(319,5,'/assamese/ui'),(320,6,'/afar/ui'),(321,7,'/abkhazian/ui'),(322,8,'/afrikaans/ui'),(323,9,'/amharic/ui'),(324,10,'/arabic/ui'),(325,11,'/aymara/ui'),(326,12,'/azerbaijani/ui'),(327,13,'/bashkir/ui'),(328,14,'/byelorussian/ui'),(329,15,'/bulgarian/ui'),(330,16,'/bihari/ui'),(331,17,'/bislama/ui'),(332,18,'/bengali/ui'),(333,19,'/tibetan/ui'),(334,20,'/breton/ui'),(335,21,'/catalan/ui'),(336,22,'/corsican/ui'),(337,23,'/czech/ui'),(338,24,'/welsh/ui'),(339,25,'/danish/ui'),(340,26,'/german/ui'),(341,27,'/bhutani/ui'),(342,28,'/greek/ui'),(343,29,'/english/ui'),(344,30,'/esperanto/ui'),(345,31,'/spanish/ui'),(346,32,'/estonian/ui'),(347,33,'/basque/ui'),(348,34,'/persian/ui'),(349,35,'/finnish/ui'),(350,36,'/fiji/ui'),(351,37,'/faroese/ui'),(352,38,'/french/ui'),(353,39,'/frisian/ui'),(354,40,'/uirish/ui'),(355,41,'/scots/ui'),(356,42,'/galician/ui'),(357,43,'/guarani/ui'),(358,44,'/gujarati/ui'),(359,45,'/manx/ui'),(360,46,'/hausa/ui'),(361,47,'/hebrew/ui'),(362,48,'/hindi/ui'),(363,49,'/croatian/ui'),(364,50,'/hungarian/ui'),(365,51,'/armenian/ui'),(366,52,'/uinterlingua/ui'),(367,53,'/uindonesian/ui'),(368,54,'/uinterlingue/ui'),(369,55,'/uinupiak/ui'),(370,56,'/uicelandic/ui'),(371,57,'/uitalian/ui'),(372,58,'/uinuktitut/ui'),(373,59,'/japanese/ui'),(374,60,'/javanese/ui'),(375,61,'/georgian/ui'),(376,62,'/kazakh/ui'),(377,63,'/greenlandic/ui'),(378,64,'/cambodian/ui'),(379,65,'/kannada/ui'),(380,66,'/korean/ui'),(381,67,'/kashmiri/ui'),(382,68,'/kurdish/ui'),(383,69,'/cornish/ui'),(384,70,'/kirghiz/ui'),(385,71,'/latin/ui'),(386,72,'/luxemburgish/ui'),(387,73,'/lingala/ui'),(388,74,'/laothian/ui'),(389,75,'/lithuanian/ui'),(390,76,'/latvian/ui'),(391,77,'/malagasy/ui'),(392,78,'/maori/ui'),(393,79,'/macedonian/ui'),(394,80,'/malayalam/ui'),(395,81,'/mongolian/ui'),(396,82,'/moldavian/ui'),(397,83,'/marathi/ui'),(398,84,'/malay/ui'),(399,85,'/maltese/ui'),(400,86,'/burmese/ui'),(401,87,'/nauru/ui'),(402,88,'/nepali/ui'),(403,89,'/dutch/ui'),(404,90,'/norwegian/ui'),(405,91,'/occitan/ui'),(406,92,'/oromo/ui'),(407,93,'/punjabi/ui'),(408,94,'/polish/ui'),(409,95,'/pashto/ui'),(410,96,'/portuguese/ui'),(411,97,'/quechua/ui'),(412,98,'/rhaeto/ui'),(413,99,'/kirundi/ui'),(414,100,'/romanian/ui'),(415,101,'/russian/ui'),(416,102,'/kinyarwanda/ui'),(417,103,'/sanskrit/ui'),(418,104,'/sindhi/ui'),(419,105,'/northern/ui'),(420,106,'/sangho/ui'),(421,107,'/serbo/ui'),(422,108,'/singhalese/ui'),(423,109,'/slovak/ui'),(424,110,'/slovenian/ui'),(425,111,'/samoan/ui'),(426,112,'/shona/ui'),(427,113,'/somali/ui'),(428,114,'/albanian/ui'),(429,115,'/serbian/ui'),(430,116,'/siswati/ui'),(431,117,'/sesotho/ui'),(432,118,'/sundanese/ui'),(433,119,'/swedish/ui'),(434,120,'/swahili/ui'),(435,121,'/tamil/ui'),(436,122,'/telugu/ui'),(437,123,'/tajik/ui'),(438,124,'/thai/ui'),(439,125,'/tigrinya/ui'),(440,126,'/turkmen/ui'),(441,127,'/tagalog/ui'),(442,128,'/setswana/ui'),(443,129,'/tonga/ui'),(444,130,'/turkish/ui'),(445,131,'/tsonga/ui'),(446,132,'/tatar/ui'),(447,133,'/twi/ui'),(448,134,'/uigur/ui'),(449,135,'/ukrainian/ui'),(450,136,'/urdu/ui'),(451,137,'/uzbek/ui'),(452,138,'/vietnamese/ui'),(453,139,'/volapuk/ui'),(454,140,'/wolof/ui'),(455,141,'/xhosa/ui'),(456,142,'/yiddish/ui'),(457,143,'/yoruba/ui'),(458,144,'/zhuang/ui'),(459,145,'/chinese/ui'),(460,146,'/zulu/ui');
/*!40000 ALTER TABLE `LangISO_Matcher` ENABLE KEYS */;
UNLOCK TABLES;
