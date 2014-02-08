<?php
class Delight_Delightapi_Model_Sales_Order_Api extends Mage_Sales_Model_Order_Api {

	public function items($filters = null) {
		$result = parent::items($filters);
		$websiteCache = array();

		foreach ($result as &$item) {
			$storeId = $item['store_id'];
			$k = 's'.$storeId;
			if (!in_array($k, $websiteCache)) {
				$website = Mage::app()->getStore($storeId)->getWebsite();
				$websiteCache[$k] = array($website->getId(), $website->getCode());
			}
			$item['website_id'] = $websiteCache[$k][0];
			$item['website'] = $websiteCache[$k][1];

			$taxes = $this->_getOrderTax($this->_initOrder($item['increment_id']));
			$item['tax_percent'] = $taxes->percent;
			$item['base_tax_percent'] = $taxes->percent;
			$item['tax_list'] = $taxes->rates;
		}
		unset($websiteCache);

		return $result;
	}

	public function info($orderIncrementId) {
		$order = $this->_initOrder($orderIncrementId);
		$taxes = $this->_getOrderTax($order);

		// Set website and website_id attributes
		$website = Mage::app()->getStore($order->getStoreId())->getWebsite();

		// Need to load the addresses and format them so "prefix", "suffix", "street1" and "street2" is loaded
		$shipping = $order->getShippingAddress()->getFormated();
		$billing = $order->getBillingAddress()->getFormated();

		// call the original script
		$result = parent::info($orderIncrementId);
		$result['website_id'] = $website->getId();
		$result['website'] = $website->getCode();
		$result['tax_percent'] = $taxes->percent;
		$result['base_tax_percent'] = $taxes->percent;
		$result['tax_list'] = $taxes->rates;
		return $result;
	}

	protected function _getOrderTax(Mage_Sales_Model_Order $order) {
		// Get Taxes and calculate the percent-value over all taxes
		$rates = $order->getFullTaxInfo();
		$taxList = array();
		$tax_percent = 0.0;
		if (is_array($rates)) {
			$tax_percent = 1.0;
			foreach ($rates as $rate) {
				if (is_array($rate) && array_key_exists('rates', $rate) && is_array($rate['rates'])) {
					$rt = 0.0;
					$group = array(
						'percent' => 0.0,
						'tax_list' => array(),
						'amount' => $rate['amount'],
						'base_amount' => $rate['base_amount'],
						'base_real_amount' => $rate['base_real_amount']
					);
					foreach ($rate['rates'] as $r) {
						if (is_array($r) && array_key_exists('percent', $r)) {
							$rt += (float)$r['percent'];
							$group['tax_list'][] = array(
								'code' => $r['code'],
								'name' => $r['title'],
								'percent' => number_format((float)$r['percent'], 4, '.', '')
							);
						}
					}
					$tax_percent = $tax_percent * (1.0 + ($rt / 100));
					$group['percent'] = number_format((float)$rt, 4, '.', '');
					$taxList[] = $group;
				}
			}
			$tax_percent -= 1.0;
		}
		$tax_percent = number_format($tax_percent*100.0, 4, '.', '');

		return (object)array('percent'=>$tax_percent, 'rates'=>$taxList);
	}
}