<?php
class Manthan_Marketplace_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCurrentSellerSales($fromDate)
	{
		$sellerAmount = 0;
		$sellerId = Mage::getModel('marketplace/seller')->isSeller();
		if(Mage::getModel('marketplace/seller')->isSeller())
		{
			$collection = Mage::getModel('marketplace/payment')->getCollection()
								->addFieldToFilter('seller_id',$sellerId)
								->addFieldToFilter('payment_date', array('from'  => $fromDate));

			if($collection->count() > 0)
			{
				$collection ->getSelect()
					->columns('SUM(seller_paid_amount) as seller_amount')
					->group('seller_id');
				$sellerArray = $collection->getData();	
				$sellerAmount = $sellerArray[0]['seller_amount'];
			}
		}
		return round($sellerAmount,2);
	}
	public function getImagePath($image)
	{
		if(is_null($image))
			$image = 'default.jpg';
		$imagePath = Mage::getBaseUrl('media').'marketplace'.DS.'seller'.DS.'images'.DS. $image;
		return $imagePath;
	}
	public function redirectUrl()
	{
		return Mage::app()->getResponse()->setRedirect(Mage::helper('core/url')->getHomeUrl());
	}
	
	public function getCurrentUserId()
	{
		$userId = Mage::getSingleton('admin/session')->getUser()->getUserId();		
		$loggedInRole = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId();
		$roleId = Mage::getStoreConfig('marketplace/seller/role');
		return $roleId == $loggedInRole ?  $userId :  false;
	}
	public function getShopUrl($url)
	{
		$shopUrl = Mage::helper('core/url')->getHomeUrl()."seller". DS . $url ;
		return $shopUrl;
	}
	public function getSellerInfo($_product)
	{
		$sellerInfo = null;
		$sellerProductCollection = Mage::getModel('marketplace/vendorproduct')->getCollection()
							->addFieldToFilter('product_id',$_product->getId());
		if($sellerProductCollection->count() > 0)
		{
			$userId = $sellerProductCollection->getFirstItem()->getUserId();
			$collection = Mage::getModel('marketplace/seller')->getCollection()
							->addFieldToFilter('user_id',$userId);
			if($collection->count() > 0)
				$sellerInfo = $collection->getFirstItem();
		}
		return $sellerInfo;
	}
	public function isSellerProduct()
	{
		$userId = Mage::getSingleton('admin/session')->getUser()->getUserId();		
		$loggedInRole = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId();
		$roleId = Mage::getStoreConfig('marketplace/seller/role');
		$url = Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/index/");
		if($roleId == $loggedInRole)
		{
				$productId =  Mage::app()->getRequest()->getParam('id');
				$collection = Mage::getModel('marketplace/vendorproduct')->getCollection()
							->addFieldToFilter('user_id',$userId)
							->addFieldToFilter('product_id',$productId);
				if($collection->count() == 0)
				{
						Mage::app()->getFrontController()->getResponse()->setRedirect($url);
				}
		}
	}
	public function getSellerPriceAttribute($order)
	{
		$isSeller = Mage::getModel('marketplace/seller')->isSeller();
		if(!is_null($isSeller))
		{	 
			$itemsCollection = Mage::getModel('sales/order_item')->getCollection()
						->addFieldToFilter('order_id',$order->getId())
						->addFieldToFilter('parent_item_id',array('null'=>true))
						->addFieldToFilter('seller_id',$isSeller);
				$itemsCollection ->getSelect()
                ->columns('SUM(seller_per_product_shipping) as seller_shipping_total')
                ->group('seller_id');
				
			return $itemsCollection->getFirstItem()->getSellerShippingTotal();
		}
	}
	public function getSellerPriceFormatAttribute($order,$strong = false, $separator = '<br/>')
	{
			$shippingAmount = $this->getSellerPriceAttribute($order);
		
			if ($order && $order->isCurrencyDifferent()) 
			{
			$res = '<strong>';
			$res.= $order->formatBasePrice($shippingAmount);
			$res.= '</strong>'.$separator;
			$res.= '['.$order->formatPrice($shippingAmount).']';
			}
			elseif ($order)
			{
				$res = $order->formatPrice($shippingAmount);
				if ($strong) {
					$res = '<strong>'.$res.'</strong>';
				}
			} else {
					$res = Mage::app()->getStore()->formatPrice($shippingAmount);
				if ($strong) {
					$res = '<strong>'.$res.'</strong>';
				}
			}
			return $res;
	}
}

?>
