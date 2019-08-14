<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_Model_Query extends Mage_Core_Model_Abstract {
	/**
     * Initialize resources
     */
    protected function _construct()
    {
        $this->_init('nanorepwidgets/query');
    }
}