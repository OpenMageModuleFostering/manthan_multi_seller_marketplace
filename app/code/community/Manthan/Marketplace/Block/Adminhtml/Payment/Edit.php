<?php

class Manthan_Marketplace_Block_Adminhtml_Payment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	 public function __construct(){
        parent::__construct();
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'marketplace';
        $this->_controller = 'adminhtml_payment';
        $this->_mode = 'edit';

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Pay Offline'));
		$this->_removeButton('delete');
    }
    public function getHeaderText()
    {
        if( Mage::registry('payment_data')&&Mage::registry('payment_data')->getId())
         {
              return 'Edit Data<br />';
         }
    }
}

?>
