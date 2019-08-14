<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */

class NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Questions_Pretty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$value = $row -> getData($this -> getColumn() -> getIndex());
		$value_array = explode(', ', $value);
		$out = array();
		$limit = 5;
		foreach ($value_array as $question) {
			if($limit > 0){
				$out[] = addslashes(strip_tags($question));
				$out[] = "\n";
				$limit--;
			}
		}
		array_pop($out);
		return join('', $out);
	}

}
