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

	case 'mailing':
		messageForm( $option );
		break;

	case 'permissions':
		permissionsForm( $option );
		break;

	case 'addPerm':
		addPermission( $option );
		break;

	case 'delPerm':
		delPermission( $option );
		break;

	default:
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

function permissionsForm( $option ) {
	global $database, $acl;

	// get list of groups as destiantions
	$query = "SELECT listid AS value, title AS text FROM #__comprofiler_lists WHERE published=1 ORDER BY ordering";
	$database->setQuery( $query );
	$lists['tolists'] = $database->loadObjectList();
	$lists['gidto'] = mosHTML::selectList( $lists['tolists'], 'mm_togroup', 'size="10"', 'value', 'text', 0 );

	// get list of groups for those allowed to send (includes unpublished lists)
	$query = "SELECT listid AS value, title AS text FROM #__comprofiler_lists ORDER BY ordering";
	$database->setQuery( $query );
	$lists['fromlists'] = $database->loadObjectList();
	$lists['gidfrom'] = mosHTML::selectList( $lists['fromlists'], 'mm_fromgroup', 'size="10"', 'value', 'text', 0 );

	// get list of existing permissions
	$query = "SELECT a.id as id, b1.title as totitle, b2.title as fromtitle 
				FROM #__cbmailing_permissions as a, #__comprofiler_lists as b1, #__comprofiler_lists as b2 
				WHERE a.toid=b1.listid AND a.fromid=b2.listid;";
	$database->setQuery( $query );
	$lists['permissions'] = $database->loadObjectList();

	HTML_cbmailing::permissionsForm( $lists, $option );
}

function addPermission( $option ) {
	global $database;

	$fromgroup			= mosGetParam( $_POST, 'mm_fromgroup', NULL );
	$togroup			= mosGetParam( $_POST, 'mm_togroup', NULL );

	// Only try to do something if both groups are set
	if ($togroup && $fromgroup) {
		// First check if there is already such a permission, if so, don't add it in and let the user know they tried this
		$query = "SELECT id 
					FROM #__cbmailing_permissions 
					WHERE toid='$togroup' AND fromid='$fromgroup';";
		$database->setQuery( $query );
		$results = $database->loadObjectList();

		if (count($results) == 0) {
			$query = "INSERT INTO #__cbmailing_permissions (id, toid, fromid) 
						VALUES (NULL, $togroup, $fromgroup);";
			$database->setQuery( $query );
			if ( $database->query() === false ) {
				$msg = $database->getErrorMsg();
			} else {
				$msg = 'Permission added';
			}
		} else {
			$msg = "You already have a permission between those two groups";
		}
	} else {
		// let the user know what they did wrong
		$msg = "";
		if ($fromgroup == NULL) {
			$msg .= " FROM group not set;";
		}
		if ($togroup == NULL) {
			$msg .= " TO group not set";
		}
	}

	mosRedirect( 'index2.php?option=com_cbmailing&task=permissions', $msg );
}

function delPermission( $option ) {
	global $database;

	$ids				= mosGetParam( $_POST, 'ids', NULL );
	$deletedCount = 0;
	foreach ($ids as $thisid) {
		$query = "DELETE FROM #__cbmailing_permissions 
					WHERE id='". $thisid ."';";
		$database->setQuery( $query );
		if ( $database->query() === false ) {
			$msg = $database->getErrorMsg();
		} else {
			$deletedCount++;
		}
	}

	if ($deletedCount > 0) {
		$msg = $deletedCount .' permission'. ($deletedCount == 1 ? '' : 's') .' deleted';
	}

	mosRedirect( 'index2.php?option=com_cbmailing&task=permissions', $msg );
}

function sendMail() {
	global $database, $my, $acl;
	// MRCB 20070619 2233 - added in mosConfig_absolute_path
	global $mosConfig_sitename, $mosConfig_absolute_path;
	global $mosConfig_mailfrom, $mosConfig_fromname;

	$mode				= mosGetParam( $_POST, 'mm_mode', 0 );
	$subject			= mosGetParam( $_POST, 'mm_subject', '' );
	$gou				= mosGetParam( $_POST, 'mm_group', NULL );
//	$recurse			= mosGetParam( $_POST, 'mm_recurse', 'NO_RECURSE' );

	// MRCB 20070619 2233 - added from here......
	$attachment = NULL;
	if (file_exists($_FILES['mm_attach']['tmp_name'])) {
		$uploadfile = $mosConfig_absolute_path . "/uploads/". basename($_FILES['mm_attach']['name']);
		if (move_uploaded_file($_FILES['mm_attach']['tmp_name'], $uploadfile)) {
			$attachment = $uploadfile;
		}
	}
	// MRCB 20070619 2233 - .............to here

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
		$problem = "";
		if (!$message_body) $problem .= " No message body;";
		if (!$subject) $problem .= " No subject;";
		if ($gou === null) $problem .= "No group selected;";
		mosRedirect( 'index2.php?option=com_cbmailing&mosmsg=Please fill in the form correctly:'. $problem );
	}

	// Get sending email address
	$query = "SELECT email FROM #__users WHERE id='$my->id'";
	$database->setQuery( $query );
	$my->email = $database->loadResult();

	// Get all users email
	$query = "SELECT email FROM #__users u, #__comprofiler ue WHERE u.id=ue.id AND u.block!=1 and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1";

	$selection = utf8RawUrlDecode(substr($filterby,1));
	// MRCB 20070618 2110 change from "if (!$selection === null) {" to.....
	if ($selection != "") {
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
		// MRCB 20070619 0840 - the two NULLs are cc and bcc
		// mosMail params are: $from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL
		//$xxxx .= $row->email. ": ". 
		mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $row->email, $subject, $message, $mode, NULL, NULL, $attachment );
	}

	// MRCB 20070619 2235 - added in this to clean up the uploaded file (if found) after it's been sent out.
	if (file_exists($uploadfile)) {
		@unlink($uploadfile);
	}
	// MRCB 20070618 2103 - changed spelling of send to sent
	$msg = 'Email sent to '. count( $rows ) .' user'. (count ( $rows ) == 1 ? ' ' : 's ');

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
