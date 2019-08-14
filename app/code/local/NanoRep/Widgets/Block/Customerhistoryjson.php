<?php
 /**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Block_Customerhistoryjson extends Mage_Core_Block_Template
{
	/**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_collection';
    }
	
	protected function _beforeToHtml(){
		
		$collection = Mage::getResourceModel($this->_getCollectionClass());
		$historyModel = Mage::getSingleton('nanorepwidgets/history');
		if(!is_null($historyModel->getEmail()) && $historyModel->getEmail() != ""){
			$collection->addAttributeToFilter("customer_email", $historyModel->getEmail());
	        $this->setCollection($collection);
		}
		
		$orders = array();
		
		foreach ($collection as $order) {
			$link = $this->helper("adminhtml")->getUrl('adminhtml/sales_order/view', array("order_id" => $order->getId()));
			$orders[] = array(
				"id" => $order->getId(),
				"created_at"=> $order->getData('created_at'),
				"grand_total" => $order->getData('grand_total'),
				"link" => $link
			);
		}
		echo "orderHistory =" . Zend_Json::encode($orders) . ";";
		if(!is_null($historyModel->getCallbackMethod()) && $historyModel->getCallbackMethod() != ""){
			$callbackMethod = $historyModel->getCallbackMethod();
			echo $callbackMethod."(orderHistory);";
		}
	}
}
	