<?php 
class Manthan_Marketplace_Block_Adminhtml_Seller_Account_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

        protected function _prepareForm() {
        $form = new Varien_Data_Form(
                        array(
                            'id' => 'edit_form',
                            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                            'method' => 'post',
							'enctype' => 'multipart/form-data'
                        )
        );
        $this->setForm($form);
		
	    $userset = $form->addFieldset('seller_account_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Account Information')));
		$sellerModel = Mage::registry('seller_data');	
		$userModel = Mage::registry('user_data');
		
			 $userset->addField('username', 'text', array(
					'name'  => 'username',
					'label' => Mage::helper('adminhtml')->__('User Name'),
					'title' => Mage::helper('adminhtml')->__('User Name'),
					'required' => true,
					'value' => $userModel->getUsername()
				)
			);
			
			$userset->addField('firstname', 'text', array(
					'name'  => 'firstname',
					'label' => Mage::helper('adminhtml')->__('First Name'),
					'title' => Mage::helper('adminhtml')->__('First Name'),
					'required' => true,
					'value' =>  $userModel->getFirstname()
				)
			);
			
			$userset->addField('lastname', 'text', array(
					'name'  => 'lastname',
					'label' => Mage::helper('adminhtml')->__('Last Name'),
					'title' => Mage::helper('adminhtml')->__('Last Name'),
					'required' => true,
					'value' =>  $userModel->getLastname()
				));
			
			$userset->addField('email', 'text', array(
					'name'  => 'email',
					'label' => Mage::helper('adminhtml')->__('Email'),
					'title' => Mage::helper('adminhtml')->__('Email'),
					'required' => true,
					'value' => $userModel->getEmail()
				));
				
			$userset->addField('new_password', 'password', array(
					'label'     => Mage::helper('adminhtml')->__('New Password'),
					'title' => Mage::helper('adminhtml')->__('New Password'),
					'name'      => 'new_password',
					'required' => $userModel->getData()  ? false : true,
					'value' => ''
				));
				
			$userset->addField('password_confirmation', 'password', array(
					'label'     => Mage::helper('adminhtml')->__('Confirm Password'),
					'name'      => 'password_confirmation',
					'required' => $userModel->getData() ? false : true,
					'value'  => ''
				));
				
			$userset->addField('is_active', 'select', array(
					'label'     => Mage::helper('adminhtml')->__('This Account is'),
					'name'      => 'is_active',
					'style'		=>'width:80px',
					'value'  => $userModel->getIsActive(),
					'values' => array(0=>'Inacitve',1=>'Acitve')
				));
				
	
       $fieldset = $form->addFieldset('shop_fieldset', array('legend'=>Mage::helper('adminhtml')->__('Shop Information')));
		
		
			$fieldset->addField('shop_name', 'text', array(
					'name'  => 'shop_name',
					'label' => Mage::helper('adminhtml')->__('Shop Name'),
					'title' => Mage::helper('adminhtml')->__('Shop Name'),
					'required' => true,
					'value' => $sellerModel->getShopName(),
				)
			);
			if($userModel->getData())
			{
				$fieldset->addField('link', 'link', array(
				  'label'     => Mage::helper('adminhtml')->__('Link'),
				  'style'   => "text-decoration:none",
				  'href' => Mage::helper('marketplace')->getShopUrl($sellerModel->getUrlPath()),
				  'value'  => $sellerModel->getUrlPath(),
				  'target' => '_blank'
				));
			}
			else
			{
				$fieldset->addField('shop_url', 'text', array(
					'label'     => Mage::helper('adminhtml')->__('Shop Url'),
					'title' => Mage::helper('adminhtml')->__('Shop Url'),
					'required'  => true,
					'name'      => 'shop_url',
					'value' => ''
				));
			}
			$fieldset->addField('shop_description', 'textarea', array(
					'label'     => Mage::helper('adminhtml')->__('Description'),
					'title' => Mage::helper('adminhtml')->__('Description'),
					'required'  => true,
					'name'      => 'shop_description',
					'value' => $sellerModel->getShopDescription()
				));
				
			$fieldset->addField('telephone', 'text', array(
					'label'     => Mage::helper('adminhtml')->__('Telephone'),
					'title' => Mage::helper('adminhtml')->__('Telephone'),
					'required'  => true,
					'name'      => 'telephone',
					'value' => $sellerModel->getTelephone()
				));
				
			$fieldset->addField('country', 'select', array(
					'label'     => Mage::helper('adminhtml')->__('Country'),
					'required'  => true,
					'name'      => 'country',
					'value'  => $sellerModel->getCountry(),
					'options' => $this->getAllCountry()
				));
				
			$fieldset->addField('postcode', 'text', array(
					'label'     => Mage::helper('adminhtml')->__('Postcode'),
					'title' => Mage::helper('adminhtml')->__('Postcode'),
					'required'  => true,
					'name'      => 'postcode',
					'value' => $sellerModel->getPostcode()
				));

			$fieldset->addField('seller_id', 'hidden', array(
					'name' => 'seller_id',
					'value' =>$sellerModel->getId()
				));
			
			$fieldset->addField('image', 'image', array(
					'name' => 'image',
					'label' => Mage::helper('adminhtml')->__('Profile Image'),
					'value' =>$this->getProfileImage($sellerModel->getImage())
				));
			
			$fieldset->addField('delete_profile_image', 'hidden', array(
					'name' => 'delete_profile_image',
					'value' =>$sellerModel->getImage()
				));
				
			 $fieldset->addField('admin_commission_by_percentage', 'text', array(
					'name' => 'admin_commission_by_percentage',
					'label' => Mage::helper('adminhtml')->__('Commission (in %)'),
					'title' => Mage::helper('adminhtml')->__('Commission (in %)'),
					'required' => true,
					'value' => $sellerModel->getAdminCommissionByPercentage(),
					)
				);				
			$fieldset->addField('admin_total_earn', 'label', array(
					'label' => Mage::helper('adminhtml')->__('Total Admin Earn From Your Sale'),
					'value' => Mage::helper('core')->currency($sellerModel->getAdminTotalEarn(), true, false)
				));
			$fieldset->addField('total_vendor_earn', 'label', array(
					'label' => Mage::helper('adminhtml')->__('Total Vendor Earn'),
					'value' =>Mage::helper('core')->currency($sellerModel->getTotalVendorEarn(), true, false)
				));
				
		if($data = Mage::getSingleton('adminhtml/session')->getSellerData())
		{ 
			$form->setValues($data);
			Mage::getSingleton('adminhtml/session')->setSellerData(null);
		}
    return parent::_prepareForm(); 
    }
public function getAllCountry()
	{	
			$countryList = Mage::getResourceModel('directory/country_collection')
					->loadData()
					->toOptionArray(false);

		foreach($countryList as $country)		
			$countryArray[$country['value']] = $country['label'];		
		
		return $countryArray;
	}
	 
	 public function getProfileImage($image) 
	 {
        if ($image == '')
            return '';
        $dir_path = Mage::getBaseUrl('media').'marketplace/seller/images/';
        return $dir_path . $image;
	 }
}

?>
