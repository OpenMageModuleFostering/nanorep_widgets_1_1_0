<?php
 /**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep. 
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */	
 

class NanoRep_Widgets_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getAccountName(){
		return Mage::getStoreConfig("nanorepwidgets/account_settings/account_name");
	}
	
	public function isFloatWidgetActiveForCurrentPage(){
	    if(Mage::getStoreConfigFlag("nanorepwidgets/float_widget/active_pages")){
            $module = Mage::app()->getRequest()->getModuleName();
            $controller = Mage::app()->getRequest()->getControllerName();
            return($module == 'catalog' && $controller == 'product');
        }
		else{
			return true;
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
	
	public function isEmbededWidgetEnabled(){
		return Mage::getStoreConfigFlag("nanorepwidgets/embed_widget/active");
	}
	
	public function isEmbededWidgetQuestionsHidden(){
		return Mage::getStoreConfigFlag("nanorepwidgets/embed_widget/question_hidden");
	}
	
    
	public function getEmbededWidgetWidth(){
		return Mage::getStoreConfig("nanorepwidgets/embed_widget/width");
	}
	
	public function getEmbededWidgetMaxHeight(){
		return Mage::getStoreConfig("nanorepwidgets/embed_widget/max_height");
	}
    
	public function getEmbededWidgetDivCss(){
		return Mage::getStoreConfig("nanorepwidgets/embed_widget/div_css");
	}
	
	public function getEmbededWidgetFaqNum(){
		return Mage::getStoreConfig("nanorepwidgets/embed_widget/faq_num");
	}
    
     public function getEmbededWidgetKb(){
        return Mage::getStoreConfig("nanorepwidgets/embed_widget/kb");
    }
	
    public function getEmbededWidgetSkip(){
        return Mage::getStoreConfig("nanorepwidgets/embed_widget/skip");
    }
    
    public function getEmbededWidgetByPopularity(){
        return Mage::getStoreConfigFlag("nanorepwidgets/embed_widget/by_popularity");
    }
    
    public function getEmbededWidgetDays(){
        return Mage::getStoreConfig("nanorepwidgets/embed_widget/days");
    }
    
    public function getEmbededWidgetLabelId(){
        return Mage::getStoreConfig("nanorepwidgets/embed_widget/label_id");
    }
    
    public function getEmbededWidgetTextFilter(){
        return Mage::getStoreConfig("nanorepwidgets/embed_widget/text_filter");
    }
    
    public function getEmbededWidgetProductFaqHeadline(){
        return Mage::getStoreConfig("nanorepwidgets/embed_widget/product_faq_headline");
    }
    
    // public function getEmbededWidgetGeneralFaqHeadline(){
        // return Mage::getStoreConfig("nanorepwidgets/embed_widget/general_faq_headline");
    // }
    
    public function getEmbededWidgetMaxItems(){
        return Mage::getStoreConfig("nanorepwidgets/embed_widget/max_items");
    }
    
    public function getEmbededWidgetCacheTimeout(){
        return Mage::getStoreConfig("nanorepwidgets/embed_widget/cache_timeout");
    }
    
	public function getStoreQueryInCookieUrl(){
		return Mage::getBaseUrl() . 'nanorep/query/storeQuery';
	}
	
	public function getSession(){
		return Mage::getSingleton('nanorepwidgets/session');
	}
}