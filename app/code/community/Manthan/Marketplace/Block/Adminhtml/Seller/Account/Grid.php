<?php
class Manthan_Marketplace_Block_Adminhtml_Seller_Account_Grid extends Mage_Adminhtml_Block_Widget_Grid {    

	protected $_countTotals = true;
    public function __construct() {
        parent::__construct();
        $this->setId('vendorGrid');
        $this->setDefaultSort('id');
    }
	public function getTotals()
    {
        $totals = new Varien_Object();
        $fields = array(
            'admin_total_earn'=> 0,
        );
		
        foreach ($this->getCollection() as $item) {
            foreach($fields as $field=>$value){
                $fields[$field] += $item->getData($field);
            }
        }
        $fields['entity_id']='Admin Total Earn';
		$totals->setData($fields);
        return $totals;
    }
    protected function _prepareCollection() {
      
        $userTable = Mage::getSingleton('core/resource')->getTableName('admin_user');
		$vendorProductCollection = Mage::getModel('marketplace/seller')->getCollection()->setOrder('created_date','DESC');
		
		$vendorProductCollection->getSelect()
		->join(array('user' => $userTable),'`main_table`.user_id=user.user_id', array('user.*'));
		
			$this->setCollection($vendorProductCollection);
			return parent::_prepareCollection();
		
	}
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('adminhtml')->__('ID'),
            'align' => 'right',
            'width' => '80px',
            'index' => 'entity_id',
        ));
		
		$this->addColumn('username', array(
            'header' => Mage::helper('adminhtml')->__('Seller'),
            'index' => 'username',
            'width' => '100px',
        ));
		
        $this->addColumn('shop_name', array(
            'header' => Mage::helper('adminhtml')->__('Shop Name'),
            'index' => 'shop_name',
            'width' => '100px',
        ));

        $this->addColumn('shop_description', array(
            'header' => Mage::helper('adminhtml')->__('Shop Description'),
            'index' => 'shop_description',
            'width' => '400px',
        ));
		
		$this->addColumn('created_date', array(
            'header' => Mage::helper('adminhtml')->__('Seller Registration Date'),
            'index' => 'created_date',
			'type' => 'datetime',
            'width' => '50px',
        ));
		
		$this->addColumn('admin_commission_by_percentage', array(
            'header' => Mage::helper('adminhtml')->__('Admin Commision(%)'),
            'index' => 'admin_commission_by_percentage',
            'width' => '20px',
        ));
		
		$currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
		$this->addColumnAfter('admin_total_earn', array(
		   'header' => Mage::helper('adminhtml')->__('Admin Earn'),
			'type'      => 'currency',
			'currency_code'  => $currencyCode,
			'index'     => 'admin_total_earn',
			'filter'	=> false,
			'width' => '100px',

		), 'admin_commission_by_percentage');
		
        $this->addColumn('telephone', array(
            'header' => Mage::helper('adminhtml')->__('Telephone'),
            'index' => 'telephone',
            'width' => '100px',
        ));
		
		$this->addColumn('is_active', array(
            'header' => Mage::helper('adminhtml')->__('Is Active'),
			'index' => 'is_active',
			'type' => 'options',
            'width' => '70px',
            'options' => array(0=> 'Inactive',1=>'Active')
        ));
		
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}

?>
