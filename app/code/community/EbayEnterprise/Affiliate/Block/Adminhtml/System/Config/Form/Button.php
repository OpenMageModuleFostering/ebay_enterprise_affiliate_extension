<?php

class EbayEnterprise_Affiliate_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('eems_affiliate/system/config/button.phtml');
	}

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		return $this->_toHtml();
	}

	public function getAjaxUninstallUrl()
	{
		return Mage::helper('adminhtml')->getUrl('adminhtml/eemsAffiliate/uninstall');
	}

	public function getButtonHtml()
	{
		$button = $this->getLayout()->createBlock('adminhtml/widget_button')->setData(
			array(
				'id' => 'eems_affiliate_uninstall',
				'label' => $this->helper('adminhtml')->__('Uninstall'),
				'onclick' => 'javascript:uninstall(); return false;',
			));

		return $button->toHtml();
	}
}
