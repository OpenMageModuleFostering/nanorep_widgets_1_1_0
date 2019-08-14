<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Block_Contactus extends Mage_Core_Block_Template
{
    
	public function getFieldsCssCommaSeperated(){
	    $_warpWithQoutes = function($var){
            return '"' . $var .'"';        
        };
        
	    $a = unserialize(Mage::getStoreConfig('nanorepwidgets/contact_us_widget/fields_css'));
	    $a = array_map($_warpWithQoutes, $a["_1424883221899_899"]);
        
	    $string = implode(', ', $a);
	    return $string;
	}
    
    public function getApiServerUrl($script){
        $server = Mage::helper('nanorepwidgets')->getServer() 
            . '/~' . Mage::getStoreConfig("nanorepwidgets/account_settings/account_name") . 
            '/api/widget/jsonp/v1/'. $script;
            
        return $server;
    }
    
    public function getKb(){
        return Mage::getStoreConfig('nanorepwidgets/contact_us_widget/kb');
    }
    
    public function getTitle(){
        return Mage::getStoreConfig('nanorepwidgets/contact_us_widget/title');
    }
}