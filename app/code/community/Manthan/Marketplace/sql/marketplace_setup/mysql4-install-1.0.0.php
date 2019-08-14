<?php
$roleCollection = Mage::getModel('admin/role')
        ->getCollection()
		->addFieldToFilter('role_name', Manthan_Marketplace_Model_Seller::ROLE);
		
if ($roleCollection->count() == 0) {
	$role = Mage::getModel('admin/role')
			->setRoleName(Manthan_Marketplace_Model_Seller::ROLE)
			->setRoleType(Manthan_Marketplace_Model_Seller::ROLE_TYPE)
			->setTreeLevel(1)
			->save();		

    Mage::getModel('admin/rules')
            ->setRoleId($role->getId())
            ->setResources(array('admin/catalog','admin/catalog/products','admin/seller_dashboard','admin/system','admin/system/myaccount',
							'admin/seller','admin/seller/orders','admin/seller/review','admin/seller/payment'))
            ->saveRel();
	
		$parentId = 0;
		$roles = Mage::getModel('admin/roles')->load(1);
		$roleName = $roles->getRoleName();
		$roles->setName($roleName)
			 ->setPid($parentId)
			 ->setRoleType('G');
        $roles->save();
		
		$resources = array('admin/seller_dashboard','admin/seller','admin/seller/orders','admin/seller/review');
		foreach($resources as $resource)
		{
			$role = Mage::getModel('admin/rules');
					$role->setRoleId($roles->getId());
					$role->setResourceId($resource);
					$role->setRoleType('G');
					$role->setAssertId(0);
					$role->setPermission('deny');
				$role->save();
			
		}
}

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('marketplace/seller');

if ($installer->getConnection()->isTableExists($tableName) != true) {
$table = $installer->getConnection()->newTable($installer->getTable('marketplace/seller'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
				'auto_increment' => true,
				'unsigned' => true,
				'nullable' => false,
				'primary' => true,
            ), 'Id')
		->addColumn('shop_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable' => true,
            ), 'Shop Name')
		->addColumn('shop_description', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable' => true,
            ), 'Shop Description')
		->addColumn('url_path', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable' => true,
            ), 'Shop Url')	
		->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable' => true,
            ), 'Seller Contact Number')
         ->addColumn('image', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable' => true,
            ), 'Shop Image')			
		->addColumn('country', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable' => true,
            ), 'Country Code')
		->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Postcode')
		 ->addColumn('admin_total_earn', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
                'nullable' => true,
            ), 'Admin Total Earn')
        ->addColumn('admin_commission_by_percentage', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
                'nullable' => true,
            ), 'Admin Commission By Percentage')
        ->addColumn('total_vendor_earn', Varien_Db_Ddl_Table::TYPE_DECIMAL, '10,2', array(
                'nullable' => true,
            ), 'Total Vendor Earn')
		->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'User Id')
		->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                'nullable' => true,
            ), 'Created Date Of Seller Registration')
		->addIndex($installer->getIdxName('marketplace/seller', array('user_id')),
			array('user_id'))
		->addForeignKey($installer->getFkName('marketplace/seller', 'user_id', 'admin/user', 'user_id'),
        'user_id', $installer->getTable('admin/user'), 'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
		
$installer->getConnection()->createTable($table);
}

/***************** CREATE TABLE TO MANAGE PRODUCT FOR MULTIVENDOR *******************/

$tableName = $installer->getTable('marketplace/vendorproduct');
if ($installer->getConnection()->isTableExists($tableName) != true) {

$table = $installer->getConnection()->newTable($installer->getTable('marketplace/vendorproduct'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
				'auto_increment' => true,
				'unsigned' => true,
				'nullable' => false,
				'primary' => true,
            ), 'Id')
		->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Product Entity Id')
         ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'User Id')
		->addIndex($installer->getIdxName('marketplace/vendorproduct', array('user_id')),
			array('user_id'))
		->addForeignKey($installer->getFkName('marketplace/vendorproduct', 'user_id', 'admin/user', 'user_id'),
        'user_id', $installer->getTable('admin/user'), 'user_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE);
		
$installer->getConnection()->createTable($table);
}
/***************** CREATE TABLE TO MANAGE SELLER RATINGS *******************/
$tableName = $installer->getTable('marketplace/rating');
if ($installer->getConnection()->isTableExists($tableName) != true) {
$table = $installer->getConnection()->newTable($installer->getTable('marketplace/rating'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
				'auto_increment' => true,
				'unsigned' => true,
				'nullable' => false,
				'primary' => true,
            ), 'Id')
		->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable' => true,
            ), 'Rating Name')
         ->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Status');
