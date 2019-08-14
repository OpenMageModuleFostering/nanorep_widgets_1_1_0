<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */

class NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Body extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$value = $row -> getData($this -> getColumn() -> getIndex());
		$out = array();
		if($value != ""){
			$out[] = '<a href="#" onclick="$(\'answer_body_'.$row->getAnswerId().'_place_holder\').toggle();">Toggle Body</a>';
			$out[] = '<div id="answer_body_'.$row->getAnswerId().'_place_holder" style="display:none;">';
		}
		$out[] = htmlspecialchars_decode($value);
		if($value != ""){
			$out[] = '</div>';
		}
		return join('',$out);
	}
}
