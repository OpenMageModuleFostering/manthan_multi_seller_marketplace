<?php
class Manthan_Marketplace_Block_Adminhtml_Rating_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

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
		$fieldset = $form->addFieldset('rating_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Rating Information')));
		
		
			$fieldset->addField('name', 'text', array(
					'name'  => 'name',
					'label' => Mage::helper('adminhtml')->__('Name'),
					'title' => Mage::helper('adminhtml')->__('Name'),
					'required' => true,
					'style'		=> 'width:150px',
				)
			);
			
			$fieldset->addField('status', 'select', array(
				'label'     => Mage::helper('adminhtml')->__('Select'),
				'class'     => 'required-entry',
				'required'  => true,
				'name'      => 'status',
				'value'		=> '1',
				'style'		=> 'width:150px',
				'values' => array('1' => 'Enable','0' => 'Disable')
				));
				
		
        if (Mage::registry('rating_data')) {
            $form->addValues(Mage::registry('rating_data')->getData());
        }
        return parent::_prepareForm();
    }
}

?>
