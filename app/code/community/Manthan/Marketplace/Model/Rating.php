<?php
class Manthan_Marketplace_Model_Rating extends Mage_Core_Model_Abstract {
	
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
	
    protected function _construct() {
        $this->_init('marketplace/rating');
    }

}

?>
