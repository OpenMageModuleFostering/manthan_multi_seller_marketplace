<?php
class Manthan_Marketplace_Adminhtml_AccountController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('manthan');
        return $this;
    }
    public function indexAction() 
	{ 
        $this->_initAction();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Sellers/Marketplace'));
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_seller_account'));
        $this->renderLayout();
    }
	public function gridAction()
	{
		$this->getResponse()->setBody(
			Mage::app()->getLayout()->createBlock('marketplace/adminhtml_seller_account_product_grid')->toHtml()
		);
	}
	 public function editAction()
    {  
        $id = $this->getRequest()->getParam('id');
        $sellerObject = Mage::getModel('marketplace/seller')->load($id);
		$userObject = Mage::getModel('admin/user')->load($sellerObject->getUserId());
        if ($sellerObject->getId() || $id == 0) 
        {
            Mage::register('seller_data', $sellerObject);
            Mage::register('user_data', $userObject);
			$this->_initAction();
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_seller_account_edit'))
		   ->_addLeft($this->getLayout()->createBlock('marketplace/adminhtml_seller_account_edit_tabs'));
            $this->renderLayout();
        } 
        else 
        {
            Mage::getSingleton('adminhtml/session')
                    ->addError('Account does not exist');
            $this->_redirect('*/*/');
        }
    }
	
	public function newAction()
	{
	  $this->_forward('edit');
	}
	
	public function saveAction() {
		$sellerId = $this->getRequest()->getParam('id');
		$seller = Mage::getModel('marketplace/seller')->load($sellerId);
		
		$shop_url = strtolower(trim($this->getRequest()->getParam('shop_url')));
		
		$userId = $seller->getUserId();
		$user = Mage::getModel("admin/user")->load($userId);
		$beforeSaveActiveStatus = $user->getIsActive();
		if(!$sellerId)
		{ 
			$user = Mage::getModel("admin/user");
			$userId = null;
			$beforeSaveActiveStatus = null;
		}	
		
        $user->setId($userId)
            ->setUsername($this->getRequest()->getParam('username', false))
            ->setFirstname($this->getRequest()->getParam('firstname', false))
            ->setLastname($this->getRequest()->getParam('lastname', false))
            ->setEmail(strtolower($this->getRequest()->getParam('email', false)))
			->setIsActive($this->getRequest()->getParam('is_active', false));
        if ( $this->getRequest()->getParam('new_password', false) ) {
            $user->setNewPassword($this->getRequest()->getParam('new_password', false));
        }

        if ($this->getRequest()->getParam('password_confirmation', false)) {
            $user->setPasswordConfirmation($this->getRequest()->getParam('password_confirmation', false));
        }
		$data = $this->getRequest()->getPost();
		$resultSeller = Mage::getModel('marketplace/seller')->validate();
		if ($resultSeller) {
			Mage::getSingleton('adminhtml/session')->setSellerData($data);
			Mage::getSingleton('adminhtml/session')->addError($resultSeller);
			$this->getResponse()->setRedirect($this->getUrl("*/*/edit"));
			return $this;
		}
        $result = $user->validate();
        if (is_array($result)) {
            foreach($result as $error) {
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
			Mage::getSingleton('adminhtml/session')->setSellerData($data);
            $this->getResponse()->setRedirect($this->getUrl("*/*/edit"));
            return;
        }

		 try {
				$user->save();
				$userId = $user->getId();
				$user = Mage::getModel('marketplace/seller')->setRole($user);
				
				 $isActive = $this->getRequest()->getParam('is_active', false);
				
				if($isActive != $beforeSaveActiveStatus && $isActive == 1)
				{
					if(Mage::getStoreConfig('marketplace/vendor_activation_email/active_vendor_email'))
						Mage::getModel('marketplace/seller')->sendSellerActivationMail($this->getRequest()->getPost(),$userId);
				}
				if ($data) 
				{ 
					$dir_path = Mage::getBaseDir('media') . DS . 'marketplace' . DS . 'seller' . DS . 'images' . DS;
				
					if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') 
					{
						   try
						   {    
							$uploader = new Varien_File_Uploader('image');
							$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
							$uploader->setAllowRenameFiles(true);
							$uploader->setFilesDispersion(false);
								
							if(!is_dir($dir_path))
								mkdir($dir_path, 0777, true);
								
							$uploader->save($dir_path, $_FILES['image']['name']);
							$image = $_FILES['image']['name'];
							}
							catch (Exception $e) 
							{
								Mage::getSingleton('adminhtml/session')->addError($e->getMessage());		 
							}
					}
					
						
					
					if(isset($data['image']['delete']) && $data['image']['delete'] == 1 && empty($_FILES['image']['name']) )
					{
						unlink($dir_path . $data['delete_profile_image']);
						$seller->setImage(null);
					}
					else if(isset($data['image']['delete']) && $data['image']['delete'] == 1 && $_FILES['image']['name']!= '' )
					{	
						unlink($dir_path . $data['delete_profile_image']);
						$seller->setImage($image);
					}
					else if($_FILES['image']['name']!= '') {
						$seller->setImage($image);
					}	
					
						$seller->setShopName($data['shop_name']);
						$seller	->setShopDescription($data['shop_description']);
						$seller	->setCountry($data['country']);
						$seller	->setPostcode($data['postcode']);
						$seller	->setTelephone($data['telephone'],false);
						$seller	->setUserId($user->getUserId());
						
						if(isset($data['shop_url']))
							$seller	->setUrlPath($data['shop_url']);
						
						if(!$sellerId)	
						$seller	->setCreatedDate(Mage::getModel('core/date')->timestamp(time()));
						
						$seller->setAdminCommissionByPercentage($data['admin_commission_by_percentage']);
						$seller->save();
				}
					
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The account has been saved.'));
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
	   $this->getResponse()->setRedirect($this->getUrl("*/*/"));	
    }
}
?>
