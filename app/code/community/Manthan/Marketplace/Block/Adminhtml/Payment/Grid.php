<?php
class Manthan_Marketplace_Block_Adminhtml_Payment_Grid extends Mage_Adminhtml_Block_Widget_Grid {    
 
	protected $_countTotals = true;
    public function __construct() {
        parent::__construct();
        $this->setId('paymentGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
    }
	
	public function getTotals()
    {
        $totals = new Varien_Object();
        $fields = array(
            'seller_paid_amount' => 0,
			'admin_amount'=> 0,
        );
		
        foreach ($this->getCollection() as $item) {
            foreach($fields as $field=>$value){
                $fields[$field] += $item->getData($field);
            }
        }
        $fields['entity_id']='Totals';
		$totals->setData($fields);
        return $totals;
    }
	
    protected function _prepareCollection() {
		$sellerId = Mage::getModel('marketplace/seller')->isSeller();
		$collection = Mage::getModel('marketplace/payment')->getCollection();
		if($sellerId)
			$collection = Mage::getModel('marketplace/payment')->getCollection()->addFieldToFilter('seller_id',$sellerId);
				
		$this->setCollection($collection);
        return parent::_prepareCollection();
	}	

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('adminhtml')->__('ID'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'entity_id',
        ));
		
		$this->addColumn('payment_date', array(
            'header' => Mage::helper('adminhtml')->__('Payment Date'),
            'index' => 'payment_date',
            'type' => 'datetime',
            'width' => '40px',
        ));
		
		$this->addColumn('seller_id', array(
            'header' => Mage::helper('adminhtml')->__('Seller'),
            'index' => 'seller_id',
            'width' => '100px',
			'renderer' => 'Manthan_Marketplace_Block_Adminhtml_Payment_Renderer_Seller',
			'filter_condition_callback' => array($this, '_sellerFilter'),
        ));
		
		$this->addColumn('order_id', array(
            'header' => Mage::helper('adminhtml')->__('Order #'),
            'index' => 'order_id',
            'width' => '100px',
        ));
		
		$this->addColumn('payment_note', array(
            'header' => Mage::helper('adminhtml')->__('Note'),
            'index' => 'payment_note',
            'width' => '400px',
        ));
		
        $this->addColumn('seller_paid_amount', array(
            'header' => Mage::helper('adminhtml')->__('Paid Amount to Seller By Admin'),
            'index' => 'seller_paid_amount',
            'width' => '100px',
			'renderer' => 'Manthan_Marketplace_Block_Adminhtml_Payment_Renderer_Sellerpaidamount'
        ));
		
		$this->addColumn('admin_amount', array(
            'header' => Mage::helper('adminhtml')->__('Admin has Amount'),
            'index' => 'admin_amount',
            'width' => '80px',
			'column_css_class' => 'a-center',
			'renderer' => 'Manthan_Marketplace_Block_Adminhtml_Payment_Renderer_Adminamount'
        ));
		
		if(!Mage::getModel('marketplace/seller')->isSeller())
		{	
			$this->addColumn('action', array(
				'header' => Mage::helper('adminhtml')->__('Action'),
				'width' => '120px',
				'type' => 'action',
				'getter' => 'getId',
				'column_css_class' => 'a-last',
				'actions' => array(
					array(
						'caption' => Mage::helper('adminhtml')->__('Pay Offline'),
						'url' => array('base' => '*/*/edit'),
						'field' => 'id',
					)
				),
				'filter' => false,
				'sortable' => false,
				'is_system' => true,
				'totals_label' => ''
			));
		}
        return parent::_prepareColumns();
    }
	
	protected function _sellerFilter($collection, $column)
    { 
		if(!$value = $column->getFilter()->getValue()) {
            return $this;
        }
			$user = Mage::getModel('admin/user')->getCollection()
				->addFieldToFilter('username',array('like'=>"%$value%"));
		$userIds = $user->getColumnValues('user_id');
		
		$sellerCollection = Mage::getModel('marketplace/seller')->getCollection()
		->addFieldToFilter('user_id',array('in'=>$userIds));
		
		$collection->addFieldToFilter('seller_id',array('in'=>$sellerCollection->getAllIds()));
		
        return $collection;
    }
}

?>
