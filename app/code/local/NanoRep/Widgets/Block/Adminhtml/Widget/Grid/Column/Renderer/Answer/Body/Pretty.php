<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */

class NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Answer_Body_Pretty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
	public function render(Varien_Object $row) {
		$value = $row -> getData($this -> getColumn() -> getIndex());
		return strip_tags(preg_replace('#<br\s*/?>#', "\n", htmlspecialchars_decode($value)));
	}
}
