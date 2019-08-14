<?php
require_once 'Mage/Adminhtml/controllers/System/AccountController.php';

class Manthan_Marketplace_Adminhtml_Admin_System_AccountController extends Mage_Adminhtml_System_AccountController
{
    public function saveAction() 
    {
       $userId = Mage::getSingleton('admin/session')->getUser()->getId();
        $pwd    = null;

        $user = Mage::getModel("admin/user")->load($userId);

        $user->setId($userId)
            ->setUsername($this->getRequest()->getParam('username', false))
            ->setFirstname($this->getRequest()->getParam('firstname', false))
            ->setLastname($this->getRequest()->getParam('lastname', false))
            ->setEmail(strtolower($this->getRequest()->getParam('email', false)));
        if ( $this->getRequest()->getParam('new_password', false) ) {
            $user->setNewPassword($this->getRequest()->getParam('new_password', false));
        }

        if ($this->getRequest()->getParam('password_confirmation', false)) {
            $user->setPasswordConfirmation($this->getRequest()->getParam('password_confirmation', false));
        }

        $result = $user->validate();
        if (is_array($result)) {
            foreach($result as $error) {
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
            $this->getResponse()->setRedirect($this->getUrl("*/*/"));
            return;
        }

        try {
            $user->save();
			
			$roleId = Mage::getStoreConfig('marketplace/seller/role');
			$currentRoleId = Mage::getSingleton('admin/session')->getUser()->getRole()->getRoleId();
			
			if($roleId == $currentRoleId)
			{
				if ($data = $this->getRequest()->getPost()) 
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
					
						$seller = Mage::getModel('marketplace/seller')->load($this->getRequest()->getParam('seller_id'));	
					
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
						$seller->setShopName($this->getRequest()->getParam('shop_name'))
							->setShopDescription($this->getRequest()->getParam('shop_description'))
							->setCountry($this->getRequest()->getParam('country'))
							->setpostcode($this->getRequest()->getParam('postcode'))
							->setTelephone($this->getRequest()->getParam('telephone'))
							->setUserId($userId)
							->setAdminCommissionByPercentage($this->getRequest()->getParam('admin_commission_by_percentage'))
							->save();
						}
					}
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('The account has been saved.'));
        }
        catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('An error occurred while saving account.'));
        }
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

}
?>