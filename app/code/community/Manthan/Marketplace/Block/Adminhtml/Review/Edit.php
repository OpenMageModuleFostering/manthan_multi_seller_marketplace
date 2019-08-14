<?php

class Manthan_Marketplace_Block_Adminhtml_Review_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	 public function __construct(){
        parent::__construct();
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'marketplace';
        $this->_controller = 'adminhtml_review';
        $this->_mode = 'edit';

        $this->_updateButton('save', 'label', Mage::helper('adminhtml')->__('Save'));
    }
    public function getHeaderText()
    {
        if( Mage::registry('review_data')&&Mage::registry('review_data')->getId())
         {
              return 'Edit '.$this->htmlEscape(
              Mage::registry('review_data')->getSubject()).'<br />';
         }
         else
         {
             return 'Add a Review';
         }
    }
}

?>
