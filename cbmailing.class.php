<?php
/**
* @version 2.3.4J1.5N
* @package CB Mailing list
* @copyright (c) 2006-2008 - Erik Happaerts  [erik@happaerts.be] / Guus Koning [guus.koning@hccnet.nl]
* @copyright (c) 2007-2009 - Mark Bradley (OSPS Ltd)
* @based on Mambo admin.massmail.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted Access' );

/* --------------------------------------------------------------------------------- */
class cbmailings
{
	var $DoingAdmin = false;
	var $cbMailingConfig = NULL;

	/* ............................................................................. */
	function cbmailings( $doingAdmin=false )
	{
		$this->DoingAdmin = $doingAdmin;
	}

	/* ............................................................................. */
	function configPath() {
		/* J1.0
		if (! isset($mainframe)) {
			global $mainframe;
		}
		 // return $mainframe->getCfg( 'absolute_path' ).'/administrator/components/com_cbmailing/cbmailing.config.php';	// J1.0
		*/
		 return JPATH_ROOT . DS.'administrator'.DS.'components'.DS.'com_cbmailing'.DS.'cbmailing.config.php';
	}

	/* ............................................................................. */
	function readConfig() {
		$this->cbMailingConfig = array();
		$this->cbMailingConfig["allAddr"] = false;
		$this->cbMailingConfig["feAllowAtt"] = true;
		$this->cbMailingConfig["feAllowHTML"] = true;
		$this->cbMailingConfig["feAllowSigOver"] = false;
		$this->cbMailingConfig["mmMethod"] = 2;
		$this->cbMailingConfig["mmFrom"] = 2;
		$this->cbMailingConfig["mmFromAddr"] = "";
		$this->cbMailingConfig["mmFromDesc"] = "";
		$this->cbMailingConfig["mmReplyTo"] = 2;
		$this->cbMailingConfig["mmReplyToAddr"] = "";
		$this->cbMailingConfig["mmReplyToDesc"] = "";
		$this->cbMailingConfig["mmTo"] = 2;
		$this->cbMailingConfig["mmToAddr"] = "";
		$this->cbMailingConfig["mmToDesc"] = "";
		$this->cbMailingConfig["mmBCC"] = 2;
		$this->cbMailingConfig["mmBCCAddr"] = "";
		$this->cbMailingConfig["mmBCCDesc"] = "";
		$this->cbMailingConfig["signature"] = "";
		$this->cbMailingConfig["incBlocked"] = true;
		$this->cbMailingConfig["debug"] = false;
		$this->cbMailingConfig["debugFromAddr"] = "";
		$this->cbMailingConfig["debugFromDesc"] = "";
		$this->cbMailingConfig["debugToAddr"] = "";
		$this->cbMailingConfig["debugToDesc"] = "";
		$this->cbMailingConfig["debugETitle"] = "";

		if (file_exists($this->configPath())) {
			$cbMailingConfig = array();
			include $this->configPath();
			foreach ($cbMailingConfig as $key => $value) {
				$this->cbMailingConfig[$key] = $value;
			}
		}
		return true;
	}

