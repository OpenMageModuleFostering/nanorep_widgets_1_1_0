<?php
/**
 * NanoRep Widgets Extension
 *
 * @package     NanoRep_Widgets
 * @company     Omniscience Co. 
 * @website     http://www.omniscience.co.il
 * @author      Dan Aharon-Shalom
 */

class Nanorep_Widgets_Model_System_Config_Source_OrdersStatusActivePages {
	public function toOptionArray() {
		return array(
			array(
				'value' => 0, 
				'label' => Mage::helper('nanorepwidgets') -> __('All Pages')
			), 
			array(
				'value' => 1, 
                'label' => Mage::helper('nanorepwidgets') -> __('Customer Account Pages')
			 )
		 );
	}

}
