<?php
class Manthan_Marketplace_Model_Catalog_Product_Attribute_Status extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {
	
	const PENDING = 1;
	const APPROVED = 2;
	const DISAPPROVED = 3;
	
	public function getAllOptions()
    {	
        if (is_null($this->_options)) {
            $this->_options = array(
				
				array(
                    'label' => Mage::helper('marketplace')->__('Pending'),
                    'value' =>  self::PENDING
                ),
				array(
                    'label' => Mage::helper('marketplace')->__('Approved'),
                    'value' =>  self::APPROVED
                ),
                array(
                    'label' => Mage::helper('marketplace')->__('Not Approved'),
                    'value' =>  self::DISAPPROVED
                ),
            );
        }
        return $this->_options;
    }
 
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
 

}

?>
