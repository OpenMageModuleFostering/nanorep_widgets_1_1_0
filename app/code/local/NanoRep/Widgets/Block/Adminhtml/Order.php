<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'nanorepwidgets';
        $this->_controller = 'adminhtml_order';
        $this->_headerText = Mage::helper('nanorepwidgets')->__('Purchases Assisted by nanoRep');
        parent::__construct();
        $this->_removeButton('add');
    }
}
