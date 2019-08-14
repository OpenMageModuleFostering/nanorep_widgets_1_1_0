<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_Model_Resource_Answer extends Mage_Core_Model_Resource_Db_Abstract{
    protected function _construct()
    {
        $this->_init('nanorepwidgets/answer', 'entity_id');
    }   
}