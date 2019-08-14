<?php
/**
 * NanoRep Widgets Extension
 *
 * @package     NanoRep_Widgets
 * @company     Omniscience Co. 
 * @website     http://www.omniscience.co.il
 * @author      Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Block_Adminhtml_System_Config_Form_Fields extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    
    public function __construct()
    {
        $this->addColumn('css', array(
            'label' => Mage::helper('nanorepwidgets')->__('field CSS'),
            'style' => 'width:120px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('nanorepwidgets')->__('Add field');
        parent::__construct();
    }
    
}
