<?php
/**
* @version 1.1
* @package CB Mailing list
* @copyright (c) 2006 - Erik Happaerts  [erik@happaerts.be] / Guus Koning [guus.koning@hccnet.nl]
* @based on Mambo admin.massmail.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

/** ensure this file is being included by a parent file */
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );


require_once( $mainframe->getPath( 'admin_html' ) );

switch ($task) {
	case 'send':
		sendMail();
		break;

	case 'cancel':
		mosRedirect( 'index2.php' );
		break;

	default:
		messageForm( $option );
		break;
}

function messageForm( $option ) {
	global $database, $acl;

	// get list of groups
	$query = "SELECT listid AS value, title AS text FROM #__comprofiler_lists WHERE published=1 ORDER BY ordering";
	$database->setQuery( $query );
	$users = $database->loadObjectList();
	$lists['gid'] = mosHTML::selectList( $users, 'mm_group', 'size="10"', 'value', 'text', 0 );

	HTML_cbmailing::messageForm( $lists, $option );
}

function sendMail() {
	global $database, $my, $acl;
	global $mosConfig_sitename;
	global $mosConfig_mailfrom, $mosConfig_fromname;

	$mode				= mosGetParam( $_POST, 'mm_mode', 0 );
	$subject			= mosGetParam( $_POST, 'mm_subject', '' );
	$gou				= mosGetParam( $_POST, 'mm_group', NULL );
	$recurse			= mosGetParam( $_POST, 'mm_recurse', 'NO_RECURSE' );

	$query = "SELECT filterfields FROM #__comprofiler_lists WHERE listid = $gou";
	$database->setQuery( $query );
	$filterby = $database->loadResult();

	// pulls message information either in text or html format
	if ( $mode ) {
		$message_body	= $_POST['mm_message'];
	} else {
		// automatically removes html formatting
		$message_body	= mosGetParam( $_POST, 'mm_message', '' );
	}
	$message_body 		= stripslashes( $message_body );
	
	if (!$message_body || !$subject || $gou === null) {
		mosRedirect( 'index2.php?option=com_cbmailing&mosmsg=Please fill in the form correctly' );
	}

	// Get sending email address
	$query = "SELECT email FROM #__users WHERE id='$my->id'";
	$database->setQuery( $query );
	$my->email = $database->loadResult();

	// Get all users email
	$query = "SELECT email FROM #__users u, #__comprofiler ue WHERE u.id=ue.id AND u.block!=1 and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1";

	$selection = utf8RawUrlDecode(substr($filterby,1));
	if (!$selection === null) {
		$query = $query . " AND " . $selection;
	}

	$database->setQuery( $query );
	$rows = $database->loadObjectList();

	// Build e-mail message format
	$message_header 	= sprintf( _MASSMAIL_MESSAGE, $mosConfig_sitename );
	// $message 			= $message_header . $message_body;
	$message 			= $message_body;
	$subject 			= $mosConfig_sitename. ' / '. stripslashes( $subject);

	//Send email
	foreach ($rows as $row) {
		mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $row->email, $subject, $message, $mode );
	}
	$msg = 'Email send to '. count( $rows ) .' users';

	mosRedirect( 'index2.php?option=com_cbmailing', $msg );
}

function utf8RawUrlDecode ($source) {
	$decodedStr = ''; 
	$pos = 0; 
	$len = strlen ($source); 
	while ($pos < $len) { 
		$charAt = substr ($source, $pos, 1); 
		if ($charAt=='%') { 
			$pos++; 
			$charAt = substr ($source, $pos, 1); 
			if ($charAt=='u') { // we got a unicode character 
				$pos++; 
				$unicodeHexVal = substr ($source, $pos, 4); 
				$unicode = hexdec ($unicodeHexVal); 
				$entity = "&#". $unicode . ';'; 
				$decodedStr .= utf8_encode ($entity); 
				$pos += 4; 
			} else { // we have an escaped ascii character 
				$hexVal = substr ($source, $pos, 2); 
				$decodedStr .= chr (hexdec ($hexVal)); 
				$pos += 2; 
			} 
		} else { 
			$decodedStr .= $charAt; 
			$pos++; 
		} 
	} 
	return $decodedStr;
}
?>
