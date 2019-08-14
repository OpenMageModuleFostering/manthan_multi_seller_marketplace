<?php
$installer = $this;
$installer->startSetup();
$createSetup = new Mage_Eav_Model_Entity_Setup('core_setup');

$createSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'domestic_shipping_cost', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Domestic Shipping Cost',
    'input'             => 'text',
    'class'             => 'domestic-shipping',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => 1,
    'required'          => 0,
    'user_defined'      => 0,
    'searchable'        => 0,
    'filterable'        => 0,
    'comparable'        => 0,
    'visible_on_front'  => 0,
    'unique'            => 0,
	'apply_to'          => 'simple,configurable,grouped,bundle',
    'is_configurable'   => 0
));
$createSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'international_shipping_cost', array(
    'group'             => 'General',
    'type'              => 'int',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'International Shipping Cost',
    'input'             => 'text',
    'class'             => 'international-shipping',
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'           => 1,
    'required'          => 0,
    'user_defined'      => 0,
    'searchable'        => 0,
    'filterable'        => 0,
    'comparable'        => 0,
    'visible_on_front'  => 0,
    'unique'            => 0,
    'apply_to'          => 'simple,configurable,grouped,bundle',
    'is_configurable'   => 0
));

$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'seller_per_product_shipping', 
 'decimal(12,2) default NULL');
	$installer->endSetup();	
 	
?>