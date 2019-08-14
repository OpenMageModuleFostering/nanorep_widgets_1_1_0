<?php
/**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		nanoRep.
 * @website		http://www.nanorep.com
 * @author		Dan Aharon-Shalom
 */

class Nanorep_Widgets_Model_Request extends Varien_Object 
{
    public function toPostData()
    {
        $poststring = "";
        if($this->hasData()){
            foreach($this->getData() as $key=>$value){
                $poststring .= "$key=$value&";
            }
            $poststring = substr($poststring, 0, -1);
        }
        return $poststring;
    }
}