<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep. 
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Model_Observer
{
	private $session;
    
	public function __construct(){
		$this->_session = Mage::helper('nanorepwidgets')->getSession();
	}
    /**
     * Add order information into NanoRep Widget block to render on checkout success pages
     *
     * @param Varien_Event_Observer $observer
     */
    public function setNanoRepWidgetOnOrderSuccessPageView(Varien_Event_Observer $observer)
    {
		$block = Mage::app()->getFrontController()->getAction()->getLayout()->createBlock(
		'NanoRep_Widgets_Block_Success',
		'nanorep_success_widget');
        if ($block) {
        	$body = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('before_body_end');
            $body->append($block);
        }
		
		if($this->_isThereNanoRepQueryInCustomerSession()){
			$queries = $this->_getNanorepQueries();
			foreach($queries as $product_id => $product_queries){
				foreach ($product_queries as $query => $results) {
					if(!is_null($query)){
						$queryModel = Mage::getModel('nanorepwidgets/query');
						$lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
					    $order = Mage::getSingleton('sales/order'); 
					    $order->load($lastOrderId);
						$queryModel->setProductId($product_id);
						$queryModel->setOrderId($order->getId());
						$queryModel->setQuery($query);
						$queryModel->setResults($results);
						$queryModel->setDate(Mage::getModel('core/date')->timestamp(time()));
						$queryModel->save();
					}
				}
			}
			$this->_session->clear();
		}
    }
	
	private function _isThereNanoRepQueryInCustomerSession(){
		$queries = $this->_session->getData('nanorep_queries');
		return (!is_null($queries) && !empty($queries)) ? true: false; 
	}
	
	private function _getNanorepQueries(){
		$queries = $this->_session->getData('nanorep_queries');
		return $queries;
	}
	
	
	
}
