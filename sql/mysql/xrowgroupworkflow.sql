DROP TABLE IF EXISTS xrowgroupworkflow;
CREATE TABLE xrowgroupworkflow (
  id int(11) NOT NULL auto_increment,
  status int(11) default NULL,
  `date` int(11) NOT NULL default '0',
  data longtext NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;