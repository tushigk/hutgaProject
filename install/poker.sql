CREATE TABLE IF NOT EXISTS `livechat` (
  `gameID` int(15) NOT NULL DEFAULT '0',
  `updatescreen` int(30) DEFAULT '0',
  `c1` text DEFAULT '',
  `c2` text DEFAULT '',
  `c3` text DEFAULT '',
  `c4` text DEFAULT '',
  `c5` text DEFAULT '',
  PRIMARY KEY  (`gameID`)  
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `userchat` (
  `gameID` int(11) NOT NULL DEFAULT '0',
  `updatescreen` int(30) DEFAULT '0',
  `c1` text DEFAULT '',
  `c2` text DEFAULT '',
  `c3` text DEFAULT '',
  `c4` text DEFAULT '',
  `c5` text DEFAULT '',
  PRIMARY KEY  (`gameID`)    
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `players` (
  `ID` int(11) NOT NULL auto_increment,
  `username` varchar(12) DEFAULT '',
  `email` varchar(70) DEFAULT '',
  `password` varchar(40) DEFAULT '',
  `avatar` varchar(80) DEFAULT '',
  `datecreated` int(35) DEFAULT '0',
  `lastlogin` int(35) DEFAULT '0',
  `ipaddress` varchar(20) DEFAULT '',
  `sessname` varchar(32) DEFAULT '',
  `banned` tinyint(1) DEFAULT '0',
  `approve` tinyint(1) DEFAULT '0',
  `lastmove` int(35) DEFAULT '0',
  `waitimer` int(35) DEFAULT '0',
  `code` varchar(16) DEFAULT '',
  `GUID` varchar(32) DEFAULT '',
  `vID` int(15) DEFAULT '0',
  `gID` int(15) DEFAULT '0',
  `timetag` int(30) DEFAULT '0',
  PRIMARY KEY  (`ID`)  
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `poker` (
  `gameID` int(15) NOT NULL auto_increment,
  `tablename` varchar(64) DEFAULT '',
  `tabletype` varchar(1) DEFAULT '',
  `tablelow` int(7) DEFAULT '0',
  `tablelimit` varchar(15) DEFAULT '',
  `sbamount` int(7) DEFAULT '0',
  `bbamount` int(7) DEFAULT '0',
  `tablestyle` varchar(20) DEFAULT '',
  `gamestyle` VARCHAR(1) DEFAULT 't',
  `move` varchar(2) DEFAULT '',
  `dealer` varchar(2) DEFAULT '',
  `hand` varchar(5) DEFAULT '',
  `pot` int(10) DEFAULT '0',
  `bet` int(10) DEFAULT '0',
  `lastbet` varchar(15) DEFAULT '',
  `lastmove` int(35) DEFAULT '0',
  `card1` varchar(40) DEFAULT '',
  `card2` varchar(40) DEFAULT '',
  `card3` varchar(40) DEFAULT '',
  `card4` varchar(40) DEFAULT '',
  `card5` varchar(40) DEFAULT '',
  `p1name` varchar(12) DEFAULT '',
  `p1pot` varchar(10) DEFAULT '',
  `p1bet` varchar(10) DEFAULT '',
  `p1card1` varchar(40) DEFAULT '',
  `p1card2` varchar(40) DEFAULT '',
  `p2name` varchar(12) DEFAULT '',
  `p2pot` varchar(10) DEFAULT '',
  `p2bet` varchar(10) DEFAULT '',
  `p2card1` varchar(40) DEFAULT '',
  `p2card2` varchar(40) DEFAULT '',
  `p3name` varchar(12) DEFAULT '',
  `p3pot` varchar(10) DEFAULT '',
  `p3bet` varchar(10) DEFAULT '',
  `p3card1` varchar(40) DEFAULT '',
  `p3card2` varchar(40) DEFAULT '',
  `p4name` varchar(12) DEFAULT '',
  `p4pot` varchar(10) DEFAULT '',
  `p4bet` varchar(10) DEFAULT '',
  `p4card1` varchar(40) DEFAULT '',
  `p4card2` varchar(40) DEFAULT '',
  `p5name` varchar(12) DEFAULT '',
  `p5pot` varchar(10) DEFAULT '',
  `p5bet` varchar(10) DEFAULT '',
  `p5card1` varchar(40) DEFAULT '',
  `p5card2` varchar(40) DEFAULT '',
  `p6name` varchar(12) DEFAULT '',
  `p6pot` varchar(10) DEFAULT '',
  `p6bet` varchar(10) DEFAULT '',
  `p6card1` varchar(40) DEFAULT '',
  `p6card2` varchar(40) DEFAULT '',
  `p7name` varchar(12) DEFAULT '',
  `p7pot` varchar(10) DEFAULT '',
  `p7bet` varchar(10) DEFAULT '',
  `p7card1` varchar(40) DEFAULT '',
  `p7card2` varchar(40) DEFAULT '',
  `p8name` varchar(12) DEFAULT '',
  `p8pot` varchar(10) DEFAULT '',
  `p8bet` varchar(10) DEFAULT '',
  `p8card1` varchar(40) DEFAULT '',
  `p8card2` varchar(40) DEFAULT '',
  `p9name` varchar(12) DEFAULT '',
  `p9pot` varchar(10) DEFAULT '',
  `p9bet` varchar(10) DEFAULT '',
  `p9card1` varchar(40) DEFAULT '',
  `p9card2` varchar(40) DEFAULT '',
  `p10name` varchar(12) DEFAULT '',
  `p10pot` varchar(10) DEFAULT '',
  `p10bet` varchar(10) DEFAULT '',
  `p10card1` varchar(40) DEFAULT '',
  `p10card2` varchar(40) DEFAULT '',
  `msg` varchar(150) DEFAULT '',
  PRIMARY KEY  (`gameID`)  
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `settings` (
  `setting` varchar(12) DEFAULT '',
  `Xkey` varchar(12) DEFAULT '',
  `Xvalue` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `stats` (
  `ID` int(11) NOT NULL auto_increment,
  `player` varchar(12) DEFAULT '',
  `rank` varchar(12) DEFAULT '',
  `winpot` int(20) DEFAULT '0',
  `gamesplayed` int(11) DEFAULT '0',
  `tournamentsplayed` int(11) DEFAULT '0',
  `tournamentswon` int(11) DEFAULT '0',
  `handsplayed` int(11) DEFAULT '0',
  `handswon` int(11) DEFAULT '0',
  `bet` int(11) DEFAULT '0',
  `checked` int(11) DEFAULT '0',
  `called` varchar(11) DEFAULT '0',
  `allin` varchar(11) DEFAULT '0',
  `fold_pf` varchar(11) DEFAULT '0',
  `fold_f` varchar(11) DEFAULT '0',
  `fold_t` varchar(11) DEFAULT '0',
  `fold_r` int(11) DEFAULT '0',
  PRIMARY KEY  (`ID`)  
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `styles` (
  `style_id` int(11) NOT NULL auto_increment,
  `style_name` varchar(20) DEFAULT '',
  `style_lic` varchar(60) DEFAULT '',
  PRIMARY KEY  (`style_id`)  
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

INSERT INTO `styles` (`style_id`, `style_name`, `style_lic`) VALUES
(1, 'table_blue', 'yTKWShHyXw'),
(2, 'table_red', 'tmnJOtYtrt'),
(3, 'table_orange', 'cfuVmfI658');

INSERT INTO `settings` VALUES ('title', 'TITLE', 'My Poker Site');
INSERT INTO `settings` VALUES ('appmod', 'APPMOD', '0');
INSERT INTO `settings` VALUES ('memmod', 'MEMMOD', '0');
INSERT INTO `settings` VALUES ('movetimer', 'MOVETIMER', '20');
INSERT INTO `settings` VALUES ('showtimer', 'SHOWDOWN', '5');
INSERT INTO `settings` VALUES ('kicktimer', 'KICKTIMER', '7');
INSERT INTO `settings` VALUES ('emailmod', 'EMAILMOD', '0');
INSERT INTO `settings` VALUES ('deletetimer', 'DELETE', 'never');
INSERT INTO `settings` VALUES ('waitimer', 'WAITIMER', '10');
INSERT INTO `settings` VALUES ('session', 'SESSNAME', '');
INSERT INTO `settings` VALUES ('renew', 'RENEW', '1');
INSERT INTO `settings` VALUES ('disconnect', 'DISCONNECT', '60');
INSERT INTO `settings` VALUES ('stakesize', 'STAKESIZE', 'med');
INSERT INTO `settings` VALUES ('ipcheck', 'IPCHECK', '0');
INSERT INTO `settings` VALUES ('scriptversio', 'SCRIPTVERSIO', '2.9.5');
INSERT INTO `settings` VALUES ('lastupdatech', 'LASTUPDATECH', '0');
INSERT INTO `settings` VALUES ('updatealert', 'UPDATEALERT', '0');
INSERT INTO `settings` VALUES ('addonupdatea', 'ADDONUPDATEA', '0');
INSERT INTO `settings` VALUES ('licensekey', 'LICENSEKEY', '');
INSERT INTO `settings` VALUES ('activation', 'ACTIVATION', '');
INSERT INTO `settings` VALUES ('theme', 'THEME', 'bs4');
INSERT INTO `settings` VALUES ('themeupdatea', 'THEMEUPDATEA', '0');
