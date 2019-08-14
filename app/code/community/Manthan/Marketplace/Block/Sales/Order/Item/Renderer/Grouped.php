<?php 
class Manthan_Marketplace_Block_Sales_Order_Item_Renderer_Grouped extends Mage_Sales_Block_Order_Item_Renderer_Grouped
{
	public function sellerLink()
	{
		$productId = $this->getItem()->getProductId();
		$sellerCollection = Mage::getModel('marketplace/vendorproduct')->getCollection()
							->addFieldToFilter('product_id',$productId);
		$sellerProfile = Mage::getModel('marketplace/seller')->getCollection()
		->addFieldToFilter('user_id',$sellerCollection->getFirstItem()->getUserId());
		return $sellerProfile->getFirstItem();
	}
	public function isSellerProduct()
	{
		$productId = $this->getItem()->getProductId();
		$collection = Mage::getModel('marketplace/vendorproduct')->getCollection()
							->addFieldToFilter('product_id',$productId);
		return $collection->count() == 0 ? null : true ;
	}
	public function getReviewUrl()
	{
		return $this->getUrl('marketplace/review/save');
	}
	
	public function shipmentItemId()
	{
		$shipmentItemCollection = Mage::getModel('sales/order_shipment_item')->getCollection()
			->addFieldToFilter('order_item_id', $this->getItem()->getId());
		$shipmentItemId = $shipmentItemCollection->getFirstItem()->getId();
		if($shipmentItemId)
			return $shipmentItemId;
		else
			return null;		
	}
	public function isReviewItemRated()
	{
		$shipmentItemId = $this->shipmentItemId();
		$reviewCollection = Mage::getModel('marketplace/review')->getCollection()
		->addFieldToFilter('shipment_item_id',$shipmentItemId);
		
		$ratingIds = Mage::getModel('marketplace/rating')->getCollection()->getAllIds();
		$sellerRateCollection = Mage::getModel('marketplace/sellerrate')->getCollection()
								->addFieldToFilter('shipment_item_id',$shipmentItemId);
		$ratesIds =  array_diff($ratingIds,$sellerRateCollection->getColumnValues('rating_id'));
		if($reviewCollection->count() > 0 && $sellerRateCollection->count() > 0 && !$ratesIds )
			return true;	
		else
			return null;
	}
	
	public function getSellerUrl($sellerId)
	{
		$seller = Mage::getModel('marketplace/seller')->load($sellerId);
		return Mage::helper('marketplace')->getShopUrl($seller->getUrlPath());
	}
}
?>