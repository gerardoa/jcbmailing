<?php

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

require_once( $mainframe->getPath( 'toolbar_html' ) );
require_once( $mainframe->getPath( 'toolbar_default' ) );

switch ( $task ) {
	case 'send':
	case 'cancel':
	case 'mailing':
		TOOLBAR_cbmailing::_DEFAULT();
		break;

	case 'permissions':
		TOOLBAR_cbmailing::_PERMISSIONS();
		break;

	default:
		break;
}
?>