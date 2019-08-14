<?php
class Manthan_Marketplace_Model_Review extends Mage_Core_Model_Abstract {

		const STATUS_PENDING = 0;
		const STATUS_APPROVED = 1;
   
   protected function _construct() {
        $this->_init('marketplace/review');
    }
}

?>
