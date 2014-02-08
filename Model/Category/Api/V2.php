<?php

class Delight_Delightapi_Model_Category_Api_V2 extends Mage_Catalog_Model_Category_Api_V2 {

	/**
	 * (non-PHPdoc)
	 * @see app/code/core/Mage/Catalog/Model/Category/Api/Mage_Catalog_Model_Category_Api_V2#create($parentId, $categoryData, $store)
	 */
	public function create($parentId, $categoryData, $store = null) {
		// Attribute "available_sort_by" needed in $categoryData
		if (!property_exists($categoryData, 'available_sort_by')) {
			$categoryData->available_sort_by = 'position,name,price';
		}

		// Attribute "available_sort_by" is defined as ArrayOfString but must be a commaseperated string
		if (is_array($categoryData->available_sort_by)) {
			$categoryData->available_sort_by = implode(',', $categoryData->available_sort_by);
		}

		// Attribute "default_sort_by" needed in $categoryData
		if (!property_exists($categoryData, 'default_sort_by')) {
			$categoryData->default_sort_by = 'price';
		}

		// Attribute "is_active" needed in $categoryData
		if (!property_exists($categoryData, 'is_active')) {
			$categoryData->is_active = 1;
		}

		// Attribute "include_in_menu" needed in $categoryData since 1.5
		if (!property_exists($categoryData, 'include_in_menu')) {
			$categoryData->include_in_menu = 1;
		}

		return parent::create($parentId, $categoryData, $store);
	}

	/**
	 * (non-PHPdoc)
	 * @see app/code/core/Mage/Catalog/Model/Category/Api/Mage_Catalog_Model_Category_Api_V2#update($categoryId, $categoryData, $store)
	 */
	public function update($categoryId, $categoryData, $store = null) {
		// The Category is here needed to get default values if they are not given
		$category = $this->_initCategory($categoryId, $store);

		// Attribute "available_sort_by" needed in $categoryData
		if (!property_exists($categoryData, 'available_sort_by')) {
			$categoryData->available_sort_by = $category->getAvailableSortBy();
		}

		// Attribute "available_sort_by" is defined as ArrayOfString but must be a commaseperated string
		if (is_array($categoryData->available_sort_by)) {
			$categoryData->available_sort_by = implode(',', $categoryData->available_sort_by);
		}
		if (substr_count($categoryData->available_sort_by, 'price') <= 0) {
			$categoryData->available_sort_by = 'position,name,price';
		}

		// Attribute "default_sort_by" needed in $categoryData
		if (!property_exists($categoryData, 'default_sort_by')) {
			$categoryData->default_sort_by = 'price';
		}

		// Attribute "include_in_menu" needed in $categoryData since 1.5
		if (!property_exists($categoryData, 'include_in_menu')) {
			$categoryData->include_in_menu = 1;
		}

		return parent::update($categoryId, $categoryData, $store);
	}

}
