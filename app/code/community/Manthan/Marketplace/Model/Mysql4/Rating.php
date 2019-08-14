<?php

class Manthan_Marketplace_Model_Mysql4_Rating extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct() {
        $this->_init('marketplace/rating', 'entity_id');
    }
}

?>
