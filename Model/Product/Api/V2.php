<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product api V2
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Delight_Delightapi_Model_Product_Api_V2 extends Mage_Catalog_Model_Product_Api_V2
{

	/**
	 * Retrieve product info
	 *
	 * @param int|string $productId
	 * @param string|int $store
	 * @param stdClass $attributes
	 * @return array
	 */
	public function info($productId, $store = null, $attributes = null, $identifierType = null) {
		if (property_exists($attributes, 'attributes') && is_array($attributes->attributes) && in_array('tier_prices', $attributes->attributes)) {
			$attributes->attributes[] = 'tier_price';
		}
		$product = parent::info($productId, $store, $attributes, $identifierType);
		if (array_key_exists('tier_price', $product)) {
			if (Mage::helper('delightapi')->isPrevious15()) {
				$tierPrices = $product['tier_price'];
				$product['tier_price'] = array();
				if (is_array($tierPrices)) {
					foreach ($tierPrices as $tierPrice) {
						$row = array();
						$row['customer_group_id'] = (empty($tierPrice['all_groups']) ? $tierPrice['cust_group'] : 'all' );
						$row['website']           = ($tierPrice['website_id'] ? Mage::app()->getWebsite($tierPrice['website_id'])->getCode() : 'all');
						$row['qty']               = $tierPrice['price_qty'];
						$row['price']             = $tierPrice['price'];
						$product['tier_price'][]  = $row;
					}
				}
			}
			$product['tier_prices'] = $product['tier_price'];
		}
		return $product;
	}

	/**
	 * Create new product.
	 *
	 * @param string $type
	 * @param int $set
	 * @param array $productData
	 * @return int
	 */
	public function create($type, $set, $sku, $productData) {
		if (Mage::helper('delightapi')->isPrevious15()) {
			if (property_exists($productData, 'tier_price')) {
				$productData->tier_price = $this->_prepareTierPrices($productData->tier_price);
			}
		}
		if (property_exists($productData, 'tier_prices')) {
			if (Mage::helper('delightapi')->isPrevious15()) {
				$productData->tier_price = $this->_prepareTierPrices($productData->tier_prices);
			}
			$productData->tier_price = $productData->tier_prices;
		}
		if (!Mage::helper('delightapi')->isPrevious15()) {
			if (property_exists($productData, 'category_ids')) {
				$productData->categories = $productData->category_ids;
			}
		}
		return parent::create($type, $set, $sku, $productData);
	}

	/**
	 * Update product data
	 *
	 * @param int|string $productId
	 * @param array $productData
	 * @param string|int $store
	 * @return boolean
	 */
	public function update($productId, $productData, $store = null, $identifierType = null) {
		if (Mage::helper('delightapi')->isPrevious15()) {
			if (property_exists($productData, 'tier_price')) {
				$productData->tier_price = $this->_prepareTierPrices($productData->tier_price);
			}
		}
		if (property_exists($productData, 'tier_prices')) {
			if (Mage::helper('delightapi')->isPrevious15()) {
				$productData->tier_price = $this->_prepareTierPrices($productData->tier_prices);
			}
			$productData->tier_price = $productData->tier_prices;
		}
		if (!Mage::helper('delightapi')->isPrevious15()) {
			if (property_exists($productData, 'category_ids')) {
				$productData->categories = $productData->category_ids;
			}
		}
		return parent::update($productId, $productData, $store, $identifierType);
	}

	/**
	 * Parse tier_price and tier_prices request parameters and prepare all data so magento could validate them
	 *
	 * @param array $tierPrices
	 * @return array()
	 */
	protected function _prepareTierPrices($tierPrices=null) {
		if (!is_array($tierPrices)) {
			return array();
		}
		$prices = array();
		foreach ($tierPrices as $tierPrice) {
			if (!is_object($tierPrice)
			|| !isset($tierPrice->qty)
			|| !isset($tierPrice->price)) {
				$this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Tier Prices'));
			}

			if (!isset($tierPrice->website) || $tierPrice->website == 'all') {
				$tierPrice->website = 0;
			} else {
				try {
					$tierPrice->website = Mage::app()->getWebsite($tierPrice->website)->getId();
				} catch (Mage_Core_Exception $e) {
					$tierPrice->website = 0;
				}
			}

			if (intval($tierPrice->website) > 0 && !in_array($tierPrice->website, $product->getWebsiteIds())) {
				$this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid tier prices. Product is not associated to the requested website.'));
			}

			if (!isset($tierPrice->customer_group_id)) {
				$tierPrice->customer_group_id = 'all';
			}

			if ($tierPrice->customer_group_id == 'all') {
				$tierPrice->customer_group_id = Mage_Customer_Model_Group::CUST_GROUP_ALL;
			}

			$prices[] = array(
                'website_id' => $tierPrice->website,
                'cust_group' => $tierPrice->customer_group_id,
                'price_qty'  => $tierPrice->qty,
                'price'      => $tierPrice->price
			);
		}
		return $prices;
	}
}
