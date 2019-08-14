<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_IndexController extends Mage_Core_Controller_Front_Action
{
	private $admin_logged_in = false;
		
	public function preDispatch(){
				// Ensure we're in the admin session namespace for checking the admin user..
		Mage::getSingleton('core/session', array('name' => 'adminhtml'))->start();
		
		$this->admin_logged_in = Mage::getSingleton('admin/session', array('name' => 'adminhtml'))->isLoggedIn();
		
		// ..get back to the original.
		Mage::getSingleton('core/session', array('name' => $this->_sessionNamespace))->start();
		
		parent::preDispatch();
	}
	
	public function getcustomerhistoryAction(){
		if($this->admin_logged_in){
			$email = $this->getRequest()->getParam("email");
			$this->loadLayout();
			$historyModel = Mage::getSingleton("nanorepwidgets/history");
			$historyModel->setEmail($email);
			// var_dump($grid);
	        $this->renderLayout();	
		}
	}
	
	public function getcustomerhistoryjsonAction(){
		if($this->admin_logged_in){
			$email = $this->getRequest()->getParam("email");
			$callbackMethod = $this->getRequest()->getParam("callback_method");
			$this->loadLayout();
			$historyModel = Mage::getSingleton("nanorepwidgets/history");
			$historyModel->setEmail($email);
			if(!is_null($callbackMethod) && $callbackMethod != "")
				$historyModel->setCallbackMethod($callbackMethod);
	        $this->renderLayout();
		}
	}
	public function cdcAction(){
		$this->loadLayout();
        $this->renderLayout();	
	}	
	
}