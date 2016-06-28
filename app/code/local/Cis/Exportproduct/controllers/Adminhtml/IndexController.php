<?php

class Cis_Exportproduct_Adminhtml_IndexController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
		     ->_setActiveMenu('exportproduct/items')
		     ->_addBreadcrumb(Mage::helper('adminhtml')->__("Export Product URL's"), Mage::helper('adminhtml')->__("Export Product URL's"));
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
		     ->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('exportproduct/exportproduct')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('exportproduct_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('exportproduct/items');

			$this->_addBreadcrumb(Mage::helper('exportproduct')->__("Export Product URL's"), Mage::helper('adminhtml')->__("Export Product URL's"));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('exportproduct/adminhtml_exportproduct_edit'))
				->_addLeft($this->getLayout()->createBlock('exportproduct/adminhtml_exportproduct_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('exportproduct')->__("Record does not exist"));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($data['stores']) && $data['stores'] != '') {
				if(in_array('0',$data['stores'])){
					$data['store_ids'] = '0';
				}
				else{
					$data['store_ids'] = implode(",", $data['stores']);
				}
				unset($data['stores']);
			}else{
				
				$data['store_ids'] = Mage::app()->getDefaultStoreView()->getId();
			}
			

			$model = Mage::getModel('exportproduct/exportproduct');		
			$model->setData($data)
			      ->setId($this->getRequest()->getParam('id'));
			try {
				if ($model->getExportproductUrlCreateDate() == NULL || $model->getExportproductUrlUpdateDate() == NULL) {
					$model->setExportproductUrlCreateDate(now())
						->setExportproductUrlUpdateDate(now());
				} else {
					$model->setExportproductUrlUpdateDate(now());
				}
				
				$model->save();
				if($model->getId() && $model->getExportProductUrl() == NULL){
					$encrypted_URL = Mage::helper('exportproduct/adminhtml_url')->getEncryptUrl($model->getId());
					if(isset($encrypted_URL) && $encrypted_URL != ''){
						$updateModel = Mage::getModel('exportproduct/exportproduct')->load($model->getId())->setData('export_product_url',$encrypted_URL);
						try {
							$updateModel->setId($model->getId())->save();
							Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('exportproduct')->__('Record saved successfully'));
						}catch (Exception $e){
							Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('exportproduct')->__("Record doesn't save properly. So please try again."));
						}
					}else{
						
					}
				}else{
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('exportproduct')->__('Record saved successfully'));
				}
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('exportproduct')->__('Unable to find Record to save'));
        $this->_redirect('*/*/');
	}
 
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('exportproduct/exportproduct');
				$model->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('exportproduct')->__('Record deleted successfully'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
	    $exportproductIds = $this->getRequest()->getParam('exportproduct');
	    if(!is_array($exportproductIds)) {
			    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('exportprodcut')->__('Please select records'));
	    } else {
		try {
		    foreach ($exportproductIds as $exportproductId) {
			$exportproduct = Mage::getModel('exportproduct/exportproduct')->load($exportproductId);
			$exportproduct->delete();
		    }
		    Mage::getSingleton('adminhtml/session')->addSuccess(
			Mage::helper('adminhtml')->__(
			    'Total of %d record(s) were successfully deleted', count($exportproductIds)
			)
		    );
		} catch (Exception $e) {
		    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
	    }
	    $this->_redirect('*/*/index');
	}
	    
	public function massStatusAction()
	{
	    $exportproductIds = $this->getRequest()->getParam('exportproduct');
	    if(!is_array($exportproductIds)) {
		Mage::getSingleton('adminhtml/session')->addError($this->__('Please select records'));
	    } else {
		try {
		    foreach ($exportproductIds as $exportproductId) {
			$exportproduct = Mage::getSingleton('exportproduct/exportproduct')
			    ->load($exportproductId)
			    ->setStatus($this->getRequest()->getParam('status'))
			    ->setIsMassupdate(true)
			    ->save();
		    }
		    $this->_getSession()->addSuccess(
			$this->__('Total of %d record(s) were successfully updated', count($exportproductIds))
		    );
		} catch (Exception $e) {
		    $this->_getSession()->addError($e->getMessage());
		}
	    }
	    $this->_redirect('*/*/index');
	}
      
	public function exportproductCsvAction()
	{
	    $fileName   = 'exportproduct.csv';
	    $content    = $this->getLayout()->createBlock('exportproduct/adminhtml_exportproduct_grid')
		->getCsv();
    
	    $this->_sendUploadResponse($fileName, $content);
	}
    
	public function exportproductXmlAction()
	{
	    $fileName   = 'exportproduct.xml';
	    $content    = $this->getLayout()->createBlock('exportproduct/adminhtml_exportproduct_grid')
		->getXml();
    
	    $this->_sendUploadResponse($fileName, $content);
	}
    
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
	{
	    $response = $this->getResponse();
	    $response->setHeader('HTTP/1.1 200 OK','');
	    $response->setHeader('Pragma', 'public', true);
	    $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
	    $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
	    $response->setHeader('Last-Modified', date('r'));
	    $response->setHeader('Accept-Ranges', 'bytes');
	    $response->setHeader('Content-Length', strlen($content));
	    $response->setHeader('Content-type', $contentType);
	    $response->setBody($content);
	    $response->sendResponse();
	    die;
	}

}