	/* ............................................................................. */
	function writeConfig() {
		if ($this->DoingAdmin) {

			if ($this->cbMailingConfig != NULL) {
/*
				$output = "<?php
defined( '_VALID_MOS' ) or die( 'Restricted Access' );
";
*/
				$output = "<?php
defined( '_JEXEC' ) or die( 'Restricted Access' );
";
				foreach ($this->cbMailingConfig as $key => $value) {
					/* $output .= '$cbMailingConfig["' . $key .'"]='.
						(is_string( $value ) ? "'" : '').
						str_replace( "'", "\\'", $value).
						(is_string( $value ) ? "'" : '').
						";
";					*/
					$output .= '$cbMailingConfig["' . $key .'"]=';
					if (is_string( $value )) {
						$output .= "'". str_replace( "'", "\\'", $value) ."'";
					} else if (is_bool( $value )) {
						$output .= $value ? "true" : "false";
					} else {
						// Doesn't cope wth complex types like arrays or objects - don't need those, er, yet.
						$output .= str_replace( "'", "\\'", $value);
					}
					$output .= ";
";
				}
				$output .= "
?>";
				$configFile = fopen( $this->configPath(), 'w' );
				if ($configFile) {
					$wrote = fwrite( $configFile, $output );
					fclose( $configFile );
					if ($wrote == strlen( $output )) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/* ............................................................................. */
	function listOfOkayToGroups( &$toList ) {
		// global $database;	// J1.0
		// global $my;			// J1.0
		$database = &JFactory::getDBO();	// J1.5
		$my = &JFactory::getUser();			// J1.5

		$toList = array();
		$query = "SELECT id, fromid, toid
					FROM #__cbmailing_permissions;";
		$database->setQuery( $query );
		$permissions = $database->loadObjectList();

		if (count( $permissions ) > 0) {
			foreach ($permissions as $permission) {
/*
				// First find the filter
				$query = "SELECT filterfields FROM #__comprofiler_lists WHERE listid = $permission->fromid";
				$database->setQuery( $query );
				$filterby = $database->loadResult();
				$selection = $this->utf8RawUrlDecode(substr($filterby,1));

				// Now check if this user is member of this list
				$query = "SELECT u.id as userid FROM #__users u, #__comprofiler ue 
							WHERE u.id=ue.id AND u.block!=1 and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1 AND u.id=". $database->Quote( $my->id );

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
*/
				$users = $this->listMembers( $permission->fromid, false, null );
				if (count( $users ) > 0) {
					foreach( $users as $thisUser ) {
						if ($thisUser->id == $my->id) {
							// User was a member, so add permission from list to list so far (to display)
							$toList[$permission->toid] = 1;
							break;
						}
					}
				}
			}
		}
	}

	/* ............................................................................. */
	function messageForm( $option ) {
		// global $database;	// J1.0
		// global $my;			// J1.0
		$database = &JFactory::getDBO();	// J1.5
		$my = &JFactory::getUser();			// J1.5

		// Need logic in here ot:
		// 1 - are there any permissions? If no bail out
		// 2 - is the user a member of any of the FROM groups? If no bail out
		// 3 - build a list of all FROM groups of which user is a member
		// 4 - build unique list of all TO groups permitted from FROM groups of which users is a member
		// 5 - display that list

		if ($this->cbMailingConfig == NULL) {
			$this->readConfig();
		}

		$allowedToSend = false;

		$this->listOfOkayToGroups( $toList );
		$allowedToSend = (count( $toList ) != 0);
		if  ( !$allowedToSend ) {
			// No permissions apply to this user - cannot send
		} else {
			// Now have to create an HTML select list of TO groups based on the built list
			$query = "SELECT listid AS value, title AS text FROM #__comprofiler_lists WHERE published=1 AND (";
			$listids = "";
			foreach (array_keys($toList) as $thisList) {
				$listids .= ($listids == "" ? "" : " OR " ) ." listid=". $database->Quote( $thisList );
			}
			$query .= $listids .") ORDER BY ordering";
			$database->setQuery( $query );
			$users = $database->loadObjectList();
			// $lists['gid'] = mosHTML::selectList( $users, 'mm_group', 'size="10"', 'value', 'text', 0 );
			// MRCB - J1.5
			$lists['gid'] = JHTML::_('select.genericlist', $users, 'mm_group', 'size="10"', 'value', 'text', 0 );
			// MRCB - J1.5
		}

		if ( $allowedToSend ) {
			HTML_cbmailing::messageForm( $lists, $this->cbMailingConfig, $option );
		} else {
			// Some suitable "you're not allowed to e-mail groups" type message
			// HTML_cbmailing::errorMessage( "You are not permitted to send e-mail to user groups - sorry", $option );	// _CB_MAILING_NOPERMISSIONATALL
			HTML_cbmailing::errorMessage( JText::_( 'CB_MAILING_NOPERMISSIONATALL' ), $option );	
		}
	}

	/* ............................................................................. */
	// New function in V2.3.0
	function extraAddressFields() {
		// global $database;	// J1.0
		$database = &JFactory::getDBO();	// J1.5

		$query = "SELECT name FROM #__comprofiler_fields WHERE type = ". $database->Quote( "emailaddress" ) ;
		$database->setQuery( $query );
		return $database->loadObjectList();
	}

	/* ............................................................................. */
	// New function in V2.3.0
	function listMembers( $group, $includeAllAddresses, $emailFieldList )
	{
		// global $database;	// J1.0
		// global $acl;			// J1.0
		$database = &JFactory::getDBO();	// J1.5
		$acl	=& JFactory::getACL();		// J1.5

		if ($this->cbMailingConfig == NULL) {
			$this->readConfig();
		}

		$query = "SELECT usergroupids,filterfields FROM #__comprofiler_lists WHERE listid = $group";
		$database->setQuery( $query );
		$filterby = $database->loadResult();
		$groupResults = $database->loadObjectList();
		$filterby = $groupResults[0]->filterfields;
		$userGroupIds = $groupResults[0]->usergroupids;
/*
echo "<pre>\n";
echo "group=";
var_dump( $group );
echo "\nquery=";
var_dump( $query );
echo "\ngroupResults=";
var_dump( $groupResults );
echo "\nfilterby=";
var_dump( $filterby );
echo "\nuserGroupIds=";
var_dump( $userGroupIds );
echo "</pre>\n";
*/

		// V2.2 - find the fields that are e-mail addresses
		$extraEmailFields = "";
		if ( $includeAllAddresses && ( $emailFieldList != NULL)) {
			if (count( $emailFieldList ) > 0) {
				foreach ($emailFieldList as $field) {
					$extraEmailFields .= ",". $field->name;
				}
			}
		}
/*
echo "<pre>\n";
echo "includeAllAddresses=";
var_dump( $includeAllAddresses );
echo "\nextraEmailFields=";
var_dump( $extraEmailFields );
echo "</pre>\n";
*/
		$allusergids=array();
		$usergids=explode(",",$userGroupIds);
		if (count( $usergids ) > 0) {
			foreach($usergids AS $usergid) {
				$allusergids[]=$usergid;

				// 29 is the GID for the front end, 30 is the GID for the backend
				if ($usergid==29 || $usergid==30) {
					$groupchildren = array();
					$groupchildren = $acl->get_group_children( $usergid, 'ARO','RECURSE' );
					$allusergids = array_merge($allusergids,$groupchildren);
				}

			}
		}
		$usergids = "-1";
		$usergids=implode(",",$allusergids);

/*
$query = "SELECT COUNT(*) FROM #__users u, #__comprofiler ue WHERE u.id=ue.id AND u.block !=1 AND ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1 AND u.gid IN (".$usergids.")";
echo "<pre>\n";
echo "usergids=";
var_dump( $usergids );
echo "\nquery=";
var_dump( $query );
echo "</pre>\n";
*/

		// Get all users email
		//$query = "SELECT email FROM #__users u, #__comprofiler ue WHERE u.id=ue.id AND u.block!=1 and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1";
		$query = "SELECT u.id AS id,name,username,email". $extraEmailFields ." FROM #__users u, #__comprofiler ue WHERE u.id=ue.id and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1".
					" AND u.gid IN (". $usergids .")";
		//$query = "SELECT * FROM #__users u, #__comprofiler ue WHERE u.id=ue.id and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1";
		if (! $this->cbMailingConfig["incBlocked"])
		{
			$query .= " AND u.block!=1";
		}

		$selection = $this->utf8RawUrlDecode(substr($filterby,1));
/*
echo "<pre>\n";
echo "selection=";
var_dump( $selection );
echo "\nquery=";
var_dump( $query );
echo "</pre>\n";
*/
		// MRCB 20070618 2110 change from "if (!$selection === null) {" to.....
		if ($selection != "") {
			$query .= " AND " . $selection;
		}

		$database->setQuery( $query );
		return $database->loadObjectList();
	}

	/* ............................................................................. */
	function sendMail() {
		// global $database;	// J1.0
		// global $acl;			// J1.0
		// global $my;			// J1.0
		$database 	= &JFactory::getDBO();	// J1.5
		$acl		= &JFactory::getACL();	// J1.5
		$my 		= &JFactory::getUser();	// J1.5

		// MRCB 20070619 2233 - added in mosConfig_absolute_path
		// global $mosConfig_sitename;	// J1.0
		// global $mosConfig_absolute_path;	// J1.0
		// global $mosConfig_mailfrom, $mosConfig_fromname;	// J1.0
		global $mainframe;	// For J1.5

		if ($this->cbMailingConfig == NULL) {
			$this->readConfig();
		}

		if ($this->DoingAdmin) {
			$redirectScript = "index2.php";
		}
		else {
			$redirectScript = "index.php";
		}
		// For security reasons, we should check that the user is permitted to send to this list

		/* J1.0
		$mode				= mosGetParam( $_POST, 'mm_mode', 0 );
		$subject			= mosGetParam( $_POST, 'mm_subject', '' );
		$group				= mosGetParam( $_POST, 'mm_group', NULL );
		*/

		// J1.5
		$mode				= JRequest::getVar( 'mm_mode', 0, "post" );
		$subject			= JRequest::getVar( 'mm_subject', '', "post" );
		$group				= JRequest::getVar( 'mm_group', NULL, "post" );
	//	$recurse			= mosGetParam( $_POST, 'mm_recurse', 'NO_RECURSE' );

		// pulls message information either in text or html format
		if ( $mode ) {
			$message_body	= $_POST['mm_message'];
		} else {
			// automatically removes html formatting
			// $message_body	= mosGetParam( $_POST, 'mm_message', '' );	// J1.0
			$message_body	= JRequest::getVar( 'mm_message', '', 'post' );	// J1.5
		}
		$message_body 		= stripslashes( $message_body );
		
		if (!$message_body || !$subject || $group === null) {
			$problem = "";
			//if (!$message_body) $problem .= " No message body;";
			if (!$message_body) $problem .= " ". JText::_( 'CB_MAILING_NOMESSAGEBODY' );
			//if (!$subject) $problem .= " No subject;";
			if (!$subject) $problem .= " ". JText::_( 'CB_MAILING_NOSUBJECT' );
			// if ($group === null) $problem .= "No group selected;";
			if ($group === null) $problem .= JText::_( 'CB_MAILING_NOGROUP' );
			//mosRedirect( 'index.php?option=com_cbmailing&mosmsg=Please fill in the form correctly: '. $problem );
			$url = $redirectScript .'?option=com_cbmailing&mosmsg='. JText::_( 'CB_MAILING_FILLFORMCORRECTLY' ) .' '. $problem;
			// mosRedirect( $url );	// J1.0
			$url = $redirectScript .'?option=com_cbmailing';
			$msg = JText::_( 'CB_MAILING_FILLFORMCORRECTLY' ) .' '. $problem;
			$app = &JFactory::getApplication();	// J1.5
			$app->redirect( $url, $msg );	// J1.5
		}

		$allowedToSend = false;

		// Create the list of possible TO groups for this user
		$this->listOfOkayToGroups( $toList );
		$allowedToSend = (count( $toList ) != 0);
		// Check if the group specified for the send is in the possible list
		if ( $allowedToSend ) {
			$allowedToSend = ( $toList[$group] == 1);
		}

		if  ( !$allowedToSend ) {
			//$msg = "You are not allowed to send to that group";
			$messageDetails = JText::_( 'CB_MAILING_DEBUG_ERRORINTRO' ) . "\n"
								. $my->name
								.' ('. $my->email .") =>"
								.$group ."\n";
			$msg = JText::_( 'CB_MAILING_NOPERMISSION' );
		} else {

			// MRCB 20070619 2233 - added from here......
			$attachment = NULL;
			if (isset( $_FILES['mm_attach'] )) {
				if (file_exists($_FILES['mm_attach']['tmp_name'])) {
					$uploadDir = "uploads";
					// Does the upload dir exist?
					// if (! file_exists($mosConfig_absolute_path ."/". $uploadDir))	// J1.0
					if (! file_exists( JPATH_SITE.DS. $uploadDir ))
					{
						// No, so create it
						// if (! mkdir($mosConfig_absolute_path ."/". $uploadDir))	// J1.0
						if (! mkdir( JPATH_SITE.DS. $uploadDir ))
						{
							// Couldn't create it, so set it to be blank
							$uploadDir = "";
						}
					}
					// else if (! is_writable($mosConfig_absolute_path ."/". $uploadDir))	// J1.0
					else if (! is_writable( JPATH_SITE.DS. $uploadDir ))
					{
						// It's not writeable, so we'll create our own
						$uploadDir = "cbmailing_uploads";
						// if (! mkdir($mosConfig_absolute_path ."/". $uploadDir))	// J1.0
						if (! mkdir( JPATH_SITE.DS. $uploadDir ))
						{
							// Couldn't create it, so set it to be blank
							$uploadDir = "";
						}
					}
					if ($uploadDir != "")
					{
						// $uploadfile = $mosConfig_absolute_path . "/". $uploadDir ."/". basename($_FILES['mm_attach']['name']);	// J1.0
						$uploadfile = JPATH_SITE.DS. $uploadDir .DS. basename($_FILES['mm_attach']['name']);
						if (move_uploaded_file($_FILES['mm_attach']['tmp_name'], $uploadfile)) {
							$attachment = $uploadfile;
						}
					}
				}
			}
			// MRCB 20070619 2233 - .............to here

			// Get sending email address
			$query = "SELECT email FROM #__users WHERE id=". $database->Quote( $my->id );
			$database->setQuery( $query );
			$my->email = $database->loadResult();

			// Copy the config value so that in future we might allow more complex derivation of whether to send to all, such as controlled per user
			$includeAllAddresses = $this->cbMailingConfig["allAddr"];
			$emailFieldList = NULL;
			if ( $includeAllAddresses ) {
				$emailFieldList = $this->extraAddressFields();
			}
			$rows = $this->listMembers( $group, $includeAllAddresses, $emailFieldList );

/*
echo "<pre>\n";
var_dump( $query );
echo "\n\n";
var_dump( $rows );
echo "\n\n";
var_dump( $emailFieldList );
echo "</pre>\n";
exit;
*/
			// Build e-mail message format
			$message 			= $message_body;
			// $subject 			= $mosConfig_sitename. ' / '. stripslashes( $subject);	// J1.0
			$subject			= $mainframe->getCfg('sitename') .' / '. stripslashes( $subject);	// J1.5

			switch ( $this->cbMailingConfig["mmTo"] )
			{
				case 1:	// List addresses
					$toEmail = "";
					$toName  = "";
					break;

				case 2:	// Specific address
				default:
					$toEmail = $this->cbMailingConfig["mmToAddr"];
					$toName  = $this->cbMailingConfig["mmToDesc"];
					break;
			}

			switch ( $this->cbMailingConfig["mmBCC"] )
			{
				case 1:	// No one
					$bccEmail = NULL;
					$bccName  = NULL;
					break;

				case 2:	// List addresses
					$bccEmail = "";
					$bccName  = "";
					break;

				case 3:	// Specific address
				default:
					$bccEmail = $this->cbMailingConfig["mmBCCAddr"];
					$bccName  = $this->cbMailingConfig["mmBCCDesc"];
					break;
			}

			switch ( $this->cbMailingConfig["mmFrom"] )
			{
				case 1:	// Logged in user
					$fromEmail = $my->email;
					$fromName  = $my->name;
					break;

				case 2:	// Specific address
				default:
					$fromEmail = $this->cbMailingConfig["mmFromAddr"];
					$fromName  = $this->cbMailingConfig["mmFromDesc"];
					break;
			}

			switch ( $this->cbMailingConfig["mmReplyTo"] )
			{
				case 1:	// No one
					$replyEmail = NULL;
					$replyName  = NULL;
					break;

				case 2:	// Logged in user
					$replyEmail = $my->email;
					$replyName  = $my->name;
					break;

				case 3:	// Specific address
				default:
					$replyEmail = $this->cbMailingConfig["mmReplyToAddr"];
					$replyName  = $this->cbMailingConfig["mmReplyToDesc"];
					break;
			}

			// MRCB - DEBUG
			$mailedDetails = "";
			$debugLineSep = "<br />\r\n";

			if (! $this->DoingAdmin ) {
				if (! $this->cbMailingConfig["feAllowSigOver"]) {
					$message .= "\r\n". $this->cbMailingConfig["signature"];
				}
			}

			$successCount = 0;
			$sendToCount = 0;
			$sendError = "";

			switch ( $this->cbMailingConfig["mmMethod"] )
			{
				case 1: // One mail for all via TO:
					$sendList= array();
					if (count( $rows ) > 0) {
						foreach ($rows as $row) {
							$sendList[] = $row->email;
							$mailedDetails .= (($mailedDetails == "") ? "" : ", " ) . $row->email;
							$sendToCount++;
							if ( $includeAllAddresses ) {
								foreach ($emailFieldList as $field) {
									if ( $row->{$field->name} != null ) {
										if ( $row->{$field->name} != "" ) {
											$sendList[] = $row->{$field->name};
											$mailedDetails .= (($mailedDetails == "") ? "" : ", " ) . $row->{$field->name};
											$sendToCount++;
										}
									}
								}
							}
						}
					}
					$toEmailDebug = $toEmail;
					if ($this->cbMailingConfig["mmTo"] == 1) {
						$toEmail = $sendList;
						$toEmailDebug = $mailedDetails;
					}
					$bccEmailDebug = $bccEmail;
					if ($this->cbMailingConfig["mmBCC"] == 2) {
						$bccEmail = $sendList;
						$bccEmailDebug = $mailedDetails;
					}
					// $result = mosMail( $fromEmail, $fromName, $toEmail , $subject, $message, $mode, NULL, $bccEmail, $attachment, $replyEmail, $replyName );	// J1.0
					$result = JUtility::sendMail( $fromEmail, $fromName, $toEmail , $subject, $message, $mode, NULL, $bccEmail, $attachment, $replyEmail, $replyName );	// J1.5

					$mailedDetails = "From e: ". $fromEmail .", " . $debugLineSep 
										."From name: ". $fromName .", " . $debugLineSep
										."To: ". $toEmailDebug .", " . $debugLineSep 
										."Subject: ". $subject .", " . $debugLineSep 
										."Message: ". $message .", " . $debugLineSep 
										."Mode: ". $mode .", " . $debugLineSep 
										."CC: (NULL)" .", " . $debugLineSep 
										."BCC: ". ($bccEmailDebug == NULL ? "(NULL)": $bccEmailDebug) .", " . $debugLineSep 
										."Attachment: ". (isset($attachment) ? $attachment : "(no attachment)") .", " . $debugLineSep 
										."Reply e: ". ($replyEmail == NULL ? "(NULL)" : $replyEmail) .", " . $debugLineSep 
										."Reply name: ". ($replyName == NULL ? "(NULL)" : $replyEmail) .", " . $debugLineSep 
										."Result=". ($result ? "true" : "false") . $debugLineSep. $debugLineSep;

					// MRCB 20090324 - V2.3.3 - First attempt at J1.5 legacy support
					if (is_object( $result )) {
						// J1.5 test
						if (isset( $result->message )) {
							$sendError = $result->message .JText::_( 'CB_MAILING_ADMIN_SENDERRORLINKTEXT' ). $mailedDetails;
						}
					}
					else if ($result) $successCount = $sendToCount;
					break;

				case 2: // Email per person
					$mailedDetails .= 	 "From e: ". $fromEmail .", " . $debugLineSep 
										."From name: ". $fromName .", " . $debugLineSep 
										."Subject: ". $subject .", " . $debugLineSep 
										."Message: ". $message .", " . $debugLineSep 
										."Mode: ". $mode .", " . $debugLineSep 
										."Attachment: ". (isset($attachment) ? $attachment : "(no attachment)") .", " . $debugLineSep 
										."Reply e: ". $replyEmail .", " . $debugLineSep 
										."Reply name: ". $replyName .", " . $debugLineSep 
										. $debugLineSep;

					$sendList= array();
					if (count( $rows ) > 0) {
						foreach ($rows as $row) {
							$sendList[] = $row->email;
							if ( $includeAllAddresses ) {
								foreach ($emailFieldList as $field) {
									if ( $row->{$field->name} != null ) {
										if ( $row->{$field->name} != "" ) {
											$sendList[] = $row->{$field->name};
										}
									}
								}
							}
						}
					}

					if (count( $sendList ) > 0) {
						foreach ($sendList as $thisAddr) {
							$sendToCount++;
							// MRCB 20070619 0840 - the two NULLs are cc and bcc
							// mosMail params are: $from, $fromname, $recipient, $subject, $body, $mode=0, $cc=NULL, $bcc=NULL, $attachment=NULL, $replyto=NULL, $replytoname=NULL
							//$xxxx .= $row->email. ": ".
							if ($this->cbMailingConfig["mmTo"] == 1) {
								$toEmail = $thisAddr;
							}
							if ($this->cbMailingConfig["mmBCC"] == 2) {
								$bccEmail = $thisAddr;
							}
							// $result = mosMail( $fromEmail, $fromName, $toEmail, $subject, $message, $mode, NULL, $bccEmail, $attachment, $replyEmail, $replyName );	// J1.0
							$result = JUtility::sendMail( $fromEmail, $fromName, $toEmail, $subject, $message, $mode, NULL, $bccEmail, $attachment, $replyEmail, $replyName );	// J1.5


							$theseMailedDetails = 	 "To: ". $toEmail .", " . $debugLineSep 
													."CC: (NULL)" .", " . $debugLineSep 
													."BCC: ". ($bccEmail == NULL ? "(NULL)": $bccEmail) .", " . $debugLineSep 
													."Result=". ($result ? "true" : "false") . $debugLineSep. $debugLineSep;
							$mailedDetails .= 	 $theseMailedDetails; 

							// MRCB 20090324 - V2.3.3 - First attempt at J1.5 legacy support
							if (is_object( $result )) {
								// J1.5 test
								if (isset( $result->message )) {
									$sendError .= "\n\n\n\n". $result->message . JText::_( 'CB_MAILING_ADMIN_SENDERRORLINKTEXT' ) . $theseMailedDetails;
								}
							}
							else if ($result) $successCount++;

							//mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $row->email, $subject, $message, $mode, NULL, NULL, $attachment );
						}
					}
					break;
			}

			// MRCB 20090324 - V2.3.3 - First attempt at J1.5 legacy support
			if (strlen( $sendError ) > 0) {
				// mosMail( $mosConfig_mailfrom, $mosConfig_fromname, $mosConfig_mailfrom, _CB_MAILING_ADMIN_SENDERRORSUBJECT, $sendError, $mode );	// J1.0
				JUtility::sendMail( $mainframe->getCfg('mailfrom'), $mainframe->getCfg('fromname'), $mainframe->getCfg('mailfrom'), JText::_( 'CB_MAILING_ADMIN_SENDERRORSUBJECT' ), $sendError, $mode );	// J1.5
			}

			// MRCB 20070619 2235 - added in this to clean up the uploaded file (if found) after it's been sent out.
			if (isset($uploadfile)) {
				if (file_exists($uploadfile)) {
					@unlink($uploadfile);
				}
			}
			$msg = JText::_( 'CB_MAILING_EMAILSENTTOXUSERS' ) .' '. $successCount  
					.' ('. JText::_( 'CB_MAILING_EMAILSENTTOXUSERSTOTAL' ) ." ". count( $rows ) 
					. (count( $rows ) != $sendToCount ? '/'. $sendToCount : '')
					.')';
		}

		if ($this->cbMailingConfig["debug"]) {
			// MRCB DEBUG
			/* J1.0
			$result = mosMail( $this->cbMailingConfig["debugFromAddr"], 
								$this->cbMailingConfig["debugFromDesc"], 
								$this->cbMailingConfig["debugToAddr"], 
								$this->cbMailingConfig["debugETitle"],
								$mailedDetails . $msg, 0);
			J1.0	*/
			// J1.5
			$result = JUtility::sendMail( $this->cbMailingConfig["debugFromAddr"], 
											$this->cbMailingConfig["debugFromDesc"], 
											$this->cbMailingConfig["debugToAddr"], 
											$this->cbMailingConfig["debugETitle"],
											$mailedDetails . $msg, 0);
			// Uncomment the following line to display the message - would need to comment out the mosRedirect
			//HTML_cbmailing::errorMessage( $mailedDetails . $msg, NULL );
		}

		// mosRedirect( $redirectScript .'?option=com_cbmailing&mosmsg='. $msg );
		$url = $redirectScript .'?mosmsg='. $msg;
		// mosRedirect( $url );	// J1.0
		$url = $redirectScript;
		$app = &JFactory::getApplication();	// J1.5
		$app->redirect( $url, $msg );	// J.15
	}

	/* ............................................................................. */
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

}

?>