<?php

class EbayEnterprise_Affiliate_Adminhtml_EemsAffiliateController extends Mage_Adminhtml_Controller_Action
{
	const AFFILIATE_ID = 'commissioning_category';
	const DEFAULT_SCONFIG_FILENAME = 'cache.cfg';
	const PACKAGE_CHANNEL = 'community';
	const PACKAGE_NAME = 'eBay_Enterprise_Affiliate_Extension';

	protected $_config;
	protected $_sconfig;

	public function uninstallAction()
	{
		// Remove commissioning category
		$setup = new Mage_Eav_Model_Entity_Setup('eems_affiliate_setup');
		$setup->startSetup();

		$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');

		Mage::log(array('uninstallAction', get_class($objCatalogEavSetup)));
		$attributeExists = (bool) $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, self::AFFILIATE_ID);
		if ($attributeExists) {
			$setup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, self::AFFILIATE_ID);
		}

		$setup->endSetup();

		// Uninstall extension
		Mage_Connect_Command::registerCommands(); // Must run or next line will fail
		$installer = Mage_Connect_Command::getInstance('uninstall');
		$installer->setFrontendObject(Mage_Connect_Frontend::getInstance('CLI'));
		$installer->setSconfig($this->getSingleConfig());

		$installer->doUninstall('uninstall', array(), array(self::PACKAGE_CHANNEL, self::PACKAGE_NAME));

		// Clear cache
		Mage::app()->cleanCache();

		// Send message to admin
		Mage::getSingleton('core/session')->addSuccess('Package ' . self::PACKAGE_CHANNEL . '/' . self::PACKAGE_NAME . ' successfully deleted');
	}

	/**
	 * Retrieve object of config and set it to Mage_Connect_Command
	 *
	 * @return Mage_Connect_Config
	 */
	public function getConfig()
	{
		if (!$this->_config) {
			$this->_config = new Mage_Connect_Config();
			$ftp=$this->_config->__get('remote_config');
			if(!empty($ftp)){
				$packager = new Mage_Connect_Packager();
				list($cache, $config, $ftpObj) = $packager->getRemoteConf($ftp);
				$this->_config=$config;
				$this->_sconfig=$cache;
			}
			$this->_config->magento_root = Mage::getBaseDir('base');
		    Mage_Connect_Command::setConfigObject($this->_config);
		}
		return $this->_config;
	}

	/**
	 * Retrieve object of single config and set it to Mage_Connect_Command
	 *
	 * @param bool $reload
	 * @return Mage_Connect_Singleconfig
	 */
	public function getSingleConfig($reload = false)
	{
		if(!$this->_sconfig || $reload) {
			$this->_sconfig = new Mage_Connect_Singleconfig(
				$this->getConfig()->magento_root . DIRECTORY_SEPARATOR
				. $this->getConfig()->downloader_path . DIRECTORY_SEPARATOR
				. self::DEFAULT_SCONFIG_FILENAME
			);
		}
		Mage_Connect_Command::setSconfig($this->_sconfig);
		return $this->_sconfig;

	}
}
