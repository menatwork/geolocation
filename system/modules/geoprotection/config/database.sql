-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_news`
-- 

CREATE TABLE `tl_content` (
  `gp_protected` char(1) NOT NULL default '',
  `gp_mode` varchar(7) NOT NULL default '',
  `gp_countries` blob NULL,
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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
