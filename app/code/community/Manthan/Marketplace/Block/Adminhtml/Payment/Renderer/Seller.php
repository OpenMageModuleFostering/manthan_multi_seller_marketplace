<?php
class Manthan_Marketplace_Block_Adminhtml_Payment_Renderer_Seller extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {
        return $this->_getSeller($row);
    } 
    protected function _getSeller(Varien_Object $row)
    {
		$seller = Mage::getModel('marketplace/seller')->load($row->getSellerId());
		$sellerId = $seller->getId();
		$url = $this->getUrl('admin_marketplace/adminhtml_account/edit',array('id'=>$sellerId));
		$user = Mage::getModel('admin/user')->load($seller->getUserId());
		if(Mage::getModel('marketplace/seller')->isSeller())
			return "<span style='color:#ea7601;'>" . $user->getUsername() . '</span>';
		else
			return "<a href={$url} title={$url} target='_blank' style='color:#ea7601;'>" . $user->getUsername() . '</a>';
    }
	
}
?>
