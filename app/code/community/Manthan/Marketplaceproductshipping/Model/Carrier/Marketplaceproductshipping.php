<?php
class Manthan_Marketplaceproductshipping_Model_Carrier_Marketplaceproductshipping
	extends Mage_Shipping_Model_Carrier_Abstract
	implements Mage_Shipping_Model_Carrier_Interface {		 
	protected $_code = 'marketplaceproductshipping';
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
		{			
		if (!$this->getConfigFlag('active')) {
			return false;
		}
			$shippingPrice = 0;
			if ($request->getAllItems()) {
				
				 $destinationCountryCode = $request->getDestCountryId();
				
				foreach ($request->getAllItems() as $item) 
				{
					$product = Mage::getModel('catalog/product')->load($item->getProductId());
				
					if ( $item->getParentItemId()) {
						continue;
					}
					$sellerProductCollection = Mage::getModel('marketplace/vendorproduct')->getCollection()
						->addFieldToFilter('product_id',$product->getId());
					
					if($sellerProductCollection->count() > 0 )
					{	
						$userId = $sellerProductCollection->getFirstItem()->getUserId();
						$sellerCollection = Mage::getModel('marketplace/seller')->getCollection()
									->addFieldToFilter('user_id',$userId);
						$sellerCountryCode = $sellerCollection->getFirstItem()->getCountry();
						
						if($sellerCountryCode == $destinationCountryCode && $product->getDomesticShippingCost() != '' )
						{
							$shippingPrice += ($product->getDomesticShippingCost() * $item->getQty());
						}
						else if($product->getDomesticShippingCost() == '' && $product->getInternationalShippingCost()=='')
						{
							$shippingPrice += ($item->getQty() * $this->getConfigData('price'));
						}
						else if($product->getInternationalShippingCost()=='' && ($product->getDomesticShippingCost() || $product->getDomesticShippingCost() == 0 ))
						{
							$shippingPrice += $product->getDomesticShippingCost() * $item->getQty();
						}
						else if($product->getDomesticShippingCost()=='' && ($product->getInternationalShippingCost() || $product->getInternationalShippingCost() == 0  ))
						{
							$shippingPrice += $product->getInternationalShippingCost() * $item->getQty();
						}
						else
						{
							$shippingPrice += $product->getInternationalShippingCost() * $item->getQty();
						}
					}
					else{
							$countryCode = Mage::getStoreConfig('general/country/default');
							if($destinationCountryCode == $countryCode)
							{
								$shippingPrice += $product->getDomesticShippingCost() * $item->getQty();
							}
							else if($product->getDomesticShippingCost()=='' && $product->getInternationalShippingCost()=='')
							{
								$shippingPrice += ($item->getQty() * $this->getConfigData('price'));
							}
							else if($product->getInternationalShippingCost()=='' && ($product->getDomesticShippingCost() || $product->getDomesticShippingCost() == 0 ))
							{
								$shippingPrice += $product->getDomesticShippingCost() * $item->getQty();
							}
							else if($product->getDomesticShippingCost()=='' && ($product->getInternationalShippingCost() || $product->getInternationalShippingCost() == 0  ))
							{
								$shippingPrice += $product->getInternationalShippingCost() * $item->getQty();
							}
							else
							{
								$shippingPrice += $product->getInternationalShippingCost() * $item->getQty();
							}
						}
                }
				$result = Mage::getModel('shipping/rate_result');			
				if ($shippingPrice !== false) {
					$method = Mage::getModel('shipping/rate_result_method');
					$method->setCarrier('marketplaceproductshipping');
					$method->setCarrierTitle($this->getConfigData('title'));
					$method->setMethod('marketplaceproductshipping');
					$method->setMethodTitle($this->getConfigData('name'));	
					$method->setPrice($shippingPrice);
					$method->setCost($shippingPrice);
					$result->append($method);
					       }
				return $result;
		}
	}
    public function getAllowedMethods()
    {
			return array('marketplaceproductshipping'=>$this->getConfigData('name'));
    }

}