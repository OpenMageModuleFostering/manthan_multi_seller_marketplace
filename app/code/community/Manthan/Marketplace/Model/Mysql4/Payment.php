<?php
class Manthan_Marketplace_Model_Mysql4_Payment extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct() {
        $this->_init('marketplace/payment', 'entity_id');
    }
}

?>
