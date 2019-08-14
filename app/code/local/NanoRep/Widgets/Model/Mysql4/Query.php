<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_Model_Mysql4_Query extends Mage_Core_Model_Mysql4_Abstract{
    protected function _construct()
    {
        $this->_init('nanorepwidgets/query', 'query_id');
    }   
}