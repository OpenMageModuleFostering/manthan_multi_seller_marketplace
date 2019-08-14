<?php

class Manthan_Marketplace_Block_Adminhtml_Review_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	
         protected function _prepareForm() {
		
		$isSeller = Mage::getModel('marketplace/seller')->isSeller();
		
        $form = new Varien_Data_Form(
                        array(
                            'id' => 'edit_form',
                            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                            'method' => 'post'
                        )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
		$fieldset = $form->addFieldset('review_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Review Information')));
		
			$fieldset->addField('subject', 'text', array(
					'name'  => 'subject',
					'label' => Mage::helper('adminhtml')->__('Subject'),
					'title' => Mage::helper('adminhtml')->__('Subject'),
					'required' => true,
					'disabled' => true,
					'readonly' => true
				)
			);
			
			$fieldset->addField('description', 'textarea', array(
					'name'  => 'description',
					'label' => Mage::helper('adminhtml')->__('Description'),
					'title' => Mage::helper('adminhtml')->__('Description'),
					'required' => true,
					'disabled' => true,
					'readonly' => true
				)
			);
			
			$fieldset->addField('detailed_rating', 'note', array(
            'label'     => Mage::helper('review')->__('Detailed Rating'),
            'required'  => true,
            'text'      => '<div id="rating_detail">'
                           . $this->getLayout()->createBlock('marketplace/adminhtml_review_rating_detailed')->setData('shipment_item_id', Mage::registry('review_data')->getShipmentItemId())->toHtml()
                           . '</div>',
			));
			
		if(!$isSeller)
		{	
			$fieldset->addField('status', 'select', array(
				'label'     => Mage::helper('adminhtml')->__('Status'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'status',
				'values' => array('0' => 'Pending','1' => 'Approved','2' => 'Not Approved')
				));
		}		
		
        if (Mage::registry('review_data')) {
            $form->addValues(Mage::registry('review_data')->getData());
        }
        return parent::_prepareForm();
    }
}

?>
