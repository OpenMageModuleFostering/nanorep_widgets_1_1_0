<?php
 /**
 * NanoRep Widgets Extension
 *
 * @package		NanoRep_Widgets
 * @company		Omniscience Co. 
 * @website		http://www.omniscience.co.il
 * @author		Dan Aharon-Shalom
 */
 
class NanoRep_Widgets_Block_Support extends Mage_Core_Block_Template
{
    public function getXml($forProduct = false)
    {
        $helper = $this -> helper("nanorepwidgets");
        $server = "my.nanorep.com";
        $username = $helper -> getUsername();
        $password = $helper -> getPassword();
        $kb = $helper -> getSupportWidgetKb();
        $skip = $helper -> getSupportWidgetSkip();
        $maxItems = $helper -> getSupportWidgetMaxItems();
        $labelId = $helper -> getSupportWidgetLabelId();
        $textFilter = $helper -> getSupportWidgetTextFilter();
        $days = $helper -> getSupportWidgetDays();
        $byPopularity = ($helper -> getSupportWidgetByPopularity()) ? "true" : "false";
        $context = null;
        $product = $this -> getProduct();
        if (is_null($product) && !is_null(Mage::registry('current_product'))){
            $product = Mage::registry('current_product');
        } 
        if($forProduct){
            $context = null;
            if($product){
                $context = array();
                $context["ProductID"] =  $product->getId();
            }
        }
        $cacheTime = $helper -> getSupportWidgetCacheTimeout(); // how long to store the data in the cache (seconds)
        
        //////////////////////////////////////////////////////////////////////////
        // do not edit below this line
        //////////////////////////////////////////////////////////////////////////  
        $contextStr = "";
        $replace = array("=", "+");
        $replacer = array("_", "|");
        
        if($textFilter != null)
        {
            $textFilter = base64_encode($textFilter);
            $textFilter = str_replace($replace, $replacer, $textFilter);
        }
        if($context != null)
        {
            $contextStr = "";
            foreach($context as $key=>$val)
            {
                if(strlen($contextStr) > 0)
                    $contextStr .= ',';
                $contextStr .= $key.':'.$val;
            }
            $contextStr = base64_encode($contextStr);
            $contextStr = str_replace($replace, $replacer, $contextStr);
        }
        $url = "https://".$server."/common/api/kbExport.xml?byPopularity=".$byPopularity."&username=".$username."&pw=".$password."&kb=".$kb."&skip=".$skip."&maxItems=".$maxItems."&days=".$days."&labelId=".$labelId."&textFilter=".$textFilter."&context=".$contextStr;
        $cache_file = Mage::getBaseDir('cache') . "/nanorepcachekb.txt";
        if($forProduct){
            $cache_file = Mage::getBaseDir('cache') . "/nanorepcachekb-p".$product->getId().".txt";   
        }
        
            
        if(!file_exists($cache_file) || time() - $cacheTime > filemtime($cache_file) || strlen(file_get_contents($cache_file)) == 0)
        {
            $ch = curl_init($url);
            $fp = fopen($cache_file, "w");
            
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
        }
            $this->drawContents($cache_file, $forProduct, $product);
    }

