<?php

class Manthan_Marketplace_Block_Adminhtml_Rating_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() 
	{
        parent::__construct();
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'marketplace';
        $this->_controller = 'adminhtml_rating';
        $this->_updateButton('save', 'label', 'Save');
		$this->_removeButton('reset');
    }

   public function getHeaderText()
    {
        if( Mage::registry('rating_data')&&Mage::registry('rating_data')->getId())
         {
              return 'Edit '.$this->htmlEscape(
              Mage::registry('rating_data')->getName()).'<br />';
         }
         else
         {
             return 'Add a Rating';
         }
    }

}

?>
