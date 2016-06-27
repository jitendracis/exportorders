<?php

class Cis_Exportproduct_Block_Adminhtml_Exportproduct_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('exportproduct_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('exportproduct')->__('Record Info'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('exportproduct')->__('Record Info'),
          'title'     => Mage::helper('exportproduct')->__('Record Info'),
          'content'   => $this->getLayout()->createBlock('exportproduct/adminhtml_exportproduct_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}