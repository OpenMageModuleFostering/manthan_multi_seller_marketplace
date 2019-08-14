<?php

class Manthan_Marketplace_Model_Mysql4_Review_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {

    public function _construct() {
        $this->_init('marketplace/review');
    }
}

?>
