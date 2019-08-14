<?php

class Manthan_Marketplace_Block_Adminhtml_Seller_Account_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid {    

    public function __construct() {
        parent::__construct();
        $this->setId('sellerProductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
		 $this->setUseAjax(true);
		$this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
		 $entityId = $this->getRequest()->getParam('id');
		$seller = Mage::getModel('marketplace/seller')->load($entityId);
		 $userId = $seller->getUserId();
		$collection = Mage::getModel('marketplace/vendorproduct')->getCollection()->addFieldToFilter('user_id',$userId);
		$productIds = $collection->getColumnValues('product_id');
		
				$product = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect('*')
					->addAttributeToFilter('entity_id',array('in'=>$productIds));
				
		
		$this->setCollection($product);
        return parent::_prepareCollection();
	}	

    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('adminhtml')->__('ID'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'entity_id',
        ));
		
		$this->addColumn('name', array(
            'header' => Mage::helper('adminhtml')->__('Product Name'),
            'index' => 'name',
            'width' => '100px',
        ));
		
		$this->addColumn('type_id', array(
            'header' => Mage::helper('adminhtml')->__('Product Type'),
            'index' => 'type_id',
            'width' => '100px',
        ));
		
		$this->addColumn('sku', array(
            'header' => Mage::helper('adminhtml')->__('SKU'),
            'index' => 'sku',
            'width' => '100px',
        ));
		
        $this->addColumn('created_at', array(
            'header' => Mage::helper('adminhtml')->__('Created At'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '70px',
        ));
		

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return '';
    }
}

?>
