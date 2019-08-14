<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */

class Nanorep_Widgets_Model_System_Config_Backend_Username extends Mage_Core_Model_Config_Data {

  protected function _beforeSave()
  {
    if($this->isValueChanged())
    {
      $value = $this->getValue();
        if(strpos($value, '@') != false){
          Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('nanorepwidgets')->__("The account name should not contain the '@company' part"));
          $modValue = substr($value, 0, strpos($value, '@'));
          $this->setValue($modValue);
        }
        return $this;
    }
  }
}
