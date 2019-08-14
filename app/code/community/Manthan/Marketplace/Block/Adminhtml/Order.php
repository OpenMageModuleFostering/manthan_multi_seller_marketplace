<?php
class Manthan_Marketplace_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	 public function __construct(){
        $this->_controller = 'adminhtml_order';
        $this->_blockGroup = 'marketplace';
        $this->_headerText = Mage::helper('adminhtml')->__('Manage Orders');
       parent::__construct();
		$this->_removeButton('add');
    }
}
?>
