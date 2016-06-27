<?php

class Cis_Exportproduct_Model_Exportproduct extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('exportproduct/exportproduct');
    }
}