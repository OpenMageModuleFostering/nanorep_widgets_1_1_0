<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_Block_Adminhtml_Answer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'nanorepwidgets';
        $this->_controller = 'adminhtml_answer';
        $this->_headerText = Mage::helper('nanorepwidgets')->__('nanoRep search report');
        parent::__construct();
        $this->_removeButton('add');
    }
}
