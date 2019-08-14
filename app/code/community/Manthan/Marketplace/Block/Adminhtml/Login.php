<?php
class Manthan_Marketplace_Block_Adminhtml_Login extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_review';
		$this->_blockGroup = 'marketplace';
		$this->_headerText = Mage::helper('adminhtml')->__('Manage Reviews');
		parent::__construct();
		$this->_removeButton('add');
	}
	
}
?>
