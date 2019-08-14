<?php
class Manthan_Marketplace_Block_Adminhtml_Catalog_Product_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {
        return $this->_getStatus($row);
    }
    protected function _getStatus(Varien_Object $row)
    {
		$productObject = Mage::getModel('catalog/product')->load($row->getId());
		if($productObject->getProductStatus() == 1)
			return "<div style='border-radius:8px;color:#FFF;font-weight:bold;background:#E47449;'>Pending</div>";
		else if($productObject->getProductStatus() == 2)
			return "<div style='border-radius:8px;color:#FFF;font-weight:bold;background:#7EE47E;'>Approved</div>";
		else if($productObject->getProductStatus() == 3)
			return "<div style='border-radius:8px;color:#FFF;font-weight:bold;background:#E80000;'>Not Approved</div>";
    }
}
?>
