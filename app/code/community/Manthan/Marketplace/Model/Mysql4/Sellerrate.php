<?php

class Manthan_Marketplace_Model_Mysql4_Sellerrate extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct() {
        $this->_init('marketplace/sellerrate', 'entity_id');
    }
}

?>
