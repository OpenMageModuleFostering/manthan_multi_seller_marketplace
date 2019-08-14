<?php

class Manthan_Marketplace_Block_Adminhtml_Rating_Grid extends Mage_Adminhtml_Block_Widget_Grid {    

    public function __construct() {
        parent::__construct();
        $this->setId('ratingGrid');
        $this->setDefaultSort('id');
    }

    protected function _prepareCollection() {
			$ratingCollection = Mage::getModel('marketplace/rating')->getCollection();			
			$this->setCollection($ratingCollection);
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
            'header' => Mage::helper('adminhtml')->__('Name'),
            'index' => 'name',
            'width' => '400px',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('adminhtml')->__('Status'),
            'index' => 'status',
            'width' => '200px',
			 'type' => 'options',
            'options' => array(
				0 => Mage::helper('adminhtml')->__('Disabled'),
                1 => Mage::helper('adminhtml')->__('Enabled'),
            ),
        ));
		
		$this->addColumn('action', array(
            'header' => Mage::helper('adminhtml')->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('adminhtml')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
        ));
		
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}

?>
