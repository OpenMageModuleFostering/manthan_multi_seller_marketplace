<?php
class Manthan_Marketplace_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{
    protected function _prepareColumns()
    { 
		 $this->addColumn('seller',
            array(
                'header'=> Mage::helper('catalog')->__('Seller Name'),
                'width' => '150px',
                'index' => 'entity_id',
				'renderer'=>'Manthan_Marketplace_Block_Adminhtml_Catalog_Product_Renderer_Seller',
				'filter_condition_callback' => array($this, '_sellerFilter'),
        ));
		
		$this->addColumnAfter('product_status',
            array(
				'header'    => Mage::helper('catalog')->__('Product Status'),
                'width' => '130px',
                'index' => 'entity_id',
				'type'  => 'options',
				'column_css_class' => 'a-center',
                'options' => array(1 => 'Pending' ,2 => 'Approved' ,3=>'Not Approved'),
				'renderer'=>'Manthan_Marketplace_Block_Adminhtml_Catalog_Product_Renderer_Status',
				'filter_condition_callback' => array($this, '_ProductStatusFilter'),
			),'qty');
       parent::_prepareColumns();
    }
	
	protected function _sellerFilter($collection, $column)
    { 
		if(!$value = $column->getFilter()->getValue()) {
            return $this;
        }
			$user = Mage::getModel('admin/user')->getCollection()
				->addFieldToFilter('username',array('like'=>"%$value%"));
		$userIds = $user->getColumnValues('user_id');
		$seller = Mage::getModel('marketplace/vendorproduct')->getCollection();
		
		if($value == "Admin" || $value == "admin" ){
			$sellerProductIds = $seller->getColumnValues('product_id');
			return $collection->addAttributeToFilter('entity_id',array('nin' => $sellerProductIds));
		}
		
		$seller->addFieldToFilter('user_id',array('in'=>$userIds));
		$productIds = $seller->getColumnValues('product_id');
		$collection->addAttributeToFilter('entity_id',array('in'=>$productIds));
		
        return $collection;
    }
	protected function _ProductStatusFilter($collection, $column)
    {
		if(!$value = $column->getFilter()->getValue()) {
            return $this;
        }
		$collection->addAttributeToSelect('*')->addAttributeToFilter('product_status',$value);
	}
}
?>