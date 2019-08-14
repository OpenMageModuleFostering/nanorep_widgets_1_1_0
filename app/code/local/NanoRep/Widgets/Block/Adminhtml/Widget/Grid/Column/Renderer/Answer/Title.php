<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */

class NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Title extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$value = $row -> getData($this -> getColumn() -> getIndex());
		$out = array();
		if($value != ""){
			$value_arr = explode(':::', $value);
			if(count($value_arr) > 0){
				if(count($value_arr) > 1 && $value_arr[0] == "<Unanswered questions>"  && $value_arr[1] != ""){
					$out[] = '<a href="#" onclick="$(\'answer_query_'.$row->getAnswerId().'_place_holder\').toggle();">';
				}
				$out[] = htmlspecialchars($value_arr[0]);
				if(count($value_arr) > 1 && $value_arr[0] == "<Unanswered questions>" && $value_arr[1] != ""){
					$out[] = '</a>';
					$out[] = '<div id="answer_query_'.$row->getAnswerId().'_place_holder" style="display:none;">';
					$queries_arr = explode("###", $value_arr[1]);
					foreach($queries_arr as $query){
						$out[] = $query;
						$out[] = "<br/>";
					}
					array_pop($out);
				$out[] = '</div>';
				}
			}
		}
		return join('',$out);
	}
}
