<?php
class Manthan_Marketplace_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() 
	{
		$this->_title($this->__('Dashboard'));
        $this->loadLayout()->_setActiveMenu('seller_dashboard');
        return $this;
    }
    public function indexAction() 
	{ 
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_seller_dashboard_info'));
        $this->renderLayout();
    }
	protected function _isAllowed()
	{
		 return Mage::getSingleton('admin/session')->isAllowed('admin/seller');
	}
}
?>
