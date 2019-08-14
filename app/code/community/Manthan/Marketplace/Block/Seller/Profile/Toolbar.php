<?php
class Manthan_Marketplace_Block_Seller_Profile_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar {
	
	public function getPagerHtml()
    {
        $pagerBlock = $this->getLayout()->createBlock('page/html_pager');
 
        if ($pagerBlock instanceof Varien_Object) {
 
            /* @var $pagerBlock Mage_Page_Block_Html_Pager */
            $pagerBlock->setAvailableLimit($this->getAvailableLimit());
 
            $pagerBlock->setUseContainer(false)
            ->setShowPerPage(false)
            ->setShowAmounts(false)
            ->setLimitVarName($this->getLimitVarName())
            ->setPageVarName($this->getPageVarName())
            ->setLimit($this->getLimit())
            ->setCollection($this->getCollection());
            return $pagerBlock->toHtml();
        }
        return '';
    }
	 public function getModes()
    {
        return null;
    }
	public function setCollection($collection)
    {
        $this->_collection = $collection;
			  //$this->_collection = Mage::getModel('marketpace/review')->getCollection();
		
        $this->_collection->setCurPage($this->getCurrentPage());

        // we need to set pagination only if passed value integer and more that 0
         $limit = (int)$this->getLimit();
		
	   if ($limit) {
            $this->_collection->setPageSize($limit);
        }
        if ($this->getCurrentOrder()) {
            $this->_collection->setOrder($this->getCurrentOrder(), $this->getCurrentDirection());
        }
        return $this;
    }
	
	/*protected function _getAvailableLimit($mode)
    {
        if (isset($this->_availableLimit[$mode])) {
            return $this->_availableLimit[$mode];
        }
        $perPageValues = (string)Mage::helper('marketplace')->getConfig('review','per_page');
        $perPageValues = explode(',', $perPageValues);
        $perPageValues = array_combine($perPageValues, $perPageValues);
        if (Mage::getStoreConfigFlag('catalog/frontend/list_allow_all')) {
            return ($perPageValues + array('all'=>$this->__('All')));
        } else {
            return $perPageValues;
        }
    }*/
}

?>
