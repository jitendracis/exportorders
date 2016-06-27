<?php

class Cis_Exportproduct_Helper_Adminhtml_Url extends Mage_Core_Helper_Abstract
{
    public function getEncryptUrl($id){
	$encrypted_data = Mage::helper('core')->encrypt(base64_encode($id));
	try{
	    return Mage::getBaseUrl()."exportproduct/index/getOrders?key=".$encrypted_data;
	}catch(Exception $e) {
	    return;
	}
    }
}