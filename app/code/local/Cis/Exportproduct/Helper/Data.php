<?php

class Cis_Exportproduct_Helper_Data extends Mage_Core_Helper_Abstract
{   
    public function checkExportproductStatus($getKey){
        $collection = Mage::getModel('exportproduct/exportproduct')->getCollection()
								   ->addFieldToFilter('id', array('eq' => $getKey))
								   ->addFieldToSelect(array('status'));						   
        try{
	    $data = $collection->getData();
	    return $data[0]['status'];
	}catch(Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function getExportProducts($exportproduct_url_id){
        $export_collection = Mage::getModel('exportproduct/exportproduct')->load($exportproduct_url_id)->getData();
	$product_ids = $export_collection['product_ids'];
	$store_ids = $export_collection['store_ids'];
	$address_type = 'shipping';
	
	try{
	    $collection = Mage::getResourceModel('sales/order_item_collection')
					    ->addFieldToSelect(array('order_id','sku','name','qty_ordered','price_incl_tax','row_total_incl_tax'))
					    ->addFieldToFilter('sku',array('in' => explode(',',$product_ids)));
	    $collection->getSelect()->joinInner(array('order' => Mage::getSingleton('core/resource')->getTableName('sales/order')),"order.entity_id = main_table.order_id and order.store_id in ($store_ids)",
		array('customer_id','store_id','increment_id','created_at','customer_firstname','customer_lastname','customer_email'))->joinInner(array('customer' => Mage::getSingleton('core/resource')->getTableName('sales/order_address')),"customer.parent_id = order.entity_id and customer.address_type = '$address_type'", array('company','street','postcode','city','region','country_id','telephone'))->order('main_table.order_id DESC');
	    
	    return $collection;
	}catch(Exception $e) {
            return $e->getMessage();
        }                                                               
    }
    
    public function generateExcel($order_customer_collection){
        $xlswriter_class_path = Mage::getBaseDir().'/lib/cis/xlsw/xlsxwriter.class.php';
        require_once($xlswriter_class_path);
        $file_download = $this->createFileFromResult($order_customer_collection);
        try{
            return $file_download;
        }catch(Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function createFileFromResult($order_customer_collection)
    {
        $header = array(
            "Name"        => "string",
            "Company"     => "string",
            "Address"     => "string",
	    "Emailaddress"     => "string",
	    "Phone number"     => "string",
	    "Order Id"     => "string",
	    "Product Name"     => "string",
	    "Product Sku"     => "string",
	    "Product Qty"     => "string",
	    "Product Price"     => "string",
	    "Product Total Price"     => "string",
	    "Order Date"     => "string"
        );
        $writer = new XLSXWriter();
        $writer->writeSheetHeader('Sheet1', $header);//optional
        $resource       = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
    
        foreach ($order_customer_collection as $data) {
            $writer->writeSheetRow('Sheet1', array(
                $data->getCustomerFirstname().' '.$data->getCustomerLastname(),
                $data->getCompany(),
		$data->getStreet().', '.$data->getPostcode().', '.$data->getCity().', '.$data->getRegion().', '.$data->getCountryId(),
                $data->getCustomerEmail(),
		$data->gettelephone(),
		$data->getIncrementId(),
		$data->getName(),
		$data->getSku(),
		$data->getQtyOrdered(),
		$data->getPriceInclTax(),
		$data->getRowTotalInclTax(),
		$data->getCreatedAt()
            ));
        }
        $date = date('mdYhis').rand(0,99999);
        $baseDirectory = Mage::getBaseDir().'/exportorder';
        if(!is_dir($baseDirectory)){
                mkdir($baseDirectory, 0777, true);
                $this->createHtaccess($baseDirectory);
        }
        $excelfile_path = $baseDirectory.'/exportorders' . $date . '.xlsx';
        $writer->writeToFile($excelfile_path);
        chmod($excelfile_path);
        return "exportorders".$date.".xlsx";
    }
    
    public function createHtaccess($baseDirectory){
        $fileLocation = $baseDirectory."/.htaccess";
        $file = fopen($fileLocation,"w");
        $content = 'Order Allow,Deny
        Deny from all
        <FilesMatch "\.(xlsx)$">
        Order Deny,Allow
        Allow from all
        </FilesMatch>';
        fwrite($file,$content);
        fclose($file);
    }
    
    public function sendMail($filePath, $exportproduct_url_id){
	$export_collection = Mage::getModel('exportproduct/exportproduct')->load($exportproduct_url_id)->getData();
	$email_ids = explode(",",$export_collection['email_ids']); // Get email addresses to send email template
	
        $emailTemplateHTML = Mage::getStoreConfig('exportproduct/exportproduct_group/email_template'); // Get email template id from configuration
        if(is_numeric($emailTemplateHTML)){
            $emailTemplate =  Mage::getModel('core/email_template')->load($emailTemplateHTML);    // Load email template content from configuration
        }else{
            $emailTemplate =  Mage::getModel('core/email_template')->loadDefault('exportproduct_template');   // Load email template content directly from email template file
        }
            $from_email = Mage::getStoreConfig('trans_email/ident_general/email'); //fetch sender email Admin
	    $from_name = Mage::getStoreConfig('trans_email/ident_general/name'); //fetch sender name Admin    	   
            $emailTemplate->setSenderEmail($from_email); 
            $emailTemplate->setSenderName($from_name);
            $emailTemplate->setType('html');
	    $emailTemplate->setTemplateSubject($emailTemplate['template_subject']);
	    $fileContents = file_get_contents($filePath); //(Here put the filename with full path of file, which have to be send)*/
	    $attachment = $emailTemplate->getMail()->createAttachment($fileContents);
	    $attachment->filename = "exportOrders.xlsx"; // Provide file name for email
	    foreach($email_ids as $email_id){
		$emailTemplate->send($email_id);
	    }
    }
    
}
