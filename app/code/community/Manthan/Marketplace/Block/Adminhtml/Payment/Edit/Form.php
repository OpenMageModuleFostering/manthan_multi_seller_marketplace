<?php
class Manthan_Marketplace_Block_Adminhtml_Payment_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	
         protected function _prepareForm() {		
        $form = new Varien_Data_Form(
                        array(
                            'id' => 'edit_form',
                            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                            'method' => 'post'
                        )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
		$fieldset = $form->addFieldset('payment_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Payment Information')));
		
			$fieldset->addField('seller_amount', 'label', array(
					'label'=>Mage::helper('adminhtml')->__('Amount'),
					'after_element_html'=>"<b>" . Mage::registry('payment_data')->getAdminAmount() . "</b>"
			));
			
			$fieldset->addField('payment_note', 'textarea', array(
					'name'  => 'payment_note',
					'label' => Mage::helper('adminhtml')->__('Payment Note'),
					'title' => Mage::helper('adminhtml')->__('Payment Note'),
				)
			);
			
			$fieldset->addField('admin_amount', 'hidden', array(
					'name'  => 'admin_amount',
					'label' => Mage::helper('adminhtml')->__('Payment Note'),
					'title' => Mage::helper('adminhtml')->__('Payment Note'),
				)
			);
			
			$fieldset->addField('seller_id', 'hidden', array(
					'name'  => 'seller_id',
					'label' => Mage::helper('adminhtml')->__('Payment Note'),
					'title' => Mage::helper('adminhtml')->__('Payment Note'),
				)
			);
			
			$fieldset->addField('order_id', 'hidden', array(
					'name'  => 'order_id',
					'label' => Mage::helper('adminhtml')->__('Payment Note'),
					'title' => Mage::helper('adminhtml')->__('Payment Note'),
				)
			);
			
			$fieldset->addField('payment_date', 'date', array(
					'name'               => 'payment_date',
					'label'              => Mage::helper('adminhtml')->__('Payment Date'),
					'tabindex'           => 1,
					'image'              => $this->getSkinUrl('images/grid-cal.gif'),
					'format'             => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT) ,
					'value'              => date( Mage::app()->getLocale()->getDateStrFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
												  strtotime('now') )
			));
		if(Mage::registry('payment_data'))
		{
			$form->addValues(Mage::registry('payment_data')->getData());
		}	
        return parent::_prepareForm();
    }
}

?>
