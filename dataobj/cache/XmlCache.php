<?php
    include_once PATH_MODEL . 'XmlModel.php';
    class XmlCache extends XmlModel
    {
        function __construct($xmlName = 'DataBase')
        {
            parent::$xmlName = $xmlName;
        }
        /**
         +----------------------------------------------------------
         * 获取单条XML
         +----------------------------------------------------------
         * @param string $groupid    组id
         * @param int $itemid        记录id
         * @return array             该条记录的值  
         *         array('itemid'=> array('attr'=>'value') )
         +----------------------------------------------------------
         */
        public function getSingleItem($groupid,$itemid)
        {
            $key = $this->getItemKey($groupid, $itemid);
            $item = $this->getFromCache($key, $this->gameuid);
            if (empty($item))
            {
                $group = $this->getGroupCache($groupid);
                $item  = $group[$itemid];
                $this->setToCache($key, $item, $this->gameuid);
            }
            
            return $item;
        }

        /**
         +----------------------------------------------------------
         * 获取不同分组下多个或单个item
         +----------------------------------------------------------
         * @param array $idArr
         *        $idArr = array('groupid' =>'itemid','groupid2'=>'itemid2')
         *   或者       $idArr = array('groupid'=>array('itemid1,itemid2'),...)
         * @return array   返回item数组
         *         array('groupid'=>array( $itemid=>array('attr'=>value) ) ) 
         +----------------------------------------------------------
         */
        public function getMultiItem($idArr)
        {
            $items = array();
            foreach ($idArr as $groupid=>$itemid)
            {
                if (is_string($itemid))
                {
                    $items[$groupid] = $this->getSingleItem($groupid, $itemid);
                }else {
                    $group = $this->getGroupCache($groupid);
                    foreach ($itemid as $id)
                    {
                        $items[$groupid] = $group[$id];
                    }
                }
            }
            
            Return $items;
        }

        /**
         +----------------------------------------------------------
         * 获取指定group的cache
         +----------------------------------------------------------
         * @param string $groupid  组id
         * @return array    group数组
         +----------------------------------------------------------
         */
        private function getGroupCache($groupid)
        {
            $key = $this->getGroupKey($groupid);
            $group = $this->getFromCache($key);
            if(empty($group))
            {
                $all = $this->getXmlCache();
                $group = $all[$groupid];
                $this->setToCache($key,$group);
            }
            return $group;
        }

        /**
         +----------------------------------------------------------
         * 获取xml文件在cache里的所有
         +----------------------------------------------------------
         * @return array  xml的cache缓存
         +----------------------------------------------------------
         */
        private function getXmlCache()
        {
            $key = $this->getXmlKey($this->xmlName);
            $all = $this->getFromCache($key);
            if(empty($all))
            {
                $all = parent::getXmlArray();
                $this->setToCache($key,$all);
            }
            return $all;
        }
        
        
        
        private function getXmlKey($xmlName)
        {
            return sprintf(MEMCACHE_KEY_XML_FILE,$xmlName);
        }
        private function getGroupKey($groupid)
        {
            return sprintf(MEMCACHE_KEY_XML_GROUP,$groupid);
        }
        private function getItemKey($groupid,$itemid)
        {
            return sprintf(MEMCACHE_KEY_XML_ITEM,$groupid,$itemid);
        }
    }
?>
