<?php
class Manthan_Marketplace_Block_Seller_Profile_Review extends Mage_Core_Block_Template {
	
	public function __construct()
    {
        parent::__construct();
		$sellerId = $this->getRequest()->getParam('id');
        $seller = Mage::getModel('marketplace/sellerrate')->getCollection()->addFieldToFilter('seller_id',$sellerId);
		$seller->getSelect()->group('shipment_item_id');
		$shipmentItemIds = $seller->getColumnValues('shipment_item_id');
		$review = Mage::getModel('marketplace/review')->getCollection()
					->addFieldToFilter('status',Manthan_Marketplace_Model_Review::STATUS_APPROVED)
					->addFieldToFilter('shipment_item_id',array('in'=>$shipmentItemIds));
        $this->setCollection($review);
    }
 
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
 
        $toolbar = $this->getToolbarBlock();
 
        // called prepare sortable parameters
        $collection = $this->getCollection();
		
        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        $toolbar->setCollection($collection);
 
        $this->setChild('toolbar', $toolbar);
        $this->getCollection()->load();
        return $this;
    }
    public function getDefaultDirection(){
        return 'DESC';
    }
    public function getAvailableOrders(){
        return array('created_date'=>'Newest to Oldest');
    }
    public function getSortBy(){
        return 'entity_id';
    }
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->createBlock('marketplace/seller_profile_toolbar', microtime());
        return $block;
    }
    public function getMode()
    {
        return;
    }
	public function getRating($shipment_item_id)
	{
		$sellerId = $this->getRequest()->getParam('id');
		$review = Mage::getModel('marketplace/review')->getCollection();
		$collection = Mage::getModel('marketplace/sellerrate')->getCollection()
						->addFieldToFilter('seller_id',$sellerId)
						->addFieldToFilter('shipment_item_id',$shipment_item_id);
		$rating = Mage::getSingleton('core/resource')->getTableName('manthan_marketplace_rating');
		
		$collection->getSelect()
		->join(array('rate' => $rating),'rate.entity_id=`main_table`.rating_id', array('name'));
		$collection->setOrder('name','ASC');		
		return $collection->getData();
	}
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
}

?>
