<?php

class Manthan_Marketplace_Model_Mysql4_Review extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct() {
        $this->_init('marketplace/review', 'entity_id');
    }
}

?>
