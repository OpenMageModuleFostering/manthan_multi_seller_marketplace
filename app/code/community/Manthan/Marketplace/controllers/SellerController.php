<?php
class Manthan_Marketplace_SellerController extends Mage_Core_Controller_Front_Action
{
	public function createAction()
	{
		$this->loadLayout();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Seller Registration'));
		$this->renderLayout();
	}
	public function categoryAction()
	{
		
// Usually, you can get category tree from category helper
$helper = Mage::helper('catalog/category');
$nodes = $helper->getStoreCategories();
// return Varien_Data_Tree_Node_Collection
//        via Mage_Catalog_Model_Resource_Category
// However, this get method return active category only.
// Most of the samples are for collection of the category.
// This is for a tree node not a normal collection.
// We can get all category tree using the code below:
$parent = Mage::app()->getStore()->getRootCategoryId();
$tree = Mage::getResourceModel('catalog/category_tree');
$nodes = $tree->loadNode($parent)
  ->loadChildren($recursionLevel)
  ->getChildren();
$tree->addCollectionData(null, false, $parent, true, false);
// Now, you can use $nodes as category tree
foreach($nodes as $category){
  echo $category->getName()."<br>";
}
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
