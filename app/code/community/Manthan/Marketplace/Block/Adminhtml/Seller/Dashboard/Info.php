<?php
class Manthan_Marketplace_Block_Adminhtml_Seller_Dashboard_Info extends Mage_Adminhtml_Block_Template {

    public function __construct()
	{
     $this->setTemplate('marketplace/seller/dashboard/info.phtml');   
    }	
	public function lifetimeSales()
	{
		$sellerTotalEarn = 0;
		$sellerId = Mage::getModel('marketplace/seller')->isSeller();
		if($sellerId)
		{
			$seller = Mage::getModel('marketplace/seller')->load($sellerId);
			$sellerTotalEarn = round($seller->getTotalVendorEarn(),2);
		}
		return $sellerTotalEarn;
	}
	public function getSellerPendingAmount()
	{	
		$sellerId = Mage::getModel('marketplace/seller')->isSeller(); 
		if($sellerId)
		{
			$collection = Mage::getModel('marketplace/payment')->getCollection()
				->addFieldToFilter('seller_id',$sellerId);
			$collection->getSelect()
					->columns('SUM(admin_amount) as pending_amount')
					->group('seller_id');
				$sellerArray = $collection->getData();
				if(empty($sellerArray))
				   return round(0,2);	
			return round($sellerArray[0]['pending_amount'],2);		
		}
		return 0;
	}
	public function weekSales()
	{ 
		
		$weekAgoDate = date('Y-m-d',strtotime(' -6 day'));
		$weekSale = Mage::helper('marketplace')->getCurrentSellerSales($weekAgoDate);
		return round($weekSale,2);
	}
	public function monthSales()
	{
		$monthAgoDate = date('Y-m-d', strtotime(' -30 day'));
		$monthSale = Mage::helper('marketplace')->getCurrentSellerSales($monthAgoDate);
		return round($monthSale,2);
	}
}
?>