<?php
class Manthan_Marketplace_Block_Adminhtml_Review_Renderer_Seller_Detail extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
    {
        $shipmentItemId = $row->getData('shipment_item_id');
		
		$seller = Mage::getModel('marketplace/sellerrate')->getCollection()
					->addFieldToFilter('shipment_item_id',$shipmentItemId);
		$sellerId = $seller->getFirstItem()->getSellerId();
		
		$seller = Mage::getModel('marketplace/seller')->load($sellerId);
		$userId = $seller->getUserId();	
		$user = Mage::getModel('admin/user')->load($userId);
		
		return $user->getFirstname()."&nbsp;".$user->getLastname();
		
		/*$seller->getSelect()
		->join(array('review' => $review),'review.shipment_item_id=`main_table`.shipment_item_id', array('shipment_item_id'))
		->where('review.status = ?',Manthan_Marketplace_Model_Review::STATUS_APPROVED);
		$shipmentIds = array_unique($seller->getColumnValues('shipment_item_id'));*/
    }

   
}

?>
