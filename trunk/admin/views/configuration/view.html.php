<?php
jimport('joomla.application.component.view');

class CbmailingViewConfiguration extends JView
{
	function display( $tpl = null )
	{
		JToolBarHelper::save('saveConfig');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
				
		parent::display( $tpl );		
	}
	
function configureForm( $option ) {

	// $lists = NULL;
	$cbm = new cbmailings(true);
	$cbm->readConfig();
	$configWritable = is_writable( $cbm->configPath() ) || ! file_exists( $cbm->configPath() ) ;
	HTML_cbmailing::configureForm( $cbm->cbMailingConfig, $configWritable, $option );
}
}