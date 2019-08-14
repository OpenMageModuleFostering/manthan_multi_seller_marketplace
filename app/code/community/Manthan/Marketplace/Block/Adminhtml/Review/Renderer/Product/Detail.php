<?php
class Manthan_Marketplace_Block_Adminhtml_Review_Renderer_Product_Detail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {
        $shipmentItemId = $row->getData('shipment_item_id');
		
		$shipment = Mage::getModel('sales/order_shipment_item')->load($shipmentItemId);
		$productId = $shipment->getProductId();							
		$product = Mage::getModel('catalog/product')->load($productId);
		
		//return "<a href=".$product->getProductUrl()." target='_blank'>".$product->getName().">";
		return '<a href="' . $this->getUrl('adminhtml/catalog_product/edit', array('id' => $product->getId())) . '" onclick="this.target=\'blank\'">' . $product->getName() . '</a>';
	}

   
}

?>
