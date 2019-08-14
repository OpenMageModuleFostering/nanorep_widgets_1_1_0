<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep. 
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */	

class Nanorep_Widgets_Model_System_Config_Source_FloatWidgetActivePages {
	public function toOptionArray() {
		return array(
			array(
				'value' => 0, 
				'label' => Mage::helper('nanorepwidgets') -> __('All Pages')
			), 
			array(
				'value' => 1, 
                'label' => Mage::helper('nanorepwidgets') -> __('Product Pages Only')
			 )
		 );
	}

}
