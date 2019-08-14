<?php
class Manthan_Marketplace_Block_Adminhtml_Rating extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_rating';
		$this->_blockGroup = 'marketplace';
		$this->_headerText = Mage::helper('adminhtml')->__('Manage Rating');
		$this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Rating');
		
		parent::__construct();
	}
	
}
?>
