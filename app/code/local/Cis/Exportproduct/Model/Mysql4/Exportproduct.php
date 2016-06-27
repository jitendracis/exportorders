<?php

class Cis_Exportproduct_Model_Mysql4_Exportproduct extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('exportproduct/exportproduct', 'id');
    }
}