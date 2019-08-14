<?php
 /**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep. 
 * @website		http://www.nanorep.com
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