$installer->getConnection()->createTable($table);
}
/***************** CREATE TABLE TO MANAGE SELLER RATINGS *******************/
$tableName = $installer->getTable('marketplace/review');
if ($installer->getConnection()->isTableExists($tableName) != true) 
{	
$table = $installer->getConnection()->newTable($installer->getTable('marketplace/review'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
				'auto_increment' => true,
				'unsigned' => true,
				'nullable' => false,
				'primary' => true,
            ), 'Id')
		->addColumn('subject', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable' => true,
            ), 'Subject of Review')
		->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
                'nullable' => true,
            ), 'Review Description')
		->addColumn('shipment_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Shipment Item ID')	
		->addColumn('status', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Status')
		->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                'nullable' => true,
            ), 'Created Date Of Rate');
$installer->getConnection()->createTable($table);
}

/***************** CREATE TABLE TO MANAGE SELLER RATED BY CUSTOMER *******************/
$tableName = $installer->getTable('marketplace/sellerrate');
if ($installer->getConnection()->isTableExists($tableName) != true) 
{
$table = $installer->getConnection()->newTable($installer->getTable('marketplace/sellerrate'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
				'auto_increment' => true,
				'unsigned' => true,
				'nullable' => false,
				'primary' => true,
            ), 'Id')
		->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Seller ID')
		->addColumn('shipment_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Shipment Item ID')
		->addColumn('rating_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Rating ID')
		->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable' => true,
            ), 'Rated Value');
		
$installer->getConnection()->createTable($table);
}
$createSetup = new Mage_Eav_Model_Entity_Setup('core_setup');

$createSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'product_status', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Product Status',
    'input'             => 'select',
    'class'             => 'product-status',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => 1,
	'source'			=> 'marketplace/catalog_product_attribute_status',
    'required'          => 1,
    'user_defined'      => 0,
    'searchable'        => 0,
    'filterable'        => 0,
    'comparable'        => 0,
    'visible_on_front'  => 0,
    'unique'            => 0,
    'apply_to'          => 'simple,configurable,bundle,grouped,virtual,downloadable',
    'is_configurable'   => 0
));

$installer->getConnection()
	->addColumn($installer->getTable('sales/order_item'),'seller_id',array(
			'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'nullable' => false,
			'default' => 0,
			'comment' => 'Seller Id'
	));
$installer->getConnection()
	->addColumn($installer->getTable('sales/order_item'),'admin_order_commission',array(
			'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'nullable' => false,
			'default' => 0,
			'comment' => 'Commission of Order Item by Admin'
	));
/*$installer->getConnection()
	->addColumn($installer->getTable('sales/order_item'),'seller_payment_status',array(
			'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
			'nullable' => false,
			'default' => 0,
			'comment' => 'Seller Payment Status'
	));*/	
$tableName = $installer->getTable('marketplace/payment');
if ($installer->getConnection()->isTableExists($tableName) != true) 
{
$table = $installer->getConnection()->newTable($installer->getTable('marketplace/payment'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
                ), 'Entity Id')
        ->addColumn('seller_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
                ), 'Seller Id')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '0',
			), 'Order Id')
        ->addColumn('payment_note', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
            'nullable' => true,
                ), 'Information')
		->addColumn('admin_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => true,
                ), 'Admin Amount')			
        ->addColumn('seller_paid_amount', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable' => true,
                ), 'Seller Paid Amount')
	->addColumn('payment_date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
			'nullable' => true,
		), 'Payment Date');
$installer->getConnection()->createTable($table);
}

$installer->endSetup();	

	$roleModel = Mage::getModel('admin/role')->getCollection()
			->addFieldToFilter('role_name',Manthan_Marketplace_Model_Seller::ROLE);

	Mage::getConfig()->saveConfig('marketplace/seller/role',$roleModel->getFirstItem()->getId());
	Mage::app()->getStore()->resetConfig();
	

?>