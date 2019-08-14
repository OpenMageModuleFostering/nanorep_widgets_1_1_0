<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_Adminhtml_MynanorepController extends Mage_Adminhtml_Controller_Action
{
	protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('NanoRep_Widgets');
    }
    
    public function indexAction()
    {
        $this->_title($this->__('my.nanoRep'));

        $this->loadLayout();
        $this->_setActiveMenu('nanorep/mynanorep');
        $this->renderLayout();
    }
	
	public function getcustomerhistoryAction(){
		$email = $this->getRequest()->getParam("email");
		$this->loadLayout();
		$grid = Mage::getSingleton("nanorepwidgets/grid");
		$grid->setEmail($email);
		// var_dump($grid);
        $this->renderLayout();		
	}
	
	// public function getlinkAction(){
		// $email = $this->getRequest()->getParam("email");
		// $error = null;
		// $response = array();
		// if(is_null($email) || $email == "")
			// $error = "Missing customer email argument";
// 		
		// if(!is_null($error)){
			// $this->getResponse()->setBody(Zend_Json::encode(array("error" => $error)));
		// }
		// else{
			// $this->getResponse()->setBody(Zend_Json::encode(array("url" => Mage::helper('adminhtml')->getUrl('*/*/getcustomerhistory', array("email" => $email)))));
		// }
	// }
}
