<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */

class NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Results extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$value = $row -> getData($this -> getColumn() -> getIndex());
		$value_array = explode(':::', $value);
		$out = array();
		$limit = 5;
		foreach ($value_array as $results) {
			if($results != ""){
				if($limit > 0){
					$results = unserialize(trim($results));
					foreach ($results as $aid => $result) {
						$title = "";
						$body = "";
						$answer_id = "";
						foreach ($result as $key => $value) {
							if($key == "title"){
								$title = $value;
							}
							if($key == "body"){
								$body = $value;
							}
							if($key == "answerId"){
								$answer_id = $value;
							}
						}
						if($title != "" && $body != "" && $answer_id != ""){
							$out[] = $title . ' <a href="#" onclick="$(\'answer_body_'.$answer_id.'-'.$row->getQueryId().'_place_holder\').toggle();">Toggle Body</a>';
							$out[] = '<div id="answer_body_'.$answer_id.'-'.$row->getQueryId().'_place_holder" style="display:none;">';
							$out[] = $body;
							$out[] = '</div>';
							$out[] = '<br/>';
							// $out[] = '<span style="font-weight:bold; text-decoration:underline; color: blue;" onMouseOver="this.style[\'text-decoration\'] = \'none\';" onMouseOut="this.style[\'text-decoration\'] = \'underline\';" title="'.$body.'">'.$title.'</span>';
							// $out[] = ', ';
						}
					}
					array_pop($out);
					$out[] = '<br/>';
					$limit--;	
				}
			}
		}
		array_pop($out);
		return join('', $out);
	}

}
