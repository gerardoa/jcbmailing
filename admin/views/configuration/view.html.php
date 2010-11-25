<?php
jimport('joomla.application.component.view');

class CbmailingViewConfiguration extends JView
{
	function display( $tpl = null )
	{
		JToolBarHelper::save('saveConfig');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		$this->configureForm();
		parent::display( $tpl );
	}

	function configureForm() {
		$params = JComponentHelper::getParams('com_cbmailing');
		$cbMailingConfig['feAllowAtt']=$params->get('cbMailingConfig_feAllowAtt');
		$cbMailingConfig['mmFromAddr']=$params->get('cbMailingConfig_mmFromAddr');
		$cbMailingConfig['mmFromDesc']=$params->get('cbMailingConfig_mmFromDesc');
		$cbMailingConfig['mmReplyToAddr']=$params->get('cbMailingConfig_mmReplyToAddr');
		$cbMailingConfig['mmReplyToDesc']=$params->get('cbMailingConfig_mmReplyToDesc');
		$cbMailingConfig['mmToAddr']=$params->get('cbMailingConfig_mmToAddr');
		$cbMailingConfig['mmToDesc']=$params->get('cbMailingConfig_mmToDesc');
		$cbMailingConfig['mmBCCAddr']=$params->get('cbMailingConfig_mmBCCAddr');
		$cbMailingConfig['mmBCCDesc']=$params->get('cbMailingConfig_mmBCCDesc');
		$cbMailingConfig['signature']=$params->get('cbMailingConfig_signature');
		$cbMailingConfig['debugFromAddr']=$params->get('cbMailingConfig_debugFromAddr');
		$cbMailingConfig['debugFromDesc']=$params->get('cbMailingConfig_debugFromDesc');
		$cbMailingConfig['debugToAddr']=$params->get('cbMailingConfig_debugToAddr');
		$cbMailingConfig['debugToDesc']=$params->get('cbMailingConfig_debugToDesc');
		$cbMailingConfig['debugETitle']=$params->get('cbMailingConfig_debugETitle');
		$this->assignRef('cbMailingConfig', $cbMailingConfig);
	}
}