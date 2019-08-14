<?php
 /**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep. 
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */	
 
class NanoRep_Widgets_Block_Header extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
    {
    	if(is_null($this->getProduct())){
			$this->setProduct(Mage::registry('current_product'));
		}
		$this->_setContext();
		$this->_setCustomParams();

		parent::_prepareLayout();
    }
	
	private function _setContext(){
		$context = array();
		$context["Website"] = $_SERVER['HTTP_HOST'];
		$_product = $this->getProduct();
		if(!is_null($_product)){
			$context["ProductID"] = $this->_getProductId($_product);
			$context["Manufacturer"] = $_product->getAttributeText('manufacturer');
		}
		$this->setContext(Zend_Json::encode($context));
	}
	
	private function _setCustomParams(){
		$_customParams = array();
		$_customParams["userLoggedIn"] = $this->_isUserLoggedIn();
		$_customParams["orders"] = $this->_getCustomerOrdersStatuses();
		/** @var Mage_Catalog_Model_Product */
		$_product = $this->getProduct();
		if(!is_null($_product)){
			$_customParams["productName"] = htmlspecialchars($_product->getName());
			$_customParams["ProductID"] = $this->_getProductId($_product);
			if(count($_product->getTypeInstance(true)->getOrderOptions($_product)) == 0){
				$_customParams["addToCartUrl"] = Mage::helper('checkout/cart')->getAddUrl($_product);
			}
			$_customParams["Category"] = $this->_getProductCategory($_product);
			$_customParams["Price"] = Mage::helper('core')->currency($_product->getFinalPrice(), true, false);
			
			$_customParams["relatedProducts"] = $this->_getProductRelatedProducts($_product);
			$_customParams["upSellsProducts"] = $this->_getProductUpSellsProducts($_product);
			$_customParams["crossSellsProducts"] = $this->_getProductCrossSellProducts($_product);
			$_customParams["bundleProducts"] = $this->_getProductBundleItems($_product);
 			$_customParams = array_merge($_customParams, $this->_getProductAttributes($_product));
		}
		$this->setCustomParams(Zend_Json::encode($_customParams));
	}
	
	private function _isUserLoggedIn(){
        $session = Mage::getSingleton('customer/session');
        // $customer = $session->getCustomer();
        if($session->isLoggedIn()){
            return "true";
        }
        return "false";
    } 
	
	private function _getCustomerOrdersStatuses(){
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
                        $orders[] = array($order_row->getIncrementId() => array(
                            "status" => $status, 
                            "link" => $link
                        ));
                    }
                }
                if(!empty($orders)){
					return $orders;
                }
				
				return "";
    	    }
        }
	}
	
	private function _getProductId($_product){
		$productIdAttribute = Mage::getStoreConfig('nanorepwidgets/account_settings/product_id_attribute');
		$productID = $_product->getId();
		if($productIdAttribute != "" &&  $productIdAttribute != "id"){
			if($_product->getData($productIdAttribute) != ""){
				$productID = $_product->getData($productIdAttribute);
			}
			else{
				$productID = $_product->getAttributeText($productIdAttribute);
			}
		}
		elseif($productIdAttribute == "sku"){
			$productID = $_product->getsku();
		}
		return $productID;
	}
	
	private function _getProductCategory($_product){
        $categories = array();
        if(!is_null($_product)){
        	$cats = $_product->getCategoryIds();
            foreach ($_product->getCategoryIds() as $cat_id) {
                $category = Mage::getModel('catalog/category')->load($cat_id);   
                $categories[] = $category->getId();
            }
            return join(" ", $categories);
        }
    }
	
	private function _getProductRelatedProducts($product){
        if(Mage::getStoreConfigFlag('nanorepwidgets/account_settings/related_products')){
            $related_product_collection = $product->getRelatedProductCollection()->addAttributeToSelect("*")->addStoreFilter();
            if($related_product_collection->count() > 0){
                $related_products = array();
                foreach($related_product_collection as $_product){
                    $related_products[] = array("name" => $_product->getName(), "short_description" => $_product->getShortDescription(), "image_url" => $_product->getImageUrl(), "url" => $_product->getProductUrl());
                }
                return $related_products;
            }
        }
    }
    
    private function _getProductUpSellsProducts($product){
        if(Mage::getStoreConfigFlag('nanorepwidgets/account_settings/upsells_products')){
            $upsells_product_collection = $product->getUpSellProductCollection()->addAttributeToSelect("*")->addStoreFilter();
            if($upsells_product_collection->count() > 0){
                $upsells_products = array();
                foreach($upsells_product_collection as $_product){
                    $upsells_products[] = array("name" => $_product->getName(), "short_description" => $_product->getShortDescription(), "image_url" => $_product->getImageUrl(), "url" => $_product->getProductUrl());
                }
                return $upsells_products;
            }
        }
    }
    
    private function _getProductCrossSellProducts($product){
        if(Mage::getStoreConfigFlag('nanorepwidgets/account_settings/crosssells_products')){
            $crosssells_product_collection = $product->getCrossSellProductCollection()->addAttributeToSelect("*")->addStoreFilter();
            if($crosssells_product_collection->count() > 0){
                $crosssells_products = array();
                foreach($crosssells_product_collection as $_product){
                    $crosssells_products[] = array("name" => $_product->getName(), "short_description" => $_product->getShortDescription(), "image_url" => $_product->getImageUrl(), "url" => $_product->getProductUrl());
                }
                return $crosssells_products;
            }
        }
    }
	
	private function _getProductBundleItems($product){
        $product_bundle_items = $product->getTypeInstance(true)->getChildrenIds($product->getId(), false);
        if(!is_null($product_bundle_items) && count($product_bundle_items) > 0){
            $bundle_products = array();
            foreach($product_bundle_items as $_option){
            	foreach($_option as $_product_id){
	            	$_product = Mage::getModel('catalog/product')->load($_product_id);
	                $bundle_products[] = array("name" => $_product->getName(), "short_description" => $_product->getShortDescription(), "image_url" => $_product->getImageUrl(), "url" => $_product->getProductUrl());
				}
            }
            return $bundle_products;
        }
    }
    
    private function _getProductAttributes($_product)
    {
    	$output = array();
        $attributes = $_product->getAttributes();
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
                    if(is_string($attribute->getFrontend()->getValue($_product))){
                    	$output[$attribute->getAttributeCode()] = str_replace("\\", "\\\\", str_replace("\r", ' ', str_replace("\n", ' ', str_replace('"','',htmlentities(trim(strip_tags($attribute->getFrontend()->getValue($_product))))))));
                        //echo '"'.$attribute->getAttributeCode().'" : "'.str_replace("\\", "\\\\", str_replace("\r", ' ', str_replace("\n", ' ', str_replace('"','',htmlentities(trim(strip_tags($attribute->getFrontend()->getValue($_product)))))))).'"';
                        // do something with $value here
                        //if($total_attributes > 1)
                        //    echo ",\n";
                    }
                    $total_attributes--;
                }
            }
            elseif (in_array($attribute->getAttributeCode(), $selected_attributes)){
                if(is_string($attribute->getFrontend()->getValue($_product))){
                	$output[$attribute->getAttributeCode()] = str_replace("\\", "\\\\", str_replace("\r", ' ', str_replace("\n", ' ', str_replace('"','',htmlentities(trim(strip_tags($attribute->getFrontend()->getValue($_product))))))));
                    //echo '"'.$attribute->getAttributeCode().'" : "'.str_replace("\\", "\\\\", str_replace("\r", ' ', str_replace("\n", ' ', str_replace('"','',htmlentities(trim(strip_tags($attribute->getFrontend()->getValue($_product)))))))).'"';
                    // do something with $value here
                    //if($total_attributes > 1)
                    //    echo ",\n";
                }
                $total_attributes--;
            }
        }
		return $output;
    }
}