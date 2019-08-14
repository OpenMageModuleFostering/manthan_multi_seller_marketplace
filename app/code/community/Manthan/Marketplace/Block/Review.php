<?php
class Manthan_Marketplace_Block_Review extends Mage_Core_Block_Template {
     
	 public function __construct() {
        $this->setTemplate('marketplace/seller/review.phtml');
        parent::__construct();
    }
	public function getProductId()
	{
		return $this->getItem()->getProductId();
	}
	
	public function ratingCollection()
	{
		return Mage::getModel('marketplace/rating')->getCollection()->addFieldToFilter('status',1);
	}
	public function getReview()
	{ 
	
		$reviewCollection = Mage::getModel('marketplace/review')->getCollection()
		->addFieldToFilter('shipment_item_id',$this->getShipmentItemId());
		if($reviewCollection->count() > 0)
			return $reviewCollection->getFirstItem();
		else 
			return null;
	}
}

?>
