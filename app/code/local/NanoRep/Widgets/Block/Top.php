<?php
 /**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Block_Top extends Mage_Core_Block_Template
{
	public function getCustomerOrdersStatuses(){
	    $session = Mage::getSingleton('customer/session');
        $customer = $session->getCustomer();
        if($customer){
    	    if(Mage::getStoreConfigFlag('nanorepwidgets/orders_status/active')){
               $orders = array();
               $statuses = array(Mage_Sales_Model_Order::STATE_PROCESSING,Mage_Sales_Model_Order::STATE_NEW, Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, "pending");
    	        if(Mage::getStoreConfigFlag('nanorepwidgets/orders_status/active_pages')){
        	       $module = $this->getRequest()->getModuleName();
                   if($module == 'customer' || $module == 'sales'){
                        $orderCollection = Mage::getModel('sales/order')->getCollection()
                            ->addFieldToFilter('customer_id', array('eq' => array($customer->getId())))
                            ->addFieldToFilter('status', array('in' => $statuses));
                        foreach($orderCollection AS $order_row){
                            $status = $order_row->getStatus();
                            $link = $this->getUrl('sales/order/view', array('order_id' =>$order_row->getId()));
                            $orders["orders"][] = array($order_row->getIncrementId() => array(
                                "status" => $status, 
                                "link" => $link
                            ));
                        }
                    }
                }
                else{
                    $orderCollection = Mage::getModel('sales/order')->getCollection()
                        ->addFieldToFilter('customer_id', array('eq' => array($customer->getId())))
                        ->addFieldToFilter('status', array('in' => $statuses));
                    foreach($orderCollection AS $order_row){
                        $status = $order_row->getStatus();
                        $link = $this->getUrl('sales/order/view', array('order_id' =>$order_row->getId()));
                        $orders["orders"][] = array($order_row->getIncrementId() => array(
                            "status" => $status, 
                            "link" => $link
                        ));
                    }
                }
                if(!empty($orders)){
                    echo substr(Zend_Json::encode($orders), 1, strlen(Zend_Json::encode($orders)) - 2) . ",";
                }
    	    }
        }
	}

    public function isUserLoggedIn(){
        $session = Mage::getSingleton('customer/session');
        // $customer = $session->getCustomer();
        if($session->isLoggedIn()){
            return "true";
        }
        return "false";
    } 
    
    public function getCategory(){
        $_product = null;
        if(!is_null($this->getProduct())){
        $_product = $this->getProduct();
        }
        elseif(!is_null(Mage::registry('current_product'))) {
            $_product = Mage::registry('current_product');
        }
        else{
            $_product = null;
        }
        $categories = array();
        if(!is_null($_product)){
        	$cats = $_product->getCategoryIds();
            foreach ($_product->getCategoryIds() as $cat_id) {
                $category = Mage::getModel('catalog/category')->load($cat_id);   
                $categories[] = $category->getId();
            }
            echo '"Category": "'. join(",", $categories) . '",' . "\n";
        }
        
        // $category = Mage::registry('current_category');
        // if(!is_null($category)){
            // echo '"Category": "'. $category->getId() . '",';
        // }
    }
    
    public function getProductRelatedProducts($product){
        if(Mage::getStoreConfigFlag('nanorepwidgets/account_settings/related_products')){
            $related_product_collection = $product->getRelatedProductCollection()->addAttributeToSelect("*");
            if($related_product_collection->count() > 0){
                $related_products = array();
                foreach($related_product_collection as $_product){
                    $related_products["relatedProducts"][] = array("name" => $_product->getName(), "short_description" => $_product->getShortDescription(), "image_url" => $_product->getImageUrl(), "url" => $_product->getProductUrl());
                }
                echo substr(Zend_Json::encode($related_products), 1, strlen(Zend_Json::encode($related_products)) - 2) . ",";
            }
        }
    }
    
    public function getProductUpSellsProducts($product){
        if(Mage::getStoreConfigFlag('nanorepwidgets/account_settings/upsells_products')){
            $upsells_product_collection = $product->getUpSellProductCollection()->addAttributeToSelect("*");
            if($upsells_product_collection->count() > 0){
                $upsells_products = array();
                foreach($upsells_product_collection as $_product){
                    $upsells_products["upSellsProducts"][] = array("name" => $_product->getName(), "short_description" => $_product->getShortDescription(), "image_url" => $_product->getImageUrl(), "url" => $_product->getProductUrl());
                }
                echo substr(Zend_Json::encode($upsells_products), 1, strlen(Zend_Json::encode($upsells_products)) - 2) . ",";
            }
        }
    }
    
    public function getProductCrossSellProducts($product){
        if(Mage::getStoreConfigFlag('nanorepwidgets/account_settings/crosssells_products')){
            $crosssells_product_collection = $product->getCrossSellProductCollection()->addAttributeToSelect("*");
            if($crosssells_product_collection->count() > 0){
                $crosssells_products = array();
                foreach($crosssells_product_collection as $_product){
                    $crosssells_products["crossSellsProducts"][] = array("name" => $_product->getName(), "short_description" => $_product->getShortDescription(), "image_url" => $_product->getImageUrl(), "url" => $_product->getProductUrl());
                }
                echo substr(Zend_Json::encode($crosssells_products), 1, strlen(Zend_Json::encode($crosssells_products)) - 2) . ",";
            }
        }
    }
    
    public function getProductAttributes($product)
    {
        $attributes = $product->getAttributes();
        $total_attributes = 0;
        $use_hidden_attributes = Mage::getStoreConfigFlag('nanorepwidgets/account_settings/hidden_attributes');
        $selected_attributes = $this->helper('nanorepwidgets')->getAttributes();
        foreach ($attributes as $attribute) {
            if ($selected_attributes[0] == "all"){
                if($attribute->getAttributeCode() != "price")
                    $total_attributes++;
            }
            elseif (in_array($attribute->getAttributeCode(), $selected_attributes)){
                    $total_attributes++;
            }
        }
        foreach ($attributes as $attribute) {
            if ($selected_attributes[0] == "all"){
                if($attribute->getAttributeCode() != "price"){
                    if(is_string($attribute->getFrontend()->getValue($product))){
                        echo '"'.$attribute->getAttributeCode().'" : "'.str_replace("\\", "\\\\", str_replace("\r", ' ', str_replace("\n", ' ', str_replace('"','',htmlentities(trim(strip_tags($attribute->getFrontend()->getValue($product)))))))).'"';
                        // do something with $value here
                        if($total_attributes > 1)
                            echo ",\n";
                    }
                    $total_attributes--;
                }
            }
            elseif (in_array($attribute->getAttributeCode(), $selected_attributes)){
                if(is_string($attribute->getFrontend()->getValue($product))){
                    echo '"'.$attribute->getAttributeCode().'" : "'.str_replace("\\", "\\\\", str_replace("\r", ' ', str_replace("\n", ' ', str_replace('"','',htmlentities(trim(strip_tags($attribute->getFrontend()->getValue($product)))))))).'"';
                    // do something with $value here
                    if($total_attributes > 1)
                        echo ",\n";
                }
                $total_attributes--;
            }
        }
    }
}