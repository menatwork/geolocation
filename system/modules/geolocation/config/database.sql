-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_geodata`
-- 

CREATE TABLE `tl_geodata` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip_start` varchar(15) NOT NULL default '',
  `ip_end` varchar(15) NOT NULL default '',
  `ipnum_start` int(10) unsigned NOT NULL default '0',
  `ipnum_end` int(10) unsigned NOT NULL default '0',
  `country_short` varchar(2) NOT NULL default '',
  `country` varchar(100) NOT NULL default '',   
  PRIMARY KEY  (`id`),
  KEY `ipnum_start` (`ipnum_start`),
  KEY `ipnum_end` (`ipnum_end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_geodatachache`
-- 

CREATE TABLE `tl_geodatacache` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `lat` varchar(8) NOT NULL default '0',
  `lon` varchar(8) NOT NULL default '0',
  `create_on` int(10) unsigned NOT NULL default '0',
  `country_short` varchar(2) NOT NULL default '',
  `country` varchar(100) NOT NULL default '',   
  PRIMARY KEY  (`id`),
  KEY `lat` (`lat`),
  KEY `lon` (`lon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_modules`
-- 

CREATE TABLE `tl_module` (
  `gpTemplate` varchar(255) NOT NULL default '', 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

