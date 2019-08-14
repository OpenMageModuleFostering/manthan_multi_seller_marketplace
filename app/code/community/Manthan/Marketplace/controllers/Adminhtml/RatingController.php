<?php
class Manthan_Marketplace_Adminhtml_RatingController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu('manthan');
        return $this;
    }
    public function indexAction() 
	{ 
        $this->_initAction();
		$this->getLayout()->getBlock('head')->setTitle($this->__('Ratings/Marketplace'));
        $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_rating'));
        $this->renderLayout();
    }
	 public function editAction()
    {  
        $ratingId = $this->getRequest()->getParam('id');
        $ratingModel = Mage::getModel('marketplace/rating')->load($ratingId);
		
        if ($ratingModel->getId() || $ratingId == 0) 
        {
            Mage::register('rating_data', $ratingModel);
           $this->_initAction();
           $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_rating_edit'));   
            $this->renderLayout();
        } 
		
        else 
        {
            Mage::getSingleton('adminhtml/session')
                    ->addError('Rating does not exist');
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
			$ratingModel = Mage::getModel('marketplace/rating');
				$ratingModel
				->addData($postData)
				->setId($this->getRequest()->getParam('id'))
				->save();	
				 Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
				 Mage::getSingleton('adminhtml/session')->setratingData(false);
				 $this->_redirect('*/*/');
				return;
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setratingData($this->getRequest()->getPost());
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
				$ratingModel = Mage::getModel('marketplace/rating');
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
	protected function _isAllowed()
	{
		 return true;
	}
}
?>
