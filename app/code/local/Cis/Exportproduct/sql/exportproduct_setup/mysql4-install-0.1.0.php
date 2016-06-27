<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('exportproductdata')};
CREATE TABLE {$this->getTable('exportproductdata')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `product_ids` varchar(255) NOT NULL default '',
  `email_ids` varchar(255) NOT NULL default '',
  `store_ids` varchar(255) NOT NULL default '',
  `export_product_url` varchar(255) NOT NULL default '',
  `exportproduct_extra1` varchar(255) NOT NULL default '',
  `exportproduct_extra2` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '1',
  `exportproduct_url_create_date` datetime default CURRENT_TIMESTAMP,
  `exportproduct_url_update_date` datetime Null,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 