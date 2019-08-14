<?php
class Manthan_Marketplace_ReviewController extends Mage_Core_Controller_Front_Action
{
	public function saveAction()
	{ 
		try
		{
			if(is_null($this->getRequest()->getPost('review')) && is_null($this->getRequest()->getPost('rating')) )
			{
				Mage::getSingleton('core/session')->addError("Please enter valid values");
				$this->_redirect('sales/order/view', array('order_id' => $this->getRequest()->getParam('order_id')));
				return;
			}
			if($review = $this->getRequest()->getPost('review'))
			{
				$reviewCollection = Mage::getModel("marketplace/review")
							->setSubject($review['subject'])
							->setDescription($review['description'])
							->setShipmentItemId($this->getRequest()->getParam('shipment_item_id'))
							->setStatus(0)
							->setCreatedDate(Mage::getModel('core/date')->timestamp(time()));
				$reviewCollection->save();
			}
			if(count($this->getRequest()->getPost('rating')) > 0 )
			{		
				foreach($this->getRequest()->getParam('rating') as $id => $value)
				{
					$rateCollection = Mage::getModel("marketplace/sellerrate")
					->setSellerId($this->getRequest()->getParam('seller_id'))
					->setShipmentItemId($this->getRequest()->getParam('shipment_item_id'))
					->setRatingId($id)
					->setValue($value);
					$rateCollection->save();
				}
			}
			Mage::getSingleton('core/session')->addSuccess('Review has been submitted successfully.');
			$this->_redirect('sales/order/view', array('order_id' => $this->getRequest()->getParam('order_id')));
		}
		catch (Mage_Core_Exception $e) 
		{
			Mage::getSingleton('core/session')->addError($e->getMessage());
			$this->_redirect('sales/order/view', array('order_id' => $this->getRequest()->getParam('order_id')));
        }	
	}
}

?>