    private function drawContents($cachefile, $forProduct, $product)
    {
        $fileContent = file_get_contents($cachefile);
        if(strlen($fileContent) != 0)
        {
            try
            {
                $xml = new SimpleXMLElement($fileContent);
                $attributes = $xml->attributes();
                $account = $attributes["account"];
                $kb_id = $attributes["kb"];
                $items = $xml->xpath("article");
                $out = "";
                $items_out = "";
                if(count($items) > 0){
                    if($forProduct){
                        $out .= '<div id="nanoRepProductFaq">';
                        if(Mage::getStoreConfig('nanorepwidgets/support_widget/product_faq_headline') != ""){
                            $out .= '<h3>' . Mage::getStoreConfig('nanorepwidgets/support_widget/product_faq_headline') . '</h3>';
                        }
                    }
                    else{
                        $out .= '<div id="nanoRepGeneralFaq">';
                        if(Mage::getStoreConfig('nanorepwidgets/support_widget/general_faq_headline') != ""){
                            $out .= '<h3>' . Mage::getStoreConfig('nanorepwidgets/support_widget/general_faq_headline') . '</h3>';
                        }
                    }
                    foreach($items as $item)
                    {
                        $product_attributes = array();
                        $product_attributes["productName"] = htmlspecialchars($product->getName()); 
                        $product_attributes["Price"] =  $product->getFinalPrice();
                        //$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'attribute_id');
                        $attributes = $product->getAttributes();
                        $total_attributes = 0;
                        $use_hidden_attributes = Mage::getStoreConfigFlag('nanorepwidgets/account_settings/hidden_attributes');
                        $selected_attributes = $this->helper('nanorepwidgets')->getAttributes();
                        foreach ($attributes as $attribute) {
                            if ($selected_attributes[0] == "all"){
                                if($attribute->getAttributeCode() != "price")
                                    $total_attributes++;
                            }
                            elseif (in_array($attribute->getAttributeCode(), $selected_attributes)){
                                    $total_attributes++;
                            }
                        }
                        foreach ($attributes as $attribute) {
                            if ($selected_attributes[0] == "all"){
                                if($attribute->getAttributeCode() != "price"){
                                    if(is_string($attribute->getFrontend()->getValue($product))){
                                        $product_attributes[$attribute->getAttributeCode()] =  str_replace("\\", "\\\\", str_replace("\r", ' ', str_replace("\n", ' ', str_replace('"','',htmlentities(trim(strip_tags($attribute->getFrontend()->getValue($product))))))));
                                    }
                                    $total_attributes--;
                                }
                            }
                            elseif (in_array($attribute->getAttributeCode(), $selected_attributes)){
                                if(is_string($attribute->getFrontend()->getValue($product))){
                                    $product_attributes[$attribute->getAttributeCode()] = str_replace("\\", "\\\\", str_replace("\r", ' ', str_replace("\n", ' ', str_replace('"','',htmlentities(trim(strip_tags($attribute->getFrontend()->getValue($product))))))));
                                }
                                $total_attributes--;
                            }
                        }
                        $product_attributes_codes = array_keys($product_attributes);
                        $value = (string)$item->body;
                        $context = $item->contextInfo->context["id"];
                        $tokens = array();
                        $itemVisible = true;
                        if($context == "ProductID" && !$forProduct){
                           $itemVisible = false;
                        }
                        if(preg_match_all("/\{\{(.*?)\}\}/i",$value, $matches)){
                            for ($i = 0; $i < count($matches[0]); $i++) {
                                $tokens[] =$matches[1][$i];
                            }
                            if(!empty($tokens)){
                                foreach($tokens as $code){
                                    if(in_array($code, $product_attributes_codes)){
                                        $value = preg_replace("/\{\{".$code."\}\}/i", $product_attributes[$code], (string)$item->body);
                                    }
                                    else{
                                        $itemVisible = false;
                                    }
                                }
                            }
                            
                        }
                        if($itemVisible){
                            $items_out .= '<div class="nR_title_cl" onclick="toggleArticle(\''.$item["id"].'\', \''.$account.'\', \''.$kb_id.'\', event, this)">
                                        <span class="nR_title_text">'.$item->title.'</span>
                                    </div>';
                            $items_out .= '<div class="nR_body" id="article_'.$item["id"].'">';
                            $items_out .= $value;
                            $items_out .= '</div>';
                        }
                    }
                    if(!empty($items_out)){
                        $out .= $items_out;
                        $out .= '</div>';
                        echo $out;
                    }
                    
                }
            }
            catch(Exception $ex)
            {
                echo $fileContent;
            }
        }
    }
    
}