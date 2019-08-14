<?php
class Manthan_Marketplace_Block_Adminhtml_Review_Rating_Detailed extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('marketplace/rating/detailed.phtml');
    }
	public function ratingCollection()
	{
		return Mage::getModel('marketplace/rating')->getCollection()->addFieldToFilter('status',1);
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
