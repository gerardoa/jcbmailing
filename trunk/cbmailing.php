<?php
/**
* @version 2.3.4J1.5N
* @package CB Mailing list
* @copyright (c) 2006-2008 - Erik Happaerts  [erik@happaerts.be] / Guus Koning [guus.koning@hccnet.nl]
* @copyright (c) 2007-2009 - Mark Bradley (OSPS Ltd)
* @based on Mambo admin.massmail.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted Access' );

/* J1.0
$UElanguagePath=$mainframe->getCfg( 'absolute_path' ).'/components/com_cbmailing/language';
if (file_exists($UElanguagePath.'/'.$mosConfig_lang.'/'.$mosConfig_lang.'.php')) {
  include_once($UElanguagePath.'/'.$mosConfig_lang.'/'.$mosConfig_lang.'.php');
} else include_once($UElanguagePath.'/default_language/default_language.php');
*/

//include_once( $mainframe->getCfg( 'absolute_path' ) . '/administrator/components/com_cbmailing/cbmailing.class.php' );	// J1.0
include_once( JPATH_ROOT . DS.'administrator'.DS.'components'.DS.'com_cbmailing'.DS.'cbmailing.class.php' );

require_once( $mainframe->getPath( 'front_html' ) );

switch ($task) {
	case 'send':
		$cbm = new cbmailings();
		$cbm->sendMail();
		break;

	case 'cancel':
		$url = 'index.php';
		// mosRedirect( $url );	// J1.0
		$app = &JFactory::getApplication();	// J1.5
		$app->redirect( $url );	// J.15

		break;

	case 'mailing':
	default:
		$cbm = new cbmailings();
		$cbm->messageForm( $option );
		break;
}


?>
