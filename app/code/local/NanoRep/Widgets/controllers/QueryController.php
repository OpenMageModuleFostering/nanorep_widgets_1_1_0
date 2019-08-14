<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_QueryController extends Mage_Core_Controller_Front_Action
{
	public function storeQueryAction(){
		$query = $this->getRequest()->getPost('query');
		$session = Mage::helper('nanorepwidgets')->getSession();
		$queries = $session->getData('nanorep_queries');
		if(is_null($queries)) $queries = array();
		$queries[] = $query;
		$session->setData( 'nanorep_queries', $queries);
		$this->getResponse()->setBody(Zend_Json::encode($session->getData('nanorep_queries')));
	}
	
}