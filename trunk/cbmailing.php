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

define("_CB_MAILING_NOMESSAGEBODY",		"No message body;");
define("_CB_MAILING_NOSUBJECT",			"No subject;");
define("_CB_MAILING_NOGROUP",			"No group;");
define("_CB_MAILING_FILLFORMCORRECTLY",	"Please fill in the form correctly:");
define("_CB_MAILING_NOPERMISSION",		"You are not allowed to send to that group");
define("_CB_MAILING_EMAILSENTTO",		"E-mail sent to");
define("_CB_MAILING_USERS",				"users");
define("_CB_MAILING_USER",				"user");
define("_CB_MAILING_FILLINSUBJECT",		"Please fill in the subject");
define("_CB_MAILING_SELECTAGROUP",		"Please select a group");
define("_CB_MAILING_FILLINMESSAGE",		"Please fill in the message");
define("_CB_MAILING_TITLE",				"CBMailing");
define("_CB_MAILING_SELECTGROUPTEXT",	"Select the group to whom you wish to send the e-mail, then complete the other entries as required");
define("_CB_MAILING_GROUPTEXT",			"Group:");
define("_CB_MAILING_HMTLMODETEXT",		"Send in HTML mode:");
define("_CB_MAILING_SUBJECTTEXT",		"Subject:");
define("_CB_MAILING_ATTACHFILETEXT",	"Attach a file:");
define("_CB_MAILING_MESSAGETEXT",		"Message:");
define("_CB_MAILING_SENDEMAILTEXT",		"Send E-mail");

require_once( $mainframe->getPath( 'front_html' ) );

switch ($task) {
	case 'send':
		sendMail();
		break;

	case 'cancel':
		mosRedirect( '/index.php' );
		break;

	case 'mailing':
	default:
		messageForm( $option );
		break;
}

function messageForm( $option ) {
	global $database, $my;

	// Need logic in here ot:
	// 1 - are there any permissions? If no bail out
	// 2 - is the user a member of any of the FROM groups? If no bail out
	// 3 - build a list of all FROM groups of which user is a member
	// 4 - build unique list of all TO groups permitted from FROM groups of which users is a member
	// 5 - display that list

	$allowedToSend = false;

	listOfOkayToGroups( $toList );
	$allowedToSend = (count( $toList ) != 0);
	if  ( !$allowedToSend ) {
		// No permissions apply to this user - cannot send
	} else {
		// Now have to create an HTML select list of TO groups based on the built list
		$query = "SELECT listid AS value, title AS text FROM #__comprofiler_lists WHERE published=1 AND (";
		$listids = "";
		foreach (array_keys($toList) as $thisList) {
			$listids .= ($listids == "" ? "" : " OR " ) ." listid='$thisList'";
		}
		$query .= $listids .") ORDER BY ordering";
		$database->setQuery( $query );
		$users = $database->loadObjectList();
		$lists['gid'] = mosHTML::selectList( $users, 'mm_group', 'size="10"', 'value', 'text', 0 );
	}

	if ( $allowedToSend ) {
		HTML_cbmailing::messageForm( $lists, $option );
	} else {
		// Some suitable "you're not allowed to e-mail groups" type message
		HTML_cbmailing::errorMessage( "You are not permitted to send e-mail to user groups - sorry", $option );
	}
}

