<?php

class Cis_Exportproduct_Block_Adminhtml_Exportproduct_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('exportproduct_form', array('legend'=>Mage::helper('exportproduct')->__('Record Info')));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('exportproduct')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('exportproduct')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('exportproduct')->__('Disabled'),
              ),
          ),
      ));
      
      $fieldset->addField('product_ids', 'text', array(
            'name'      => 'product_ids',
            'label'     => Mage::helper('exportproduct')->__('Product ID'),
            'title'     => Mage::helper('exportproduct')->__('Product ID'),
	    'required'  => true,
	    'class'     => 'required-entry',
	    'comment'	=> 'Text'
      ));
      
      $fieldset->addField('email_ids', 'text', array(
            'name'      => 'email_ids',
            'label'     => Mage::helper('exportproduct')->__('Email ID'),
            'title'     => Mage::helper('exportproduct')->__('Email ID'),
	    'required'  => true,
	    'class'     => 'required-entry validate-email'
      ));
      
      if (!Mage::app()->isSingleStoreMode()) {
	$fieldset->addField('store_ids', 'multiselect', array(
	      'name' => 'stores[]',
	      'label' => Mage::helper('exportproduct')->__('Store View'),
	      'title' => Mage::helper('exportproduct')->__('Store View'),
	      'required' => true,
	      'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
	));
      } else {
	$fieldset->addField('store_ids', 'hidden', array(
	      'name' => 'stores[]',
	      'value' => Mage::app()->getStore(true)->getId(),
	));
      }
      
      $fieldset->addField('exportproduct_url_create_date', 'hidden', array(
	    'name' => 'exportproduct_url_create_date'
      ));
      
      $fieldset->addField('exportproduct_url_update_date', 'hidden', array(
	    'name' => 'exportproduct_url_update_date'
      ));
      
      $fieldset->addField('export_product_url', 'hidden', array(
	    'name' => 'export_product_url'
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getExportproductData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getExportproductData());
          Mage::getSingleton('adminhtml/session')->getExportproductData(null);
      } elseif ( Mage::registry('exportproduct_data') ) {
          $form->setValues(Mage::registry('exportproduct_data')->getData());
      }
      return parent::_prepareForm();
  }
}