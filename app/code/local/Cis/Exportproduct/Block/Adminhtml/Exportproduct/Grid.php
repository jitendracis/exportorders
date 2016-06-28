<?php

class Cis_Exportproduct_Block_Adminhtml_Exportproduct_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('exportproductGrid');
      $this->setDefaultSort('id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('exportproduct/exportproduct')->getCollection();
      foreach ($collection as $view) {
        if ( $view->getStoreIds() && $view->getStoreIds() != 0 ) {
            $view->setStoreIds(explode(',',$view->getStoreIds()));
        } else {
            $view->setStoreIds(array('0'));
        }
      }
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }
  
  protected function _filterStoreCondition($collection, $column){
    if (!$value = $column->getFilter()->getValue()) {
        return;
    }
    $this->getCollection()->addStoreFilter($value);
  }

  protected function _prepareColumns()
  {
    
      $this->addColumn('id', array(
          'header'    => Mage::helper('exportproduct')->__('S.no'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'id',
      ));

      $this->addColumn('product_ids', array(
          'header'    => Mage::helper('exportproduct')->__('Product IDs'),
          'align'     =>'left',
          'index'     => 'product_ids',
      ));
      
      $this->addColumn('email_ids', array(
          'header'    => Mage::helper('exportproduct')->__('Email IDs'),
          'align'     =>'left',
          'index'     => 'email_ids',
      ));
      
      $this->addColumn('store_ids', array(
          'header'    => Mage::helper('exportproduct')->__('Stores'),
          'align'     =>'left',
          'index'     => 'store_ids',
      ));
      
      $this->addColumn('store_ids', array(
	  'header' => Mage::helper('exportproduct')->__('Stores'),
	  'index' => 'store_ids',
	  'type' => 'store',
	  'store_all' => true,
	  'store_view' => true,
	  'sortable' => true,
	  'filter_condition_callback' => array($this, '_filterStoreCondition'),
      ));
      
      $this->addColumn('export_product_url', array(
          'header'    => Mage::helper('exportproduct')->__('Export Product URLs'),
          'align'     =>'left',
          'index'     => 'export_product_url',
      ));
      
      $this->addColumn('status', array(
          'header'    => Mage::helper('exportproduct')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
		
      $this->addExportType('*/*/exportproductCsv', Mage::helper('exportproduct')->__('CSV'));
      $this->addExportType('*/*/exportproductXml', Mage::helper('exportproduct')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
      
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('exportproduct');
	
	

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('exportproduct')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('exportproduct')->__('Are you sure?')
        ));

        $statuses = Mage::getModel('exportproduct/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('exportproduct')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('exportproduct')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}
