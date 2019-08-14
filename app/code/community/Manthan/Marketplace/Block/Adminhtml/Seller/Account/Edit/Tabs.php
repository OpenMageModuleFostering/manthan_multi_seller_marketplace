<?php
class Manthan_Marketplace_Block_Adminhtml_Seller_Account_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{

        /**
     * Initializa the tab system
     */
   public function __construct(){
        parent::__construct();
        $this->setId('seller_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Seller View'));
    }
	
	protected function _beforeToHtml(){
      
		 $this->addTab('profile_section', array(
            'label' => Mage::helper('adminhtml')->__('Account Settings'),
            'title' => Mage::helper('adminhtml')->__('Account Settings'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_seller_account_edit_tab_form')->toHtml(),
        ));
		 $this->addTab('product_section', array(
            'label' => Mage::helper('adminhtml')->__('Products'),
            'title' => Mage::helper('adminhtml')->__('Products'),
			'url'=>$this->getUrl('admin_marketplace/adminhtml_account/grid',array('_current'=>true)),
			'class' => 'ajax'
        ));
       return parent::_beforeToHtml();
    }

    /**
     * Generate the teab system to send tot he template.
     */
	 
   /* $this->addTab('product_section', array(
            'label' => Mage::helper('adminhtml')->__('Product'),
            'title' => Mage::helper('adminhtml')->__('Product'),
			'url'=>$this->getUrl('admin_marketplace/adminhtml_account/grid'),
        'class' => 'ajax'
        ));
        $this->addTab('profile_section', array(
            'label'  => Mage::helper('adminhtml')->__('Vendor Information'),
            'title'  => Mage::helper('adminhtml')->__('Vendor Information'),
          'content'=>$this->getLayout()->createBlock('marketplace/adminhtml/seller/account/edit/tab/invoice')
			

		  // 'url'=>$this->getUrl('admin_marketplace/adminhtml_account/form'),
        //'class' => 'ajax'
        )); public function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label' => Mage::helper('adminhtml')->__('Template information'),
            'title' => Mage::helper('adminhtml')->__('Template information'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_review_edit_tabs_general')->toHtml(),
        ));
        $this->addTab('main_section', array(
            'label' => Mage::helper('adminhtml')->__('Review'),
            'title' => Mage::helper('adminhtml')->__('Review'),
            'content' => $this->getLayout()->createBlock('marketplace/adminhtml_review_edit_tabs_form')->toHtml(),
        ));
       
        return parent::_beforeToHtml();
    }*/
}

?>
