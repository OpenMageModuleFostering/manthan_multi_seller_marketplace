<?php
class Manthan_Marketplace_Block_Adminhtml_Payment_Renderer_Adminamount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {
         $adminAmount = $row->getData('admin_amount');
		$order = Mage::getModel('sales/order')->loadByIncrementId($row->getData('order_id'));
		if($adminAmount == 0)
		{
			return "<div style='border-radius:8px;color:#FFF;font-weight:bold;background:#038E03;'>{$order->formatPrice($adminAmount)}</div>";
		}
		else
		{
			return "<div style='border-radius:8px;color:#FFF;font-weight:bold;background:#E80000;'>{$order->formatPrice($adminAmount)}</div>";
		}
    }

   
}

?>
