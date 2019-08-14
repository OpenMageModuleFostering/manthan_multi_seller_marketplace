<?php
class Manthan_Marketplace_Adminhtml_PaymentController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        if(Mage::getModel('marketplace/seller')->isSeller())
			$this->loadLayout()->_setActiveMenu('seller');
		else
		$this->loadLayout()->_setActiveMenu('manthan');
        return $this;
    }  public function indexAction() 
	{ 
        $this->_initAction();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Transaction/Marketplace'));
		$this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_payment'));
        $this->renderLayout();
    }
	 public function editAction()
    {   
        $paymentId = $this->getRequest()->getParam('id');
        $payment = Mage::getModel('marketplace/payment')->load($paymentId);
	
		if ($payment->getId() || $paymentId == 0) 
        {
            Mage::register('payment_data', $payment);
			$this->_initAction();
			$this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_payment_edit'));
			$this->renderLayout();
        }
        else 
        {
            Mage::getSingleton('adminhtml/session')->addError('Payment does not exist');
            $this->_redirect('*/*/');
        }
    }
	
	public function newAction()
	{
	  $this->_forward('edit');
	}
	
	public function saveAction()
	{
		if ($postData = $this->getRequest()->getPost())
		{
			try
			{
				$payment = Mage::getModel('marketplace/Payment');
				if($postData['admin_amount'] == 0)
				{
					$payment
				->setPaymentNote($postData['payment_note'])
				->setPaymentDate($postData['payment_date'])
				->setId($this->getRequest()->getParam('id'))
				->save();
					Mage::getSingleton('adminhtml/session')->addSuccess('Payment has been successfully saved');
					$this->_redirect('*/*/');
					return;	
				}	
				
				$payment
				->setSellerPaidAmount($postData['admin_amount'])
				->setAdminAmount(0)
				->setPaymentNote($postData['payment_note'])
				->setPaymentDate($postData['payment_date'])
				->setId($this->getRequest()->getParam('id'))
				->save();
				$seller = Mage::getModel('marketplace/seller')->load($postData['seller_id']);
				$sellerAmount = $seller->getTotalVendorEarn();
				$totalVendorEarn = $sellerAmount + $postData['admin_amount'];
				
				$order = Mage::getModel('sales/order')->loadByIncrementId($postData['order_id']);
				$collection = Mage::getModel('sales/order_item')->getCollection()
					->addFieldToFilter('order_id',$order->getId())
					->addFieldToFilter('seller_id',$postData['seller_id']);
				$currentAdminCommissionAmount = 0;
				foreach($collection as $_item)
				{
					$currentAdminCommissionAmount+=($_item->getAdminOrderCommission() * $_item->getRowTotalInclTax())/100;	
				}
				
				$seller->setTotalVendorEarn($totalVendorEarn);
				
				$adminAmount = $seller->getAdminTotalEarn();
				$totalAdminEarn = $adminAmount + $currentAdminCommissionAmount;
				$seller->setAdminTotalEarn($totalAdminEarn);
				$seller->save();
				Mage::getSingleton('adminhtml/session')->addSuccess('Payment has been successfully saved');
				$this->_redirect('*/*/');
				return;
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit',array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}
	
	public function deleteAction()
	{
		if($this->getRequest()->getParam('id') > 0)
		{
			try
			{
				$ratingModel = Mage::getModel('marketplace/review');
				$ratingModel->setId($this->getRequest()->getParam('id'))->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess('successfully deleted');
				$this->_redirect('*/*/');
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	public function massStatusAction() {
        $Ids = $this->getRequest()->getParam('id');
		$status = $this->getRequest()->getParam('review_status');
        if (!is_array($Ids) && empty($status)) 
		{
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Please select record(s).'));
		}
		else
		{
			try{
					$review = Mage::getModel('marketplace/review');
					foreach ($Ids as $id) {
						$review->load($id);	
						$review->setStatus($status);
						$review->save();	
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(
						Mage::helper('adminhtml')->__(
						'Total of %d status(es) were changed.', count($Ids)
						)
					);
				 $this->_redirect('*/*/');
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
        }
    }
	protected function _isAllowed()
	{
		 return true;
	}
}
?>
