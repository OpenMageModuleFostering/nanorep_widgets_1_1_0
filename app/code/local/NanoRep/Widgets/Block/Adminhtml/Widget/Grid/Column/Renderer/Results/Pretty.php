<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */

class NanoRep_Widgets_Block_Adminhtml_Widget_Grid_Column_Renderer_Results_Pretty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
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
						foreach ($result as $key => $value) {
							if($key == "title"){
								$title = $value;
							}
							if($key == "body"){
								$body = $value;
							}
						}
						if($title != "" && $body != ""){
							$out[] = addslashes(strip_tags($title))."\n";
							$out[] = "\t".addslashes(strip_tags($body));
							$out[] = "\n";
						}
					}
					$limit--;
				}
				array_pop($out);
				$out[] = '\n';
			}
		}
		array_pop($out);
		return join('', $out);
	}

}
