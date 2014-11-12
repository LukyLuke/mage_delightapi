<?php
/**
 * DelightAPI by delight software gmbh for Magento
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
 * Do not edit or add to this file if you wish to upgrade this Module to newer
 * versions in the future.
 *
 * @category   Custom
 * @package    Mage_Delightapi
 * @copyright  Copyright (c) 2010 delight software gmbh (http://www.delightsoftware.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper for translation and perhaps other actions
 *
 * @category   Custom
 * @package    Mage_Delightapi
 * @author     delight software gmbh <info@delightsoftware.com>
 */
class Delight_Delightapi_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isPrevious15() {
		return (version_compare(Mage::getVersion(), '1.5.0.0') <= 0);
	}
	public function isPrevious16() {
		return (version_compare(Mage::getVersion(), '1.6.0.0') <= 0);
	}
	public function isPrevious17() {
		return (version_compare(Mage::getVersion(), '1.7.0.0') <= 0);
	}
	public function isPrevious18() {
		return (version_compare(Mage::getVersion(), '1.8.0.0') <= 0);
	}
	public function isPrevious19() {
		return (version_compare(Mage::getVersion(), '1.9.0.0') <= 0);
	}

}
