<?php

class Delight_Delightapi_Model_Product_Media_Api_V2 extends Mage_Catalog_Model_Product_Attribute_Media_Api_V2 {

	/**
	 * (non-PHPdoc)
	 * @see app/code/core/Mage/Catalog/Model/Product/Attribute/Media/Api/Mage_Catalog_Model_Product_Attribute_Media_Api_V2#update($productId, $file, $data, $store, $identifierType)
	 */
	public function update($productId, $file, $data, $store = null, $identifierType = null) {
		if (Mage::helper('delightapi')->isPrevious15()) {
			// BEGIN: Taken from parent
			$product = $this->_initProduct($productId, $store, $identifierType);
			$gallery = $this->_getGalleryAttribute($product);
			if (!$gallery->getBackend()->getImage($product, $file)) {
				$this->_fault('not_exists');
			}
			// END: Taken from parent

			// The Original Code does not upgrade the Image itselfs
			if (isset($data->file->mime) && isset($data->file->content)) {
				if (!isset($this->_mimeTypes[$data->file->mime])) {
					$this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid image type.'));
				}

				$fileContent = @base64_decode($data->file->content, true);
				if (!$fileContent) {
					$this->_fault('data_invalid', Mage::helper('catalog')->__('Image content is not valid base64 data.'));
				}

				unset($data->file->content);

				$ioAdapter = new Varien_Io_File();
				try {
					$fileName = Mage::getBaseDir('media'). DS . 'catalog' . DS . 'product' . $file;
					$ioAdapter->open(array('path'=>dirname($fileName)));
					$ioAdapter->write(basename($fileName), $fileContent, 0666);
				} catch(Exception $e) {
					$this->_fault('not_created', Mage::helper('catalog')->__('Can\'t create image.'));
				}
			}
		}

		// Call the Original Code
		return parent::update($productId, $file, $data, $store, $identifierType);
	}
}