CREATE TABLE `logcounterx_cfg` (
  `recid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cfgname` varchar(32) NOT NULL DEFAULT '',
  `cfgvalue` varchar(191) NOT NULL DEFAULT '',
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB;

CREATE TABLE `logcounterx_count` (
  `ymd` date NOT NULL,
  `cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `robot` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ymd`)
) ENGINE=InnoDB;

CREATE TABLE `logcounterx_hours` (
  `hour` char(2) NOT NULL,
  `cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `robot` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`hour`)
) ENGINE=InnoDB;

CREATE TABLE `logcounterx_ip` (
  `accip` varchar(191) NOT NULL,
  `acctime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`accip`)
) ENGINE=InnoDB;

CREATE TABLE `logcounterx_log` (
  `recid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `igflag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_agent` varchar(150) NOT NULL DEFAULT '',
  `remote_host` varchar(150) NOT NULL DEFAULT '',
  `rh_short` varchar(150) NOT NULL DEFAULT '',
  `path_info` varchar(80) NOT NULL DEFAULT '',
  `referer` varchar(191) NOT NULL DEFAULT '',
  `ref_short` varchar(150) NOT NULL DEFAULT '',
  `ref_query` varchar(150) NOT NULL DEFAULT '',
  `acccnt` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `uname` varchar(25) DEFAULT NULL,
  `agent` varchar(20) DEFAULT NULL,
  `os` varchar(20) DEFAULT NULL,
  `qword` varchar(191) DEFAULT NULL,
  `accday` date NOT NULL,
  `acctime` time NOT NULL,
  `accwday` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`recid`)
) ENGINE=InnoDB;
