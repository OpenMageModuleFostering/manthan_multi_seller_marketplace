<?php
class Manthan_Marketplace_Model_Observer {
	
	public function addSellerInfo(Varien_Event_Observer $observer) 
	{
			$sellerInfo = Mage::helper('marketplace')->getSellerInfo($observer->getProduct()); 
				if($sellerInfo)
				{	
					$product = $observer->getProduct();
					$additionalOptions = array();
					
					$additionalOptions[] = array(
						'label'=> Mage::helper('core')->__('seller:'),
						'value' => $sellerInfo->getShopName(),
					);
					$observer->getProduct()->addCustomOption('additional_options', serialize($additionalOptions));
				}
		return $this;	
	}
	
	public function dynamicSellerLink(Varien_Event_Observer $observer)
	{
		$layout = $observer->getEvent()->getLayout();
		$update = $layout->getUpdate();
		$sellerLink = Mage::getStoreConfig('marketplace/seller/link_label');
		$xml = "<reference name='top.links'>
					<action method='addLink' translate='label title'>
						<label>{$sellerLink}</label>
						<url>marketplace/seller/create</url>
						<title>Connect As Seller</title>
						<prepare>true</prepare>
						<position>1</position>
					</action>
				</reference>";
    $update->addUpdate($xml);
    return;
	}
	public function saveSellerToOrderItem(Varien_Event_Observer $observer) 
	{
			$quoteItem = $observer->getItem();
			$orderItem = $observer->getOrderItem();
			
			$productId = $quoteItem->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			$marketplacePerProductShippingMethod = $this->getCurrentShippingObject()->getShippingMethod();
			$collection = Mage::getModel('marketplace/vendorproduct')->getCollection()
						->addFieldToFilter('product_id',$productId);
						
			if($collection->count() > 0 && !$quoteItem->getParentItemId())
			{
				if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) 
				{
					$options = $orderItem->getProductOptions();
					$options['additional_options'] = unserialize($additionalOptions->getValue());
					$orderItem->setProductOptions($options);
				}
				$userId = $collection->getFirstItem()->getUserId();
				$seller = Mage::getModel('marketplace/seller')->getCollection()
								->addFieldToFilter('user_id',$userId);
				$seller_admin_comission = $seller->getFirstItem()->getAdminCommissionByPercentage();
				$country = $seller->getFirstItem()->getCountry();
				
				//IF Marketplace product shipping method set then assign shipping Value to order item 
				
				if($marketplacePerProductShippingMethod == 'marketplaceproductshipping_marketplaceproductshipping')
				{
					$shippingPrice = $product->getInternationalShippingCost() * $quoteItem->getQty();
					if($country == $this->getCurrentShippingObject()->getCountryId())
							$shippingPrice = $product->getDomesticShippingCost() * $quoteItem->getQty();
					$orderItem->setSellerPerProductShipping($shippingPrice);
				}
				
				$orderItem->setAdminOrderCommission($seller_admin_comission);
				$orderItem->setSellerId($seller->getFirstItem()->getId());
			}
	}
	
	public function getCurrentShippingObject()
	{
		return Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
	}
	public function checkPermissionRole($observer)
	{
		$action = $observer->getEvent()->getAction();
		$fullActionName = $action->getFullActionName();
		$url = Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/index/");
		  		
		if($fullActionName == "adminhtml_catalog_product_edit")
		{ 
			$isSeller = Mage::getModel('marketplace/seller')->isSeller();
			if($isSeller)
			{
				$userId = Mage::helper('marketplace')->getCurrentUserId();
				$productId =  Mage::app()->getRequest()->getParam('id');
				$collection = Mage::getModel('marketplace/vendorproduct')->getCollection()
							->addFieldToFilter('user_id',$userId)
							->addFieldToFilter('product_id',$productId);
				if($collection->count() == 0)
					Mage::app()->getFrontController()->getResponse()->setRedirect($url);
			}
		}
	}
	public function customFilterProductCollection(Varien_Event_Observer $observer) 
	{
		$collection = $observer->getEvent()->getCollection();
		if($product_status = Mage::app()->getRequest()->getParam('product_status'))
		{
			$collection->addAttributeToFilter('product_status',1);
			return $this;
		}
		$isSeller = Mage::getModel('marketplace/seller')->isSeller();
		$currentUserId = Mage::helper('marketplace')->getCurrentUserId();
		if(!is_null($isSeller) && !is_null($currentUserId))
		{	  
			$vendorProductCollection = Mage::getModel('marketplace/vendorproduct')->getCollection()
						->addFieldToFilter('user_id',$currentUserId);
			$productIds = $vendorProductCollection->getColumnValues('product_id');

		$collection->addAttributeToFilter('entity_id',array('in' => $productIds ));
		return $this;	
		}		 
    }
    public function catalogProductSaveAfter(Varien_Event_Observer $observer)
    {
		$isSeller = Mage::getModel('marketplace/seller')->isSeller();
		$currentUserId = Mage::helper('marketplace')->getCurrentUserId();
		if(!is_null($isSeller) && !is_null($currentUserId))
		{	  
			$product = $observer->getProduct();
			$id = null;
		
			$vendorProductCollection = Mage::getModel('marketplace/vendorproduct')->getCollection()
			->addFieldToFilter('user_id',$currentUserId)
			->addFieldToFilter('product_id',$product->getId());
			
			if($vendorProductCollection->count() > 0)
				$id = $vendorProductCollection->getFirstItem()->getId();	
			
				$vendorProductCollection = Mage::getModel('marketplace/vendorproduct');
				$vendorProductCollection->load($id);		
				$vendorProductCollection->setProductId($product->getId());
				$vendorProductCollection->setUserId($currentUserId);
				$vendorProductCollection->save();
		}
	}
	
	public function catalogProductDeleteBefore(Varien_Event_Observer $observer)
	{ 		
		$product = $observer->getProduct();
		
		$sellerCollection = Mage::getModel('marketplace/vendorproduct')->getCollection()
			->addFieldToFilter('product_id',$product->getId());
		
		if($sellerCollection->count() > 0)
		{
			$id = $sellerCollection->getFirstItem()->getId();	
			
			$seller = Mage::getModel('marketplace/vendorproduct');
			$seller->load($id);
			$seller->delete($id);
		}
	}
	public function sellerOperationAfterOrderSuccess($observer)
	{		
		$orderIncrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);		
		
		$payment = Mage::getModel('marketplace/payment');
		
		$vars['customer_name'] = $order->getBillingAddress()->getName();
		$vars['billing_address'] = $order->getBillingAddress()->format('html');
		$vars['shipping_address'] = $order->getShippingAddress()->format('html');
		$vars['order_increment_id'] = $orderIncrementId;		
		$vars['not_virtual_order'] = $order->getIsNotVirtual();
		
		$head = '<thead style="background:#f9f9f9;">
			<tr>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 12px; padding: 3px 9px;"><strong>Product</strong></th>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 12px; padding: 3px 9px;"><strong><span>Original Price</span></strong></th>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 12px; padding: 3px 9px;"><strong>Price</strong></th>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 12px; padding: 3px 9px;"><strong>Qty</strong></th>
				<th align="left" bgcolor="#EAEAEA" style="font-size: 12px; padding: 3px 9px;"><strong><span>Row Total</strong></span></th>
			</tr>
		</thead>';
		
		$orderItemsArray = array();
		$sellerArray = array();
		$sum = 0;
		foreach($order->getAllItems() as $_item)
		{
			if($_item->getParentItemId())
			{ continue;
			}
			$collection = Mage::getModel('marketplace/vendorproduct')->getCollection()
						->addFieldToFilter('product_id',$_item->getProductId());
			if($collection->count() > 0)
			{
				$seller = Mage::getModel('admin/user')->load($collection->getFirstItem()->getUserId());
				
				$orderItemsArray[$seller->getUserId()]['seller_email'] = $seller->getEmail();
				$orderItemsArray[$seller->getUserId()]['seller_name'] = $seller->getName();
				if(!isset($orderItemsArray[$seller->getUserId()]['order_items']))
				{
					$orderItemsArray[$seller->getUserId()]['order_items']= '';
				}
				$orderItemsArray[$seller->getUserId()]['order_items'] .= '<tbody>
					<tr>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">
							<p>' . $_item->getName() . '</p>
							<strong>SKU:</strong> ' . $_item->getSku().
						'</td>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">' . $order->formatPrice($_item->getPriceInclTax()) . '</td>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">' . $order->formatPrice($_item->getOriginalPrice()) . '</td>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">' . $_item->getQtyOrdered() . '</td>
						<td align="left" valign="top" style="font-size: 11px; padding: 3px 9px; border-bottom: 1px dotted #CCCCCC;">' . $order->formatPrice($_item->getRowTotalInclTax()) . '</td>
					</tr>
				</tbody>';
				if(!isset($orderItemsArray[$seller->getUserId()]['seller_order_total']))
				{
					$orderItemsArray[$seller->getUserId()]['seller_order_total'] = 0;
				}	
				$adminCommisionAmount = ($_item->getAdminOrderCommission() * $_item->getRowTotalInclTax())/100;
				$sellerAmount = $_item->getRowTotalInclTax() - $adminCommisionAmount;
				$sellerCollection = Mage::getModel('marketplace/seller')->getCollection()->addFieldToFilter('user_id',$seller->getUserId());
				$sellerId = $sellerCollection->getFirstItem()->getId();
				$orderItemsArray[$seller->getUserId()]['order_id'] = $orderIncrementId;
				$orderItemsArray[$seller->getUserId()]['seller_id'] = $sellerId;
				
				$orderItemsArray[$seller->getUserId()]['seller_order_total'] +=  $sellerAmount;
				if($order->getShippingMethod() == 'marketplaceproductshipping_marketplaceproductshipping')	
				{ 	$shippingPrice = Mage::getModel('marketplace/seller')->getSellerShippingPrice($order,$_item->getSellerId());
					$orderItemsArray[$seller->getUserId()]['seller_order_total'] +=$shippingPrice;
				}
			}
		}
		
		foreach($orderItemsArray as $key => $value)
		{
			$payment = Mage::getModel('marketplace/payment');
			$payment->setOrderId($value['order_id']);
			$payment->setSellerId($value['seller_id']);
			$payment->setSellerPaidAmount(0);
			$payment->setAdminAmount($value['seller_order_total']);	
			$payment->save();
			$seller_order = '<table cellspacing="0" cellpadding="0" border="0" width="700" style="border: 1px solid #EAEAEA;">';
			$seller_order .= $head;
			$seller_order .= $value['order_items'];
			$seller_order .= $value['seller_order_total'];
			$seller_order .= '</table>';
			$vars['seller_order_items'] = $seller_order;
			$vars['seller_name'] = $value['seller_name'];
			$this->sendTransactionalEmail($value['seller_email'], $vars);
		}
	}
	
	public function sendTransactionalEmail($email, $vars)
	{
		$templateId = Mage::getStoreConfig('marketplace/email/email_template');
		$emailSender = Mage::getStoreConfig('marketplace/email/email_sender');		
		$identify_name = Mage::getStoreConfig('trans_email/ident_'.$emailSender.'/name');
		$identify_email = Mage::getStoreConfig('trans_email/ident_'.$emailSender.'/email');
	  
		$sender = array('name'  => $identify_name,	'email' => $identify_email);
		$recepientEmail = $email;
		$storeId = Mage::app()->getStore()->getId();
		$translate  = Mage::getSingleton('core/translate');
		Mage::getModel('core/email_template')
		->sendTransactional($templateId, $sender, $recepientEmail, $email,$vars, $storeId);
		$translate->setTranslateInline(true);
	}
}

?>