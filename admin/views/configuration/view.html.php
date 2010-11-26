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

		$cbMailingConfig['allAddr'] = $params->get('cbMailingConfig_allAddr');
		$cbMailingConfig['feAllowAtt'] = $params->get('cbMailingConfig_feAllowAtt');
		$cbMailingConfig['feAllowHTML'] = $params->get('cbMailingConfig_feAllowHTML');
		$cbMailingConfig['feAllowSigOver'] = $params->get('cbMailingConfig_feAllowSigOver');
		$cbMailingConfig['mmMethod'] = $params->get('cbMailingConfig_mmMethod');
		$cbMailingConfig['mmFrom'] = $params->get('cbMailingConfig_mmFrom');
		$cbMailingConfig['mmFromAddr'] = $params->get('cbMailingConfig_mmFromAddr');
		$cbMailingConfig['mmFromDesc'] = $params->get('cbMailingConfig_mmFromDesc');
		$cbMailingConfig['mmReplyTo'] = $params->get('cbMailingConfig_mmReplyTo');
		$cbMailingConfig['mmReplyToAddr'] = $params->get('cbMailingConfig_mmReplyToAddr');
		$cbMailingConfig['mmReplyToDesc'] = $params->get('cbMailingConfig_mmReplyToDesc');
		$cbMailingConfig['mmTo'] = $params->get('cbMailingConfig_mmTo');
		$cbMailingConfig['mmToAddr'] = $params->get('cbMailingConfig_mmToAddr');
		$cbMailingConfig['mmToDesc'] = $params->get('cbMailingConfig_mmToDesc');
		$cbMailingConfig['mmBCC'] = $params->get('cbMailingConfig_mmBCC');
		$cbMailingConfig['mmBCCAddr'] = $params->get('cbMailingConfig_mmBCCAddr');
		$cbMailingConfig['mmBCCDesc'] = $params->get('cbMailingConfig_mmBCCDesc');
		$cbMailingConfig['signature'] = $params->get('cbMailingConfig_signature');
		$cbMailingConfig['incBlocked'] = $params->get('cbMailingConfig_incBlocked');

		$cbMailingConfig['debug'] = $params->get('cbMailingConfig_debug');
		$cbMailingConfig['debugFromAddr'] = $params->get('cbMailingConfig_debugFromAddr');
		$cbMailingConfig['debugFromDesc'] = $params->get('cbMailingConfig_debugFromDesc');
		$cbMailingConfig['debugToAddr'] = $params->get('cbMailingConfig_debugToAddr');
		$cbMailingConfig['debugToDesc'] = $params->get('cbMailingConfig_debugToDesc');
		$cbMailingConfig['debugETitle'] = $params->get('cbMailingConfig_debugETitle');

		$this->assignRef('cbMailingConfig', $cbMailingConfig);
	}
}