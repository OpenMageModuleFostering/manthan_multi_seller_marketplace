<?php

class Manthan_Marketplace_Model_Mysql4_Seller_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    public function _construct() {
        $this->_init('marketplace/seller');
    }
	public function addUserData($sellerId = null)
	{
		if(is_null($sellerId))
		{
			$user= Mage::getSingleton('core/resource')->getTableName('admin_user'); 
			$this->getSelect()
				->join(array('user' => $user),'user.user_id =`main_table`.user_id',
				array('main_table.*','user.*'));
		}
		else
		{
			$user = Mage::getSingleton('core/resource')->getTableName('admin_user'); 
			$seller = Mage::getModel('marketplace/seller')->getCollection();
			$this->getSelect()
			->join(array('user' => $user),'user.user_id =`main_table`.user_id',array('main_table.*','user.*'))
			->where('entity_id = ?',$sellerId);
		}
		return $this;
	}
}

?>
