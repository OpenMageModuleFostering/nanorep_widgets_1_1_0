<?php
	$installer = $this;
	$installer->startSetup();
	$installer->run("
	    CREATE TABLE `{$installer->getTable('nanorepwidgets/query')}` (
	      `query_id` int(11) NOT NULL auto_increment,
	      `order_id` int(11) NOT NULL,
	      `query` text,
	      PRIMARY KEY  (`query_id`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	");
	$installer->endSetup();    
?>