<?php
 /**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Block_Success extends Mage_Core_Block_Template
{
	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('nanorepwidgets/success.phtml');
    }
}