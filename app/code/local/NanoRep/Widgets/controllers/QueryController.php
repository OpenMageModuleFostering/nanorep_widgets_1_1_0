<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */
class NanoRep_Widgets_QueryController extends Mage_Core_Controller_Front_Action
{
	public function storeQueryAction(){
		$query = $this->getRequest()->getPost('query');
		$results = serialize(Zend_Json::decode($this->getRequest()->getPost('results')));
		$session = Mage::helper('nanorepwidgets')->getSession();
		$product_id = $this->getRequest()->getPost('product_id');
		if(is_null($product_id)){
			$product_id = 0;
		}
		$queries = $session->getData('nanorep_queries');
		if(!is_null($query)){
			if(is_null($queries)) $queries = array();
			if(!key_exists($product_id, $queries)) $queries[$product_id] = array();
			if(!key_exists($query, $queries[$product_id]) || (key_exists($query, $queries[$product_id]) && $queries[$product_id][$query] != $results)){
				$queries[$product_id][$query] = $results;
			}
			$session->setData( 'nanorep_queries', $queries);
			
			$answers = unserialize($results);
			$date = Mage::getModel('core/date')->date('Y-m-d H:i:s');
			if($product_id != 0){
				if(!empty($answers)){
					foreach($answers as $answerObject){
						$answer = Mage::getModel('nanorepwidgets/answer');
						$answer->setAnswerId($answerObject["answerId"])
							->setProductId($product_id)
							->setQuery($query)
							->setAnswerTitle($answerObject["title"])
							->setAnswerBody(htmlspecialchars($answerObject["body"]))
							->setDate($date)
							->save();
					}		
				}
				else{
					$answer = Mage::getModel('nanorepwidgets/answer');
					$answer->setAnswerId(0)
						->setProductId($product_id)
						->setQuery($query)
						->setAnswerTitle("<Unanswered questions>")
						->setAnswerBody("")
						->setDate($date)
						->save();
				}
			}
			
			$this->getResponse()->setBody(Zend_Json::encode($session->getData('nanorep_queries')));
		}
		
	}
	
}