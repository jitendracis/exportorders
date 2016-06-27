<?php
class Cis_Exportproduct_Block_Adminhtml_Exportproduct extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_exportproduct';
    $this->_blockGroup = 'exportproduct';
    $this->_headerText = Mage::helper('exportproduct')->__("Export Product URL's Setting");
    $this->_addButtonLabel = Mage::helper('exportproduct')->__("Export Product URL's Setting");
    parent::__construct();
  }
}