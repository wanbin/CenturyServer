<?php
    require_once 'BaseModel.php';
    class XmlModel extends BaseModel
    {
        public static $xmlName = '';

        public function getXmlArray()
        {
            $xmlFile = PATH_STATIC . $this->xmlName . '.xml';
            
            $XML = simplexml_load_file($xmlFile);
    
            $xmlArr = array();
            foreach ($XML->children() as $group) {
            	$groupid = (string) $group['id'];
            	$temp = array();
            	foreach ($group as $item) {
            		$itemid = (string) $item['id'];
            		$item_arr = (array) $item;
            		$temp[$itemid] = $item_arr['@attributes'];
            	}
            	$xmlArr[$groupid] = $temp;
            }
			
            Return $xmlArr;
        }
    }
?>
