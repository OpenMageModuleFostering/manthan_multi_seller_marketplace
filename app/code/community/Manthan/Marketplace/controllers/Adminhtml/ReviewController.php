<?php
class Manthan_Marketplace_Adminhtml_ReviewController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
		if(Mage::getModel('marketplace/seller')->isSeller())
			$this->loadLayout()->_setActiveMenu('seller');
		else
		$this->loadLayout()->_setActiveMenu('manthan');
        return $this;
    }
    public function indexAction() 
	{ 
        $this->_initAction();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Reviews/Marketplace'));
		$this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_review'));
        $this->renderLayout();
    }
	 public function editAction()
    {   
        $reviewId = $this->getRequest()->getParam('id');
        $reviewModel = Mage::getModel('marketplace/review')->load($reviewId);
	
		if ($reviewModel->getId() || $reviewId == 0) 
        {
            Mage::register('review_data', $reviewModel);
			$this->_initAction();
			$this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_review_edit'));
			$this->renderLayout();
        } 
        else 
        {
            Mage::getSingleton('adminhtml/session')->addError('Review does not exist');
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
				$ratingModel = Mage::getModel('marketplace/review');
				$ratingModel
				->setStatus($postData['status'])
				->setId($this->getRequest()->getParam('id'))
				->save();	
				 Mage::getSingleton('adminhtml/session')->addSuccess('Review has been successfully saved');
				 Mage::getSingleton('adminhtml/session')->setreviewData(false);
				 $this->_redirect('*/*/');
				return;
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setreviewData($this->getRequest()->getPost());
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
