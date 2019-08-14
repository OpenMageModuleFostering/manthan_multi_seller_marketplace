<?php
class Manthan_Marketplace_Block_Adminhtml_Payment_Renderer_Sellerpaidamount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {
        $sellerPaidAmount = $row->getData('seller_paid_amount');
		$order = Mage::getModel('sales/order')->loadByIncrementId($row->getData('order_id'));
		return $order->formatPrice($sellerPaidAmount); 
    }

   
}

?>
