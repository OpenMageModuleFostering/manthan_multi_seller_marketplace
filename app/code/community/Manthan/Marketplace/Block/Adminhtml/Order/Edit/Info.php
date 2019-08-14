<?php
class Manthan_Marketplace_Block_Adminhtml_Order_Edit_Info extends Mage_Adminhtml_Block_Template {

    public function __construct() {
        $this->setTemplate('marketplace/sales/order/view/info.phtml');
        parent::__construct();
    }

    public function getOrder() {
        return Mage::registry('current_order');
    }

    public function shouldDisplayCustomerIp() {
        return !Mage::getStoreConfigFlag('sales/general/hide_customer_ip', $this->getOrder()->getStoreId());
    }
	
    public function getOrderStoreName() {
        if ($this->getOrder()) {
            $storeId = $this->getOrder()->getStoreId();
            if (is_null($storeId)) {
                $deleted = Mage::helper('adminhtml')->__(' [deleted]');
                return nl2br($this->getOrder()->getStoreName()) . $deleted;
            }
            $store = Mage::app()->getStore($storeId);
            $name = array(
                $store->getWebsite()->getName(),
                $store->getGroup()->getName(),
                $store->getName()
            );
            return implode('<br/>', $name);
        }
        return null;
    }
	
	public function getCustomerGroupName() {
        if ($this->getOrder()) {
            return Mage::getModel('customer/group')->load((int) $this->getOrder()->getCustomerGroupId())->getCode();
        }
        return null;
    }
	
	public function getViewUrl($orderId){
		return $this->getUrl('*/*/view', array('order_id'=>$orderId));	
    }
}

?>
