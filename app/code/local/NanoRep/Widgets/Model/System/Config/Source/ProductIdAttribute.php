<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep. 
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */	
class NanoRep_Widgets_Model_System_Config_Source_ProductIdAttribute
{
    protected $_options;

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->_options) {
        	$attributes = Mage::getSingleton('eav/config')
    			->getEntityType(Mage_Catalog_Model_Product::ENTITY)->getAttributeCollection();
			$this->_options[] = array('value'=>"id", 'label'=> Mage::helper('adminhtml')->__('Product Id'));
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