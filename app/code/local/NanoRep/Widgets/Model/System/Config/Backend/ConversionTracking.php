<?php
/**
 * MCI Co.
 * http://www.mage.co.il
 *
 * @package    Mci_Core
 * @copyright  Copyright (c) 2014 MCI Co. (http://www.mage.co.il)
 * @author	   info@mage.co.il
 */

class Nanorep_Widgets_Model_System_Config_Backend_ConversionTracking extends Mage_Core_Model_Config_Data {
    
	protected function _afterSaveCommit()
    {
        if($this->isValueChanged() || Mage::getStoreConfig('nanorepwidgets/account_settings/conversion_script') == "")   
        {
            if (function_exists("curl_init")) {
            	$url = "https://my.nanorep.com/api/addconversion";
				$request = Mage::getModel('nanorepwidgets/request');
				$request->setData('account', $this->getFieldsetDataValue("account_name"));
				$request->setData('url', rtrim(Mage::getBaseUrl(),"/"));
				$request->setData('apikey', urlencode($this->getFieldsetDataValue("account_api_key")));
				$request->setData('ab', '5');
				$request->setData('name', 'Conversion');
				$currencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
				if($currencyCode == "ILS"){
					$currencyCode = "NIS";
				}
				$request->setData('currency', $currencyCode);
				
				if(Mage::getStoreConfigFlag('nanorepwidgets/account_settings/debug')){
					Mage::log("Request: " . $url ."?" . $request->toPostData(), null, "nanorep-conversion-script-debug.log");
				}
				
                $CR = curl_init();
                curl_setopt($CR, CURLOPT_URL, $url);
                curl_setopt($CR, CURLOPT_POST, 1);
                curl_setopt($CR, CURLOPT_POSTFIELDS, $request->toPostData());
                curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($CR, CURLOPT_FAILONERROR, true);

                //actual curl execution perfom
                $result = curl_exec($CR);
                $error = curl_error($CR);

                if ($error) {
                    if(Mage::getStoreConfigFlag('nanorepwidgets/account_settings/debug')){
						Mage::log("Error: " . $error, null, "nanorep-conversion-script-debug.log");
					}
					
                }
				if($result){
					if(Mage::getStoreConfigFlag('nanorepwidgets/account_settings/debug')){
						Mage::log("Result: " . $result, null, "nanorep-conversion-script-debug.log");
					}
					try{
						if(is_string($result)){
							$result = stripslashes($result);
							$result = Zend_Json::decode($result);
							if($result["result"] == "error"){
								Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nanorepwidgets')->__("Faild to create coinversion tracking code, Error: %s", $result["description"]));
							}
							elseif($result["result"] == "success"){
								$config = Mage::getModel("core/config");
								$script = str_replace("ORDER_ID", "{{order_id}}", str_replace("REVENUE", "{{order_amount}}", $result["script"]));
								$config->saveConfig('nanorepwidgets/account_settings/conversion_script', $script, "default", 0);
							}
						}
						elseif(is_object($result)){
							if($result->result == "error"){
								Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nanorepwidgets')->__("Faild to create coinversion tracking code, Error: %s", $result->description));
							}
							elseif($result->result == "success"){
								$config = Mage::getModel("core/config");
								$script = str_replace("ORDER_ID", "{{order_id}}", str_replace("REVENUE", "{{order_amount}}", $result->script));
								$config->saveConfig('nanorepwidgets/account_settings/conversion_script', $script, "default", 0);
							}	
						}
					}
					catch(Zend_Json_Exception $e){
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nanorepwidgets')->__("Faild to create coinversion tracking code, Error: %s", $e->getMessage()));
					}
				}

                curl_close($CR);
            }
        }
        return $this;  
    }
}
