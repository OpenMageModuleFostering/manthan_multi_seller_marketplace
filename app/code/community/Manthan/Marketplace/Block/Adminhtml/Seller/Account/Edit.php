<?php
class Manthan_Marketplace_Block_Adminhtml_Seller_Account_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() 
	{
        parent::__construct();
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'marketplace';
        $this->_controller = 'adminhtml_seller_account';
        $this->_updateButton('save', 'label', 'Save Seller');
		$this->_removeButton('delete');
		$this->_removeButton('reset');
    }

    public function getHeaderText()
    {
        if( Mage::registry('user_data')&&Mage::registry('user_data')->getId())
         {
              return 'Edit '.$this->htmlEscape(
              " ' " . Mage::registry('user_data')->getFirstname() . " " . Mage::registry('user_data')->getLastname())." ' " .' Seller <br />';
         }
    }
	protected function _prepareLayout()
    { 
		parent::_prepareLayout();
        if ($this->_blockGroup && $this->_controller && $this->_mode) 
		{
			$orderDetailsBlock = $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode."_form" );
			
			$this->setChild('form',$orderDetailsBlock);
			
        }
    }

}

?>
