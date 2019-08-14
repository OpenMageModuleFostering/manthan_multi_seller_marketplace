<?php
require_once 'Mage/Adminhtml/controllers/IndexController.php';
class Manthan_Marketplace_Adminhtml_Admin_IndexController extends Mage_Adminhtml_IndexController
{
   
    public function indexAction()
    { 
        $session = Mage::getSingleton('admin/session');
        $url = $session->getUser()->getStartupPageUrl();
        if ($session->isFirstPageAfterLogin()) {
            // retain the "first page after login" value in session (before redirect)
            $session->setIsFirstPageAfterLogin(true);
        }
		if(Mage::getModel('marketplace/seller')->isSeller())
			$url= 'admin_marketplace/adminhtml_dashboard/index';
        $this->_redirect($url);
    }
}
