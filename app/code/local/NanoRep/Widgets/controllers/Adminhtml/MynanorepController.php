<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
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
}
