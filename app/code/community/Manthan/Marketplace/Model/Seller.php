<?php
class Manthan_Marketplace_Model_Seller extends Mage_Core_Model_Abstract {

    const ROLE = 'SELLER';
    const ROLE_TYPE = 'G';
	
    protected function _construct() {
        $this->_init('marketplace/seller');
    }
	public function isSeller()
	{
		$userId = Mage::getSingleton('admin/session')->getUser()->getUserId();		
		$loggedInRole = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId();
		$roleId = Mage::getStoreConfig('marketplace/seller/role');
		
		$collection = Mage::getModel('marketplace/seller')->getCollection()
						->addFieldToFilter('user_id',$userId);				
		if($collection->count() > 0)				
			return $roleId == $loggedInRole ?  $collection->getFirstItem()->getId() :  false;
		else
			return null;
		
	}
	public function setRole($userModel)
	{ $role_id = Mage::getStoreConfig('marketplace/seller/role');
		$userModel->setRoleIds(array($role_id))
				->setRoleUserId($userModel->getId())
				->saveRelations();
		return $userModel;		
	}
	public function passUserData()
	{
		$userModel = Mage::getModel('admin/user');
		$userModel->setUsername(strtolower(Mage::app()->getRequest()->getPost('username')))
		->setFirstname(strtolower(Mage::app()->getRequest()->getPost('firstname')))
		->setLastname(strtolower(Mage::app()->getRequest()->getPost('lastname')))
		->setPassword(trim(Mage::app()->getRequest()->getPost('password')))
		->setEmail(strtolower(trim(Mage::app()->getRequest()->getPost('email'))))
		->setIsActive(0);
		if (Mage::app()->getRequest()->getPost('password', false)) {
			$userModel->setNewPassword(Mage::app()->getRequest()->getPost('password', false));
		}
		if (Mage::app()->getRequest()->getPost('confirmation', false)) {
			$userModel->setPasswordConfirmation(Mage::app()->getRequest()->getPost('confirmation', false));
		}
		return $userModel;
	}
	public function saveData($user_id)
	{ 
		$data = Mage::app()->getRequest()->getPost();
		$this->setShopName($data['shop_name']);
		$this->setCreatedDate(Mage::getModel('core/date')->timestamp(time()));
		$this->setShopDescription($data['shop_description']);
		$this->setCountry($data['country']);
		$this->setTelephone($data['telephone']);
		$this->setUserId($user_id);
		$this->setPostcode($data['postcode']);
		$this->setUrlPath(str_replace(" ","-",strtolower(trim($data['shop_url']))));
		$this->setTotalVendorEarn(0);
		$this->save();
	}
	public function validate()
    {
        $errors = false;
        if ($this->isShopUrlExist()) {
            $errors = Mage::helper('marketplace')->__('Seller with this shop url already exist.');
        }
        return $errors;
    }
	public function isShopUrlExist()
	{ 
		$shop_url = strtolower(trim(Mage::app()->getRequest()->getPost('shop_url')));
		
		$collection = Mage::getModel('marketplace/seller')->getCollection()
								->addFieldToFilter('url_path',str_replace(" ","-",$shop_url));
				if($collection->count() > 0){
					return true;
				}
		return false;		
	}
	public function sendSellerActivationMail($data,$userId)
	{ 
		$templateId = Mage::getStoreConfig('marketplace/vendor_activation_email/email_template');
		$identity = Mage::getStoreConfig('marketplace/vendor_activation_email/email_sender');
		$this->sendSellerEmail($templateId, $identity, $data);
	}
	public function sendEmailToSeller($templateId, $identity, $vars)
	{
		$senderEmail = Mage::getStoreConfig('trans_email/ident_' . $identity . '/email'); 
		$sender = array('name' => $_SERVER['HTTP_HOST'] , 'email' => $senderEmail);
		$recepientEmail = $vars['email'];
		$recepientName = $vars['firstname'] .  ' ' . $vars['lastname'] ;
		$storeId = Mage::app()->getStore()->getId();

		$translate  = Mage::getSingleton('core/translate');		
		Mage::getModel('core/email_template')->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
		$translate->setTranslateInline(true);		
	}
	public function sendSellerEmail($templateId, $identity, $vars)
	{	
		$senderName = Mage::getStoreConfig('trans_email/ident_' . $identity . '/name'); 
		
		$senderEmail = Mage::getStoreConfig('trans_email/ident_' . $identity . '/email'); 
		$sender = array('name' => $_SERVER['HTTP_HOST'] , 'email' => $senderEmail);
		$recepientEmail = $vars['email'];
		$recepientName = $vars['firstname'] . ' ' . $vars['lastname'];
		$storeId = Mage::app()->getStore()->getId();

		$translate  = Mage::getSingleton('core/translate');		
		Mage::getModel('core/email_template')->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

		$translate->setTranslateInline(true);		
	}
	public function sendSellerRegistrationConfirmationEmailToSeller($data,$sellerId)
	{
		$identity = Mage::getStoreConfig('marketplace/registration_confirmation_email/email_sender');
		$templateId = Mage::getStoreConfig('marketplace/registration_confirmation_email/email_template');
		$this->sendEmailToSeller($templateId, $identity, $data);
	}
	public function sendSellerRegistrationEmailToAdmin($data, $sellerId)
	{ 
		$templateId = Mage::getStoreConfig('marketplace/vendor_registration_email/email_template');
		$identity = Mage::getStoreConfig('marketplace/vendor_registration_email/email_receiver');		
		
		$country = Mage::getModel('directory/country')->loadByCode($data['country']);
		
		$data['country'] = $country->getName();
		$data['telephone'] = $data['telephone'];
		$data['seller_email_id'] = $data['email'];
		$data['seller_url'] = Mage::helper('adminhtml')->getUrl('admin_marketplace/adminhtml_account/edit', array('id'=> $sellerId));		
		
		$this->sendEmail($templateId, $identity, $data);
	}
	
	public function sendEmail($templateId, $identity, $vars)
	{	
		$recepientName = Mage::getStoreConfig('trans_email/ident_' . $identity . '/name'); 
		$recepientEmail = Mage::getStoreConfig('trans_email/ident_' . $identity . '/email'); 
		$sender = array('name' => $vars['firstname'] . ' ' . $vars['lastname'], 'email' => $vars['seller_email_id']);
		
		$storeId = Mage::app()->getStore()->getId();

		$translate  = Mage::getSingleton('core/translate');		
		Mage::getModel('core/email_template')->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);

		$translate->setTranslateInline(true);		
	}
	
	public function getSellerShippingPrice($order,$sellerId)
	{
			$itemsCollection = Mage::getModel('sales/order_item')->getCollection()
						->addFieldToFilter('order_id',$order->getId())
						->addFieldToFilter('parent_item_id',array('null'=>true))
						->addFieldToFilter('seller_id',$sellerId);
				$itemsCollection ->getSelect()
                ->columns('SUM(seller_per_product_shipping) as seller_shipping_total')
                ->group('seller_id');
				
			return $itemsCollection->getFirstItem()->getSellerShippingTotal();
	}
}

?>
