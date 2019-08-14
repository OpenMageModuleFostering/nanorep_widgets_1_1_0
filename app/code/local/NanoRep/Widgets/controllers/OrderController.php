<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_OrderController extends Mage_Core_Controller_Front_Action
{
	/**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::getUrl('nanorepwidgets/account/login');
		
    	if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
		    $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
	
	public function listAction(){
		$this->loadLayout();
        $this->renderLayout();
	}
}