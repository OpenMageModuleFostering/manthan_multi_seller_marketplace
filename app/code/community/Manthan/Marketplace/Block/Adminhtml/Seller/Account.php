<?php
class Manthan_Marketplace_Block_Adminhtml_Seller_Account extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{		
		$this->_controller = 'adminhtml_seller_account';
		$this->_blockGroup = 'marketplace';
		$this->_headerText = Mage::helper('adminhtml')->__('Manage Sellers');
		$this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Vendor');
		parent::__construct();
		
	}
	
}
?>
