<?php
class Manthan_Marketplace_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid 
{
	public function __construct()
	{
        parent::__construct();
        $this->setId('orderGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareCollection() 
	{
		$isSeller = Mage::getModel('marketplace/seller')->isSeller();
       if($isSeller)
	   {
		   $userId = Mage::helper('marketplace')->getCurrentUserId();
		   
			$sellerCollection = Mage::getModel('marketplace/seller')->getCollection()->addFieldToFilter('user_id',$userId);
			$sellerId = $sellerCollection->getFirstItem()->getId();
			$orderItem = Mage::getModel('sales/order_item')->getCollection()->addFieldToFilter('seller_id',$sellerId);
			$orderIds = $orderItem->getColumnValues('order_id');
			
            $salesOrderGridCollection = Mage::getResourceModel($this->_getCollectionClass())
			->addFieldToFilter('entity_id',array('in'=>$orderIds))
			->setOrder('created_at','DESC');
        }
        else
		{
            $salesOrderGridCollection = Mage::getResourceModel($this->_getCollectionClass());
		}
        $this->setCollection($salesOrderGridCollection);
        return parent::_prepareCollection();
    }

    protected function _getCollectionClass() {
        return 'sales/order_grid_collection';
    }

    protected function _prepareColumns() {
		
        $this->addColumn('increment_id', array(
            'header' => Mage::helper('adminhtml')->__('Order #'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'increment_id',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('adminhtml')->__('Purchased On'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('billing_name', array(
            'header' => Mage::helper('adminhtml')->__('Bill to Name'),
            'index' => 'billing_name',
            'width' => '400px',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('adminhtml')->__('Ship to Name'),
            'index' => 'shipping_name',
            'width' => '400px',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('adminhtml')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('adminhtml')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('adminhtml')->__('View'),
                    'url' => array('base' => '*/*/view'),
                    'field' => 'order_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/view', array('order_id' => $row->getId()));
    }
}

?>
