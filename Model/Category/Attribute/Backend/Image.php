<?php

class Delight_Delightapi_Model_Category_Attribute_Backend_Image extends Mage_Catalog_Model_Category_Attribute_Backend_Image
{

	/**
	 * Overriden to catch the ugly exceptions if there is no Image set on an API-Call
	 * @see app/code/core/Mage/Catalog/Model/Category/Attribute/Backend/Mage_Catalog_Model_Category_Attribute_Backend_Image#afterSave($object)
	 */
	public function afterSave($object) {
		$value = $object->getData($this->getAttribute()->getName());
		if (!empty($value)) {
			return parent::afterSave($object);
		}
		return;
	}

}