function sendMail() {
	global $database, $my, $acl;
	// MRCB 20070619 2233 - added in mosConfig_absolute_path
	global $mosConfig_sitename, $mosConfig_absolute_path;
	//global $mosConfig_mailfrom, $mosConfig_fromname;

	// For security reasons, we should check that the user is permitted to send to this list

	$mode				= mosGetParam( $_POST, 'mm_mode', 0 );
	$subject			= mosGetParam( $_POST, 'mm_subject', '' );
	$gou				= mosGetParam( $_POST, 'mm_group', NULL );
//	$recurse			= mosGetParam( $_POST, 'mm_recurse', 'NO_RECURSE' );

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
		//if (!$message_body) $problem .= " No message body;";
		if (!$message_body) $problem .= " ". _CB_MAILING_NOMESSAGEBODY;
		//if (!$subject) $problem .= " No subject;";
		if (!$subject) $problem .= " ". _CB_MAILING_NOSUBJECT;
		// if ($gou === null) $problem .= "No group selected;";
		if ($gou === null) $problem .= _CB_MAILING_NOGROUP;
		//mosRedirect( '/index.php?option=com_cbmailing&mosmsg=Please fill in the form correctly: '. $problem );
		mosRedirect( '/index.php?option=com_cbmailing&mosmsg='. _CB_MAILING_FILLFORMCORRECTLY .' '. $problem );
	}

	$allowedToSend = false;

	// Create the list of possible TO groups for this user
	listOfOkayToGroups( $toList );
	$allowedToSend = (count( $toList ) != 0);
	// Check if the group specified for the send is in the possible list
	if ( $allowedToSend ) {
		$allowedToSend = ( $toList[$gou] == 1);
	}

	if  ( !$allowedToSend ) {
		//$msg = "You are not allowed to send to that group";
		$msg = _CB_MAILING_NOPERMISSION;
	} else {

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

		// Get sending email address
		$query = "SELECT email FROM #__users WHERE id='$my->id'";
		$database->setQuery( $query );
		$my->email = $database->loadResult();

		// Get all users email
		$query = "SELECT email FROM #__users u, #__comprofiler ue WHERE u.id=ue.id AND u.block!=1 and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1";

		$selection = utf8RawUrlDecode(substr($filterby,1));
		// MRCB 20070618 2110 change from "if (!$selection === null) {" to.....
		if ($selection != "") {
			$query .= " AND " . $selection;
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
			mosMail( $my->email, $my->name, $row->email, $subject, $message, $mode, NULL, NULL, $attachment );
			//mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $row->email, $subject, $message, $mode, NULL, NULL, $attachment );
		}

		// MRCB 20070619 2235 - added in this to clean up the uploaded file (if found) after it's been sent out.
		if (file_exists($uploadfile)) {
			@unlink($uploadfile);
		}
		// MRCB 20070618 2103 - changed spelling of send to sent
		//$msg = 'Email sent to '. count( $rows ) .' user'. (count ( $rows ) == 1 ? ' ' : 's ');
		$msg = _CB_MAILING_EMAILSENTTO .' '. count( $rows ) .' '. (count ( $rows ) == 1 ? _CB_MAILING_USER : _CB_MAILING_USERS) .' ';
	}

	mosRedirect( '/index.php?option=com_cbmailing&mosmsg='. $msg );
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

function listOfOkayToGroups( &$toList ) {
	global $database, $my;

	$toList = array();
	$query = "SELECT id, fromid, toid
				FROM #__cbmailing_permissions;";
	$database->setQuery( $query );
	$permissions = $database->loadObjectList();

	if (count($permissions) != 0) {
		foreach ($permissions as $permission) {

			// First find the filter
			$query = "SELECT filterfields FROM #__comprofiler_lists WHERE listid = $permission->fromid";
			$database->setQuery( $query );
			$filterby = $database->loadResult();
			$selection = utf8RawUrlDecode(substr($filterby,1));

			// Now check if this user is member of this list
			$query = "SELECT u.id as userid FROM #__users u, #__comprofiler ue 
						WHERE u.id=ue.id AND u.block!=1 and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1 AND u.id='$my->id'";

			if (!( $selection === null )) {
				if ( $selection != "" )
				{
					$query = $query . " AND " . $selection;
				}
				$database->setQuery( $query );
				$users = $database->loadObjectList();

				if (count($users) > 0) {
					// User was a member, so add permission from list to list so far (to display)
					$toList[$permission->toid] = 1;
				}
			}
		}
	}
}

?>
