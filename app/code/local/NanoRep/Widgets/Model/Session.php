<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */


class NanoRep_Widgets_Model_Session extends Mage_Core_Model_Session_Abstract
{
	public function __construct()
    {
        $this->init('nanorepwidgets');
    }
}
