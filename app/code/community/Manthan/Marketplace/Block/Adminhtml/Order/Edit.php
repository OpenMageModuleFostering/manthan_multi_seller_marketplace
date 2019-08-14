<?php

class Manthan_Marketplace_Block_Adminhtml_Order_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

	 public function __construct(){
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'marketplace';
        $this->_controller = 'adminhtml_order';
        $this->_mode = 'edit';
		parent::__construct();
		$this->_removeButton('save');
		$this->_removeButton('reset');
		if( $this->getRequest()->getActionName() == "invoice")
		{
			$this->_removeButton('back');
			
			 $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back to Order'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/view',array('order_id'=>$this->getOrder()->getId())) . '\')',
            'class'     => 'back',
        ), 1);
		
			 $this->_addButton('print', array(
            'label'     => Mage::helper('adminhtml')->__('Print'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/print',array('invoice_id'=>$this->getInvoice()->getId())) . '\')',
            'class'     => 'scalable',
        ), 1);
		$this->_addButton('packingslip', array(
            'label'     => Mage::helper('adminhtml')->__('Packing Slip'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/packingslip',array('invoice_id'=>$this->getInvoice()->getId())) . '\')',
            'class'     => 'scalable',
        ), 1);
		}
		else{
		
		 $this->_addButton('invoice', array(
            'label'     => Mage::helper('adminhtml')->__('Invoice'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/invoice',array('order_id'=>$this->getOrder()->getId())) . '\')',
            'class'     => 'scalable go',
        ), 1);
		}
    }
	
	protected function _prepareLayout()
    {
		parent::_prepareLayout();
        if ($this->_blockGroup && $this->_controller && $this->_mode) 
		{
			$childBlock = $this->getLayout()->createBlock('core/template')->setTemplate('marketplace/sales/order/view/totals.phtml');
			$orderDetailsBlock = $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form');
			$this->setChild('form',$orderDetailsBlock);
			$orderDetailsBlock->setChild('seller.order.totals',$childBlock);
        }
		
    }
	 public function getHeaderCssClass()
    {
        return 'icon-head head-sales-order';
    }

    public function getHeaderHtml()
    {
        return '<h3 class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }

    public function getHeaderText()
    {
       if (!is_null($this->getOrder())) 
		{
			if( $this->getRequest()->getActionName() == "invoice")
			{
				return Mage::helper('sales')->__('Invoice #%1$s | %2$s | %3$s', $this->getInvoice()->getIncrementId(), $this->getInvoice()->getStateName(), $this->formatDate($this->getInvoice()->getCreatedAtDate(), 'medium', true));	
			}
			return Mage::helper('marketplace')->__("Order #%s | %s", $this->getOrder()->getRealOrderId(), $this->formatDate($this->getOrder()->getCreatedAtDate(), 'medium', true));
		}
    }

    public function getOrder() 
	{
        return Mage::registry('current_order');
    }
	
	public function getInvoice() 
	{
		return $this->getOrder()->getInvoiceCollection()->getFirstItem();
    }

}

?>
