<?php

class Manthan_Marketplace_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid {    

    public function __construct() {
        parent::__construct();
        $this->setId('reviewGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
    }
	protected function _prepareMassaction()
    { 
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
 
        $statuses = array(
						array('label'=>'Pending', 'value'=>0),
						array('label'=>'Approved', 'value'=>1),
						array('label'=>'Not Approved', 'value'=>2)
						);
        $this->getMassactionBlock()->addItem('mass_status', array(
             'label'=> Mage::helper('adminhtml')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
			 'width' => '150px',
             'additional' => array(
                    'visibility' => array(
                         'name' => 'review_status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('adminhtml')->__('Status'),
                         'values' => $statuses
        )
        )
        ));
        return $this;
    }
    protected function _prepareCollection() {
		$sellerId = Mage::getModel('marketplace/seller')->isSeller();
		$seller = Mage::getModel('marketplace/sellerrate')->getCollection();
		if($sellerId)
		{
		$seller = Mage::getModel('marketplace/sellerrate')->getCollection()
					->addFieldToFilter('seller_id',$sellerId);
		}			
		$shipmentIds = array_unique($seller->getColumnValues('shipment_item_id'));
		
		$review = Mage::getModel('marketplace/review')->getCollection()
					->addFieldToFilter('shipment_item_id',array('in'=>$shipmentIds));
				
		
		$this->setCollection($review);
        return parent::_prepareCollection();
	}	

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('adminhtml')->__('ID'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'entity_id',
        ));
		
		$this->addColumn('seller_name', array(
            'header' => Mage::helper('adminhtml')->__('Seller'),
            'index' => 'shipment_item_id',
            'width' => '100px',
			'renderer' => 'Manthan_Marketplace_Block_Adminhtml_Review_Renderer_Seller_Detail'
        ));
		
		$this->addColumn('product_name', array(
            'header' => Mage::helper('adminhtml')->__('Product Name'),
            'index' => 'shipment_item_id',
            'width' => '100px',
			'renderer' => 'Manthan_Marketplace_Block_Adminhtml_Review_Renderer_Product_Detail'
        ));
		
        $this->addColumn('subject', array(
            'header' => Mage::helper('adminhtml')->__('Subject'),
            'index' => 'subject',
            'width' => '100px',
        ));
		
        $this->addColumn('description', array(
            'header' => Mage::helper('adminhtml')->__('Comment'),
            'index' => 'description',
            'width' => '400px',
        ));
		
		 $this->addColumn('created_date', array(
            'header' => Mage::helper('adminhtml')->__('Created At'),
            'index' => 'created_date',
            'type' => 'datetime',
            'width' => '70px',
        ));
		
        $this->addColumn('status', array(
            'header' => Mage::helper('adminhtml')->__('Status'),
            'index' => 'status',
            'type' => 'options',
            'width' => '70px',
            'options' => array(0=>'Pending',1=>'Approved',2=>'Not Approved'),
        ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}

?>
