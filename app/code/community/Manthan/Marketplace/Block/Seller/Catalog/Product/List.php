<?php
class Manthan_Marketplace_Block_Seller_Catalog_Product_List extends Mage_Catalog_Block_Product_List {
	 protected function _getProductCollection()
    {
		$userCollection = Mage::getModel('admin/user')->getCollection()
					->addFieldToFilter('is_active',0);
		$productList = null;
		if($userCollection->count() > 0)
		{	
			$inActiveUserList = $userCollection->getColumnValues('user_id');
		
		$userCollection = Mage::getModel('marketplace/vendorproduct')->getCollection()
					->addFieldToFilter('user_id',array('in'=>$inActiveUserList));
			if($userCollection->count() > 0)
			{
				$productList = $userCollection->getColumnValues('product_id');
			}	
		}			
        parent::_getProductCollection();
		
		$this->_productCollection->addAttributeToSelect('*')
		->addAttributeToFilter('entity_id',array('nin'=>$productList))
		->addAttributeToFilter('product_status',Manthan_Marketplace_Model_Catalog_Product_Attribute_Status::APPROVED);
		return $this->_productCollection;
    }
	
}

?>
