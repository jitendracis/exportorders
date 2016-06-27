<?php
class Cis_Exportproduct_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
	if (!Mage::getStoreConfigFlag('exportproduct/exportproduct_group/enable')) {
		$url = Mage::getBaseUrl();
		Mage::app()->getFrontController()->getResponse()
						 ->setRedirect($url)
						 ->sendResponse();
		exit;
	}
	$this->loadLayout();     
	$this->renderLayout();
    }
    
    public function getOrdersAction(){
	
	if (!Mage::getStoreConfigFlag('exportproduct/exportproduct_group/enable')) {
		$url = Mage::getBaseUrl();
		Mage::app()->getFrontController()->getResponse()
						 ->setRedirect($url)
						 ->sendResponse();
		exit;
	}
	
	if ($_GET['key'] && $_GET['key']!='') {
	    $exportproduct_url_id = base64_decode(Mage::helper('core')->decrypt($_GET['key']));
	    $exportproduct_status = Mage::helper('exportproduct')->checkExportproductStatus($exportproduct_url_id);
	    if($exportproduct_status == 1){
		$order_customer_collection = Mage::helper('exportproduct')->getExportProducts($exportproduct_url_id);
		if(count($order_customer_collection) > 0){
		    $excel = Mage::helper('exportproduct')->generateExcel($order_customer_collection);
		    if($excel){
			$filePath = Mage::getBaseDir().'/exportorder/'.$excel;
			try{
			    $send = Mage::helper('exportproduct')->sendMail($filePath, $exportproduct_url_id);
			    Mage::getSingleton('core/session')->addSuccess(Mage::helper('exportproduct')->__("Excel sheet sent successfully"));
			    $this->_redirect('*/*/index');
			    return;
			}catch(Exception $e){
			    Mage::getSingleton('core/session')->addError(Mage::helper('exportproduct')->__($e->getMessage()));
			    $this->_redirect('*/*/error');
			    return;    
			}
		    }else{
			Mage::getSingleton('core/session')->addError(Mage::helper('exportproduct')->__($excel));
			$this->_redirect('*/*/error');
			return;
		    }
		}elseif(count($order_customer_collection) == 0){
		    Mage::getSingleton('core/session')->addError(Mage::helper('exportproduct')->__("Orders are not available"));
		    $this->_redirect('*/*/error');
		    return;
		}else{
		    Mage::getSingleton('core/session')->addError(Mage::helper('exportproduct')->__($order_collection));
		    $this->_redirect('*/*/error');
		    return;    
		}
	    }elseif($exportproduct_status == 2){
		Mage::getSingleton('core/session')->addError(Mage::helper('exportproduct')->__("URL is currently disabled!"));
		$this->_redirect('*/*/error');
		return;
	    }else{
		Mage::getSingleton('core/session')->addError(Mage::helper('exportproduct')->__($exportproduct_status));
		$this->_redirect('*/*/error');
		return;
	    }
	}else{
	    Mage::getSingleton('core/session')->addError(Mage::helper('exportproduct')->__("URL is not correct!"));
	    $this->_redirect('*/*/error');
	    return;
	}
    }
    
    public function errorAction()
    {
	$this->loadLayout();     
	$this->renderLayout();
    }
}