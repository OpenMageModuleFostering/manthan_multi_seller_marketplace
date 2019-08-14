<?php
class Manthan_Marketplace_Block_Register extends Mage_Core_Block_Template {
     
	public function getCountryList()
    {
		$countries[''] = '';
		$countryCollection = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
		foreach($countryCollection as $country)		
			$countries[$country['value']] = $country['label'];
		return $countries;
	}
}

?>
