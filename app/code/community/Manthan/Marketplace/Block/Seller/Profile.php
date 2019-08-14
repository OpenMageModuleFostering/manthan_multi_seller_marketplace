<?php
class Manthan_Marketplace_Block_Seller_Profile extends Mage_Core_Block_Template {
	
	public function sellerProfile() 
	{
		$seller = Mage::getModel('marketplace/seller')->getCollection()->addUserData($this->getCurrentSellerId());
		if($seller->count() > 0)
		{	
			$data =  $seller->getFirstItem()->getData();
			return $data;
		}
		else
		{
			$url = Mage::helper('core/url')->getHomeUrl();
			echo Mage::helper('marketplace')->redirectUrl($url);
			exit;
		}
    }
	public function getCurrentSellerId()
	{
		return  $this->getRequest()->getParam('id'); 
	}
	public function ratingOverview()
	{ 
		$ratedCollection = Mage::getModel('marketplace/sellerrate')->getCollection()
							->addFieldToSelect(array('seller_id','rating_id'))
							->addFieldToFilter('seller_id',$this->getCurrentSellerId());
		$ratedCollection ->getSelect()
		->columns('SUM(value) as sub_total,COUNT(*) AS rating_count')
		->group('rating_id');
		
		return 	$ratedCollection->getData();
	}
	public function getRatingName($id)
	{
		$rating = Mage::getModel('marketplace/rating')->load($id);
		return $rating->getName();
	}
	public function getReviewStars()
	{
		$ratedCollection = Mage::getModel('marketplace/sellerrate')->getCollection()
							->addFieldToSelect('*')
							->addFieldToFilter('seller_id',$this->getCurrentSellerId())
							->setOrder('value','DESC');
		 $ratedCollection ->getSelect()
		->columns('SUM(value) as rating_total,COUNT(value) AS value_count')
		->group('value');
		
		return 	$ratedCollection->getData();
	}
	public function getReviewStar($value)
	{
		/*$seller = Mage::getModel('marketplace/sellerrate')->getCollection()
					->addFieldToFilter('seller_id',$sellerId);
		$review = Mage::getSingleton('core/resource')->getTableName('manthan_marketplace_review');
		
		$seller->getSelect()
		->join(array('review' => $review),'review.shipment_item_id=`main_table`.shipment_item_id', array('shipment_item_id'))
		->where('review.status = ?',Manthan_Marketplace_Model_Review::STATUS_APPROVED);
		$shipmentIds = array_unique($seller->getColumnValues('shipment_item_id'));*/
		
		$ratedCollection = Mage::getModel('marketplace/sellerrate')->getCollection()
							->addFieldToFilter('value',$value)
							->addFieldToFilter('seller_id',$this->getCurrentSellerId());
		$ratedCollection ->getSelect()
                ->columns('COUNT(value) AS value_count')
				->group('value');
		return 	$ratedCollection->getFirstItem()->getData();
	}
	public function getPositivePercentage($positiveVote)
	{
		$ratedCollection = Mage::getModel('marketplace/sellerrate')->getCollection()
							->addFieldToFilter('value',array('in'=>$positiveVote))
							->addFieldToFilter('seller_id',$this->getCurrentSellerId());
		$ratedCollection ->getSelect()
                ->columns('COUNT(value) AS positive_total')
				->group('seller_id');				
		return $ratedCollection->getFirstItem()->getData();
	}
}

?>
