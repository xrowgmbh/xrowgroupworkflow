xrowgroupworkflow
=================

The basic feature of this extension is to set the object state for a group of objects.

Setup
* Active the extenstion
* Add in Setup->States a state group (for example "group workflow")
* Add states "online" and "offline" in "group workflow"
* For permissions edit anonymous in "Roles and policies". Set here for module content/read a limitation in dropdown "StateGroup_groupworkflow:" to "online"

MySQL
DROP TABLE IF EXISTS xrowgroupworkflow;
CREATE TABLE xrowgroupworkflow (
  id int(11) NOT NULL auto_increment,
  status int(11) default NULL,
  `date` int(11) NOT NULL default '0',
  data longtext NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;