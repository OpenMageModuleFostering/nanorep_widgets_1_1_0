<?php
/**
 * TravelersBox Api Extension
 *
 * @package		TravelersBox_Api
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */

class NanoRep_Widgets_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getAccountName(){
		return Mage::getStoreConfig("nanorepwidgets/account_settings/account_name");
	}
	
	public function isFloatWidgetActive(){
	    if(Mage::getStoreConfigFlag("nanorepwidgets/float_widget/active_pages")){
            $module = Mage::app()->getRequest()->getModuleName();
            $controller = Mage::app()->getRequest()->getControllerName();
            return(($module == 'catalog' && $controller == 'product') && Mage::getStoreConfigFlag("nanorepwidgets/float_widget/active"));
        }
        else{
		  return Mage::getStoreConfigFlag("nanorepwidgets/float_widget/active");
        }
	}
	
	public function getUsername(){
		return Mage::getStoreConfig("nanorepwidgets/account_settings/username") . '@'. Mage::getStoreConfig("nanorepwidgets/account_settings/account_name");
	}
	
	public function getPassword(){
		return Mage::getStoreConfig("nanorepwidgets/account_settings/password");
	}
	
	public function getAttributes(){
		return (is_null(Mage::getStoreConfig("nanorepwidgets/account_settings/attribute_select"))) ? array(): explode(',', Mage::getStoreConfig("nanorepwidgets/account_settings/attribute_select"));
	}
	
	public function isSupportWidgetEnabled(){
		return Mage::getStoreConfigFlag("nanorepwidgets/support_widget/active");
	}
    
    // public function getSupportWidgetUsername(){
        // return Mage::getStoreConfig("nanorepwidgets/support_widget/username");
    // }
    
    // public function getSupportWidgetPassword(){
        // return Mage::getStoreConfig("nanorepwidgets/support_widget/password");
    // }
	
	public function getSupportWidgetWidth(){
		return Mage::getStoreConfig("nanorepwidgets/support_widget/width");
	}
	
	public function getSupportWidgetMaxHeight(){
		return Mage::getStoreConfig("nanorepwidgets/support_widget/max_height");
	}
    
	
	public function getSupportWidgetFaqNum(){
		return Mage::getStoreConfig("nanorepwidgets/support_widget/faq_num");
	}
    
     public function getSupportWidgetKb(){
        return Mage::getStoreConfig("nanorepwidgets/support_widget/kb");
    }
	
    public function getSupportWidgetSkip(){
        return Mage::getStoreConfig("nanorepwidgets/support_widget/skip");
    }
    
    public function getSupportWidgetByPopularity(){
        return Mage::getStoreConfigFlag("nanorepwidgets/support_widget/by_popularity");
    }
    
    public function getSupportWidgetDays(){
        return Mage::getStoreConfig("nanorepwidgets/support_widget/days");
    }
    
    public function getSupportWidgetLabelId(){
        return Mage::getStoreConfig("nanorepwidgets/support_widget/label_id");
    }
    
    public function getSupportWidgetTextFilter(){
        return Mage::getStoreConfig("nanorepwidgets/support_widget/text_filter");
    }
    
    public function getSupportWidgetProductFaqHeadline(){
        return Mage::getStoreConfig("nanorepwidgets/support_widget/product_faq_headline");
    }
    
    public function getSupportWidgetGeneralFaqHeadline(){
        return Mage::getStoreConfig("nanorepwidgets/support_widget/general_faq_headline");
    }
    
    public function getSupportWidgetMaxItems(){
        return Mage::getStoreConfig("nanorepwidgets/support_widget/max_items");
    }
    
    public function getSupportWidgetCacheTimeout(){
        return Mage::getStoreConfig("nanorepwidgets/support_widget/cache_timeout");
    }
    
	public function getStoreQueryInCookieUrl(){
		return Mage::getBaseUrl() . 'nanorep/query/storeQuery';
	}
	
	public function getSession(){
		return Mage::getSingleton('nanorepwidgets/session');
	}
}