<?php

class Cis_Exportproduct_Block_Adminhtml_Exportproduct_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'exportproduct';
        $this->_controller = 'adminhtml_exportproduct';
        $this->_updateButton('save', 'label', Mage::helper('exportproduct')->__('Save record'));
        $this->_updateButton('delete', 'label', Mage::helper('exportproduct')->__('Delete record'));
    }

    public function getHeaderText()
    {
        if( Mage::registry('exportproduct_data') && Mage::registry('exportproduct_data')->getId() ) {
            return Mage::helper('exportproduct')->__("Edit record '%s'", $this->htmlEscape(Mage::registry('exportproduct_data')->getId()));
        } else {
            return Mage::helper('exportproduct')->__('Export Product URL');
        }
    }
}