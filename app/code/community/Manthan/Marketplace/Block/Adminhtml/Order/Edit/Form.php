<?php
class Manthan_Marketplace_Block_Adminhtml_Order_Edit_Form extends Mage_Adminhtml_Block_Template{
	
	public function __construct()
	{
		$this->setTemplate('marketplace/sales/order/form.phtml');
		 parent::__construct();
	}
	
    public function getOrder() 
	{
        return Mage::registry('current_order');
    }
	
    public function getPaymentInfo() {
        return $this->getOrder()->getPayment()->getMethodInstance()->getTitle();
    }
	
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>') 
	{
		if($this->getPriceDataObject()->getShippingMethod() == "marketplaceproductshipping_marketplaceproductshipping")
		{
			return  Mage::helper('marketplace')->getSellerPriceFormatAttribute($this->getPriceDataObject(),$strong = false, $separator = '<br/>');
		}	
        return Mage::helper('adminhtml/sales')->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }
	
    public function getPriceDataObject() 
	{
        $obj = null;
        if (is_null($obj)) 
		{
            return $this->getOrder();
        }
        return $obj;
    }

    public function displayShippingPriceInclTax($order) 
	{
        $shipping = $order->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $order->getBaseShippingInclTax();
        } else {
            $shipping = $order->getShippingAmount() + $order->getShippingTaxAmount();
            $baseShipping = $order->getBaseShippingAmount() + $order->getBaseShippingTaxAmount();
        }
        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }

    public function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>') 
	{
        return Mage::helper('adminhtml/sales')->displayPrices($this->getPriceDataObject(), $basePrice, $price, $strong, $separator);
    }
	
    public function getBackUrl() 
	{
        return Mage::helper('adminhtml')->getUrl('*/*/index');
    } 
	
}

?>
