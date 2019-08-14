<?php
	$installer = $this;
	$installer->startSetup();
	$installer->run("
	    ALTER TABLE `{$installer->getTable('nanorepwidgets/query')}` ADD date DATETIME;
	");
	$installer->endSetup();    
?>