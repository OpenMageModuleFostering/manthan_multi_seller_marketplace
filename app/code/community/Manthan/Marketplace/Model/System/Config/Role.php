<?php
class Manthan_Marketplace_Model_System_Config_Role
{
	protected function _construct() 
	{
		$this->_init('marketplace/system_config_role');	
	}
    
	public function toOptionArray()
    {
		$roleArray = array();
		$roleCollection = Mage::getModel('admin/roles')->getCollection();
		
		foreach($roleCollection as $role)
			$roleArray[] = array('value' => $role->getId(), 'label' => $role->getRoleName());
			
        return $roleArray;
    }    
}
?>
