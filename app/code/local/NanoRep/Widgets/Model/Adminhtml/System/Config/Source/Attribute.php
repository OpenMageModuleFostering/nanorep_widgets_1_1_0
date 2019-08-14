<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_Model_Adminhtml_System_Config_Source_Attribute
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
        	$attributes = Mage::getSingleton('eav/config')
    			->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getAttributeCollection();
			$this->_options[] = array('value'=>"all", 'label'=> Mage::helper('adminhtml')->__('--All--'));
			foreach ($attributes as $attr) {
				$label = $attr->getStoreLabel() ? $attr->getStoreLabel() . " (".$attr->getAttributeCode().")" : $attr->getFrontendLabel() . " (".$attr->getAttributeCode().")";
				$value = $attr->getAttributeCode();
				if ($value != "price"){
					if($label != ""){
						$this->_options[] = array('value'=>$value, 'label'=> $label);
					}
				}
			}
        }

        $options = $this->_options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
}