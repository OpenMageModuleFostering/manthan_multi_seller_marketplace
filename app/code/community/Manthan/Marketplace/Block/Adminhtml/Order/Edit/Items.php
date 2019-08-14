<?php  
class Manthan_Marketplace_Block_Adminhtml_Order_Edit_Items extends Mage_Adminhtml_Block_Template {

	public function __construct()
	{
		$this->setTemplate('marketplace/sales/order/view/items.phtml');
		 parent::__construct();
	}
	public function getItemHtml($_item)
	{
		$items = Mage::app()->getLayout()->createBlock('marketplace/adminhtml_order_edit_items_renderer_default')
				->setItem($_item)
				->setAdminComission($this->getAdminCommission($_item))
				->setSellerItemAmount($this->getSellerItemAmount($_item))
				->toHtml();
		return $items;
	}

    public function getCurrentOrder() 
	{
        return Mage::registry('current_order');
    }
	
    public function getItemsCollection() 
	{
		$isSeller = Mage::getModel('marketplace/seller')->isSeller();
		if(!is_null($isSeller))
		{	 
		 return $itemCollection = Mage::getModel('sales/order_item')->getCollection()
						->addFieldToFilter('order_id',$this->getCurrentOrder()->getId())
						->addFieldToFilter('seller_id',$isSeller);
		}
		 
	   return null;
    }
    public function getAdminCommission($item) {
		   $commission_amount = 0;  
            if (!is_null($item)) 
			{
                 $commission = $item->getAdminOrderCommission();
				 $rowTotal = $item->getPriceInclTax() * $item->getQtyOrdered();
				$commission_amount = ($rowTotal * $commission) / 100;
			}
            return $commission_amount;
    }

    public function getSellerItemAmount($item) 
	{	
		if(!$item)
			return 0;
                $commission = $item->getAdminOrderCommission();
                $total_price = ($item->getPriceInclTax() * $item->getQtyOrdered());
                $sellerItemAmount = $total_price - $this->getAdminCommission($item);
				
		return round($sellerItemAmount,2);
         
    }
}

?>
