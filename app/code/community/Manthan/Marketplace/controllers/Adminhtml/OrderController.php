<?php
class Manthan_Marketplace_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
			$this->loadLayout()->_setActiveMenu('seller');
        return $this;
    }
    public function indexAction()
	{ 
       $this->_initAction();
       $this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_order'));
       $this->renderLayout();
    }
	
	public function viewAction() {
        $id = $this->getRequest()->getParam('order_id');
		
        if (!empty($id)) 
		{ 
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            Mage::register('current_order', Mage::getModel('sales/order')->load($id));
			$this->_initAction();
			$this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_order_edit'));
			$this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
	public function invoiceAction()
	{ 
		$orderId = $this->getRequest()->getParam('order_id');
		 $order = Mage::getModel('sales/order')->load($orderId);
		if(!$order->hasInvoices())
		{
			 Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('you can see invoice details after invoice created by Admin'));
            $this->_redirect('*/*/view',array('order_id'=>$orderId));
			return;
		}	
		
		if (!empty($orderId)) 
		{ 
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            Mage::register('current_order',$order);
			$this->_initAction();
			$this->_addContent($this->getLayout()->createBlock('marketplace/adminhtml_order_edit'));
			$this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('marketplace')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
	}
	public function printAction()
	{
		if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = Mage::getModel('sales/order_invoice')->load($invoiceId)) {
                $pdf = Mage::getModel('marketplace/pdf_invoice')->getPdf($invoice);
                $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else{
            $this->_forward('noRoute');
        }
	}
	
	public function PackingslipAction()
	{
		if ($invoiceId = $this->getRequest()->getParam('invoice_id')) {
            if ($invoice = Mage::getModel('sales/order_invoice')->load($invoiceId)) {
                $pdf = Mage::getModel('marketplace/pdf_shipment_packing')->getPdf($invoice);
                $this->_prepareDownloadResponse('invoice'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').
                    '.pdf', $pdf->render(), 'application/pdf');
            }
        }
        else{
            $this->_forward('noRoute');
        }
	}
	protected function _isAllowed()
	{
		 return true;
	}
	
}
?>
