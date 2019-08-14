<?php
class Manthan_Marketplace_Block_Rating extends Mage_Core_Block_Template {
     
	 public function __construct() {
		 
        $this->setTemplate('marketplace/seller/rating.phtml');
        parent::__construct();
    }
	public function ratingCollection()
	{
		return Mage::getModel('marketplace/rating')->getCollection()
		->addFieldToFilter('status',Manthan_Marketplace_Model_Rating::STATUS_ENABLED);
	}
	public function getValue($ratingId)
	{	
		$ratedCollection = Mage::getModel('marketplace/sellerrate')
			->getCollection()
			->addFieldToFilter('rating_id', $ratingId)
			->addFieldToFilter('shipment_item_id', $this->getShipmentItemId());
			
		if($ratedCollection->count())
			return $ratedCollection->getFirstItem()->getValue();
		else
			return 0;
	}
}

?>
