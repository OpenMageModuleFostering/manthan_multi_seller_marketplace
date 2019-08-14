<?php
class Manthan_Marketplace_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard
{
	public function match(Zend_Controller_Request_Http $request)
    {  
		$identifier = trim(strtolower($request->getPathInfo()), '/');
		$path = explode('/',$identifier);
		if (count($path) > 2 && $path[0] != 'seller')
			return false; 
		
		$condition = new Varien_Object(array(
		'identifier' => $identifier,
		'continue' => true
		));
		
		$identifier = $condition->getIdentifier();

		if ($condition->getRedirectUrl()) 
		{
			Mage::app()->getFrontController()->getResponse()
			->setRedirect($condition->getRedirectUrl())
			->sendResponse();
			$request->setDispatched(true);
			return true;
		}

		if (!$condition->getContinue()) 
		{
		return false;
		}

		$sellerCollection = Mage::getModel('marketplace/seller')->getCollection()
						->addFieldToSelect(array('url_path','entity_id'))
						->addFieldToFilter('url_path',$path[1]);				
	
		$sellerId = $sellerCollection->getFirstItem()->getId();
		if(is_null($sellerId)){
			$url = Mage::helper('core/url')->getHomeUrl();
			echo Mage::helper('marketplace')->redirectUrl($url);
			exit;
		}
		$request->setModuleName('marketplace')
		->setControllerName('seller')
		->setActionName('view')
		->setParam('id', $sellerId);

		$request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,$identifier);
		return true;
    }
}

?>
