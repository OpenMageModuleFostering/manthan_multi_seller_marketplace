<?php
class Manthan_Marketplace_SellerController extends Mage_Core_Controller_Front_Action
{
	public function createAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Seller Registration'));
		$this->renderLayout();
	}
	public function saveAction()
	{ 
		$data = $this->getRequest()->getPost();
		$seller = Mage::getModel('marketplace/seller');
		$userModel = Mage::getModel('admin/user');
		$userModel = $seller->passUserData();
		$resultSeller = Mage::getModel('marketplace/seller')->validate();
		if ($resultSeller) {
			Mage::getSingleton('core/session')->setSellerData($data);
			Mage::getSingleton('core/session')->addError($resultSeller);
			$this->_redirect('*/*/create', array('_current' => true));
			return $this;
		}
		$result = $userModel->validate();
	
		if (is_array($result)) {
			Mage::getSingleton('core/session')->setSellerData($data);
			foreach ($result as $message) {
				Mage::getSingleton('core/session')->addError($message);
			}
			$this->_redirect('*/*/create', array('_current' => true));
			return $this;
		}	
		try { 
			$userModel->save();
			$userModel = $seller->setRole($userModel);
			
			$seller->saveData($userModel->getId());
			if(Mage::getStoreConfig('marketplace/vendor_registration_email/enable_registration_email'))
				$seller->sendSellerRegistrationEmailToAdmin($data, $seller->getId());
			if(Mage::getStoreConfig('marketplace/registration_confirmation_email/enabled'))
				$seller->sendSellerRegistrationConfirmationEmailToSeller($data,$seller->getId());
			
			Mage::getSingleton('core/session')->addSuccess('Thank you for seller registration with us, we will contact you soon.');
			Mage::getSingleton('core/session')->setSellerData(false);
			$this->_redirect('*/*/create');	
		}
		catch (Mage_Core_Exception $e) {
			Mage::getSingleton('core/session')->addError($e->getMessage());
			Mage::getSingleton('core/session')->setSellerData($data);
			$this->_redirect('*/*/create');
			return $this;
        }
	}
	
	public function viewAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Seller Page'));
		$this->renderLayout();
	}
}

?>
