<?php
/**
* @version 2.3.4J1.5N
* @package CB Mailing list
* @copyright (c) 2006-2008 - Erik Happaerts  [erik@happaerts.be] / Guus Koning [guus.koning@hccnet.nl]
* @copyright (c) 2007-2009 - Mark Bradley (OSPS Ltd)
* @based on Mambo admin.massmail.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( 'toolbar_html' ) );
require_once( $mainframe->getPath( 'toolbar_default' ) );

/*
global $mosConfig_lang;
$UElanguagePath=$mainframe->getCfg( 'absolute_path' ).'/components/com_cbmailing/language';
if (file_exists($UElanguagePath.'/'.$mosConfig_lang.'/'.$mosConfig_lang.'.php')) {
  include_once($UElanguagePath.'/'.$mosConfig_lang.'/'.$mosConfig_lang.'.php');
} else include_once($UElanguagePath.'/default_language/default_language.php');
*/

switch ( $task ) {
	case 'send':
	case 'cancel':
	case 'mailing':
		TOOLBAR_cbmailing::_DEFAULT();
		break;

	case 'configure':
		TOOLBAR_cbmailing::_CONFIGURATION();
		break;

	case 'permissions':
		TOOLBAR_cbmailing::_PERMISSIONS();
		break;

	default:
		break;
}
?>