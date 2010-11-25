<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
JLoader::register('cbmailings', JPATH_COMPONENT_ADMINISTRATOR.DS.'cbmailing.class.php');
JLoader::register('CbmailingControllerSendMail', JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'sendmail.php');

class CbmailingController extends JController
{
	function display()
	{
		$this->mailing();
		//parent::display();
	}

	function send() {
		$controller = new CbmailingControllerSendMail();
		$controller->send();
	}

	function cancel() {
		$url = 'index2.php';
		// mosRedirect( $url );	// J1.0
		$app = &JFactory::getApplication();	// J1.5
		$app->redirect( $url );	// J1.5

	}

	function mailing() {
		$view = $this->getView( 'mailing', 'html' );
		$model = $this->getModel( 'cbmailing' );
		$view->setModel( $model, true );
		$view->display();
		if ($view->getErrors()) {
			JError::raiseWarning('0', $view->getError());
		}
	}

	function permissions() {
		$view = $this->getView( 'permissions', 'html' );
		$model = $this->getModel( 'cbmailing' );
		$view->setModel( $model, true );
		$view->display();
		if ($view->getErrors()) {
			JError::raiseWarning('0', $view->getError());
		}
	}

	function addPerm() {
		$this->addPermission( $option );
	}

	function delPerm() {
		delPermission( $option );
	}

	function configure() {
		$view = $this->getView( 'configuration', 'html' );
		$view->display();
		if ($view->getErrors()) {
			JError::raiseWarning('0', $view->getError());
		}
	}

	function saveConfig() {
		saveConfig2( $option );
	}

	function showMembers() {
		showMembers2( $option );
	}

	function addPermission( $option ) {
		// global $database;	// J1.0
		$database = &JFactory::getDBO();	// J1.5

		/* J1.0
		 $fromgroup			= mosGetParam( $_POST, 'mm_fromgroup', NULL );
		 $togroup			= mosGetParam( $_POST, 'mm_togroup', NULL );
		 */
		// J1.5
		$fromgroup			= JRequest::getVar( 'mm_fromgroup', NULL, "post" );
		$togroup			= JRequest::getVar( 'mm_togroup', NULL, "post" );

		// Only try to do something if both groups are set
		if ($togroup && $fromgroup) {
			// First check if there is already such a permission, if so, don't add it in and let the user know they tried this
			$query = "SELECT id
					FROM #__cbmailing_permissions 
					WHERE toid=". $database->Quote( $togroup ) ." AND fromid=". $database->Quote( $fromgroup ) .";";
			$database->setQuery( $query );
			$results = $database->loadObjectList();

			if (count($results) == 0) {
				$query = "INSERT INTO #__cbmailing_permissions (id, toid, fromid)
						VALUES (NULL, $togroup, $fromgroup);";
				$database->setQuery( $query );
				if ( $database->query() === false ) {
					$msg = $database->getErrorMsg();
				} else {
					$msg = JText::_( 'CB_MAILING_ADMIN_PERMADDED' );
				}
			} else {
				$msg = JText::_( 'CB_MAILING_ADMIN_EXISTINGPERM' );
			}
		} else {
			// let the user know what they did wrong
			$msg = "";
			if ($fromgroup == NULL) {
				$msg .= JText::_( 'CB_MAILING_ADMIN_FROMGRPNOTSET' );
			}
			if ($togroup == NULL) {
				$msg .= JText::_( 'CB_MAILING_ADMIN_TOGRPNOTSET' );
			}
		}

		$url = 'index2.php?option=com_cbmailing&task=permissions';
		// mosRedirect( $url, $msg );	// J1.0
		$app = &JFactory::getApplication();	// J1.5
		$app->redirect($url, $msg);	// J1.5
	}

	function delPermission( $option ) {
		// global $database;	// J1.0
		$database = &JFactory::getDBO();	// J1.5

		// $ids				= mosGetParam( $_POST, 'ids', NULL ); // J1.0
		$ids				= JRequest::getVar( 'ids', NULL, "post" );	// J1.5

		$deletedCount = 0;
		if (count( $ids ) > 0) {
			foreach ($ids as $thisid) {
				$query = "DELETE FROM #__cbmailing_permissions
						WHERE id=". $database->Quote( $thisid ) .";";
				$database->setQuery( $query );
				if ( $database->query() === false ) {
					$msg = $database->getErrorMsg();
				} else {
					$deletedCount++;
				}
			}
		}

		if ($deletedCount > 0) {
			$msg = JText::_( 'CB_MAILING_ADMIN_PERMISSIONSDEL' ) . $deletedCount;
		}

		$url = 'index2.php?option=com_cbmailing&task=permissions';
		// mosRedirect( $url, $msg );	// J1.0
		$app = &JFactory::getApplication();	// J1.5
		$app->redirect($url, $msg);	// J1.5
	}

	function showMembers2( $option ) {
		// global $database;	// J1.0
		$database = &JFactory::getDBO();	// J1.5

		// $ids				= mosGetParam( $_POST, 'ids', NULL );	// J1.0
		$ids				= JRequest::getVar( 'ids', NULL, "post" );	// J1.5

		if (count( $ids ) > 0) {
			$cbm = new cbmailings(true);
			if ($cbm->cbMailingConfig == NULL) {
				$cbm->readConfig();
			}

			$lists['groupData'] = array();
			$lists['includeAll'] = $cbm->cbMailingConfig["allAddr"];
			$emailFieldList = NULL;
			if ( $lists['includeAll'] ) {
				$emailFieldList = $cbm->extraAddressFields();
			}
			$lists['emailFieldList'] = $emailFieldList;

			foreach ($ids as $thisid) {
				// get list of existing permissions
				$query = "SELECT a.id as id, a.toid as toid, b1.title as totitle, a.fromid as fromid, b2.title as fromtitle
						FROM #__cbmailing_permissions as a, #__comprofiler_lists as b1, #__comprofiler_lists as b2 
						WHERE a.toid=b1.listid AND a.fromid=b2.listid AND a.id=".$database->Quote( $thisid ) .";";
				$database->setQuery( $query );
				$lists['groupData'][$thisid] = array();
				$lists['groupData'][$thisid]['permission'] = $database->loadObjectList();

				$lists['groupData'][$thisid]['fromMembers'] = $cbm->listMembers( $lists['groupData'][$thisid]['permission'][0]->fromid,
				false, $emailFieldList );
				$lists['groupData'][$thisid]['toMembers'] = $cbm->listMembers( $lists['groupData'][$thisid]['permission'][0]->toid,
				$lists['includeAll'], $emailFieldList );
			}

			HTML_cbmailing::showMembersForm( $lists, $option );
		}
		else
		{
			$url = 'index2.php?option=com_cbmailing&task=permissions';
			// mosRedirect( $url );	// J1.0
			$app = &JFactory::getApplication();	// J1.5
			$app->redirect($url, $msg);	// J1.5
		}
	}

	function saveConfig2( $option ) {
		global $_POST;

		// J1.5
		$cbMailingConfig_allAddr = JRequest::getVar( "cbMailingConfig_allAddr", null, "post" );
		$cbMailingConfig_feAllowAtt = JRequest::getVar( "cbMailingConfig_feAllowAtt", null, "post" );
		$cbMailingConfig_feAllowHTML = JRequest::getVar( "cbMailingConfig_feAllowHTML", null, "post" );
		$cbMailingConfig_feAllowSigOver = JRequest::getVar( "cbMailingConfig_feAllowSigOver", null, "post" );
		$cbMailingConfig_mmMethod = JRequest::getVar( "cbMailingConfig_mmMethod", null, "post" );
		$cbMailingConfig_mmFrom = JRequest::getVar( "cbMailingConfig_mmFrom", null, "post" );
		$cbMailingConfig_mmFromAddr = JRequest::getVar( "cbMailingConfig_mmFromAddr", null, "post" );
		$cbMailingConfig_mmFromDesc = JRequest::getVar( "cbMailingConfig_mmFromDesc", null, "post" );
		$cbMailingConfig_mmReplyTo = JRequest::getVar( "cbMailingConfig_mmReplyTo", null, "post" );
		$cbMailingConfig_mmReplyToAddr = JRequest::getVar( "cbMailingConfig_mmReplyToAddr", null, "post" );
		$cbMailingConfig_mmReplyToDesc = JRequest::getVar( "cbMailingConfig_mmReplyToDesc", null, "post" );
		$cbMailingConfig_mmTo = JRequest::getVar( "cbMailingConfig_mmTo", null, "post" );
		$cbMailingConfig_mmToAddr = JRequest::getVar( "cbMailingConfig_mmToAddr", null, "post" );
		$cbMailingConfig_mmToDesc = JRequest::getVar( "cbMailingConfig_mmToDesc", null, "post" );
		$cbMailingConfig_mmBCC = JRequest::getVar( "cbMailingConfig_mmBCC", null, "post" );
		$cbMailingConfig_mmBCCAddr = JRequest::getVar( "cbMailingConfig_mmBCCAddr", null, "post" );
		$cbMailingConfig_mmBCCDesc = JRequest::getVar( "cbMailingConfig_mmBCCDesc", null, "post" );
		$cbMailingConfig_signature = JRequest::getVar( "cbMailingConfig_signature", null, "post" );
		$cbMailingConfig_incBlocked = JRequest::getVar( "cbMailingConfig_incBlocked", null, "post" );

		$cbMailingConfig_debug = JRequest::getVar( "cbMailingConfig_debug", null, "post" );
		$cbMailingConfig_debugFromAddr = JRequest::getVar( "cbMailingConfig_debugFromAddr", null, "post" );
		$cbMailingConfig_debugFromDesc = JRequest::getVar( "cbMailingConfig_debugFromDesc", null, "post" );
		$cbMailingConfig_debugToAddr = JRequest::getVar( "cbMailingConfig_debugToAddr", null, "post" );
		$cbMailingConfig_debugToDesc = JRequest::getVar( "cbMailingConfig_debugToDesc", null, "post" );
		$cbMailingConfig_debugETitle = JRequest::getVar( "cbMailingConfig_debugETitle", null, "post" );
		// J1.5

		$cbm = new cbmailings(true);
		$cbm->readConfig();

		$cbm->cbMailingConfig["allAddr"] = false;
		if ($cbMailingConfig_allAddr) {
			$cbm->cbMailingConfig["allAddr"] = $cbMailingConfig_allAddr == "on";
		}

		$cbm->cbMailingConfig["feAllowAtt"] = false;
		if ($cbMailingConfig_feAllowAtt) {
			$cbm->cbMailingConfig["feAllowAtt"] = $cbMailingConfig_feAllowAtt == "on";
		}

		$cbm->cbMailingConfig["feAllowHTML"] =  false;
		if ($cbMailingConfig_feAllowHTML) {
			$cbm->cbMailingConfig["feAllowHTML"] = $cbMailingConfig_feAllowHTML == "on";
		}

		$cbm->cbMailingConfig["feAllowSigOver"] = false;
		if ($cbMailingConfig_feAllowSigOver) {
			$cbm->cbMailingConfig["feAllowSigOver"] = $cbMailingConfig_feAllowSigOver == "on";
		}
		if ($cbMailingConfig_mmMethod) {
			$cbm->cbMailingConfig["mmMethod"] = $cbMailingConfig_mmMethod;
		}
		if ($cbMailingConfig_mmFrom) {
			$cbm->cbMailingConfig["mmFrom"] = $cbMailingConfig_mmFrom;
		}
		if ($cbMailingConfig_mmFromAddr) {
			$cbm->cbMailingConfig["mmFromAddr"] = $cbMailingConfig_mmFromAddr;
		}
		if ($cbMailingConfig_mmFromDesc) {
			$cbm->cbMailingConfig["mmFromDesc"] = $cbMailingConfig_mmFromDesc;
		}
		if ($cbMailingConfig_mmReplyTo) {
			$cbm->cbMailingConfig["mmReplyTo"] = $cbMailingConfig_mmReplyTo;
		}
		if ($cbMailingConfig_mmReplyToAddr) {
			$cbm->cbMailingConfig["mmReplyToAddr"] = $cbMailingConfig_mmReplyToAddr;
		}
		if ($cbMailingConfig_mmReplyToDesc) {
			$cbm->cbMailingConfig["mmReplyToDesc"] = $cbMailingConfig_mmReplyToDesc;
		}
		if ($cbMailingConfig_mmTo) {
			$cbm->cbMailingConfig["mmTo"] = $cbMailingConfig_mmTo;
		}
		if ($cbMailingConfig_mmToAddr) {
			$cbm->cbMailingConfig["mmToAddr"] = $cbMailingConfig_mmToAddr;
		}
		if ($cbMailingConfig_mmToDesc) {
			$cbm->cbMailingConfig["mmToDesc"] = $cbMailingConfig_mmToDesc;
		}
		if ($cbMailingConfig_mmBCC) {
			$cbm->cbMailingConfig["mmBCC"] = $cbMailingConfig_mmBCC;
		}
		if ($cbMailingConfig_mmBCCAddr) {
			$cbm->cbMailingConfig["mmBCCAddr"] = $cbMailingConfig_mmBCCAddr;
		}
		if ($cbMailingConfig_mmBCCDesc) {
			$cbm->cbMailingConfig["mmBCCDesc"] = $cbMailingConfig_mmBCCDesc;
		}
		if ($cbMailingConfig_signature) {
			$cbm->cbMailingConfig["signature"] = $cbMailingConfig_signature;
		}

		$cbm->cbMailingConfig["incBlocked"] = false;
		if ($cbMailingConfig_incBlocked) {
			$cbm->cbMailingConfig["incBlocked"] = $cbMailingConfig_incBlocked == "on";
		}

		$cbm->cbMailingConfig["debug"] = false;
		if ($cbMailingConfig_debug) {
			$cbm->cbMailingConfig["debug"] = $cbMailingConfig_debug == "on";
		}
		if ($cbMailingConfig_debugFromAddr) {
			$cbm->cbMailingConfig["debugFromAddr"] = $cbMailingConfig_debugFromAddr;
		}
		if ($cbMailingConfig_debugFromDesc) {
			$cbm->cbMailingConfig["debugFromDesc"] = $cbMailingConfig_debugFromDesc;
		}
		if ($cbMailingConfig_debugToAddr) {
			$cbm->cbMailingConfig["debugToAddr"] = $cbMailingConfig_debugToAddr;
		}
		if ($cbMailingConfig_debugToDesc) {
			$cbm->cbMailingConfig["debugToDesc"] = $cbMailingConfig_debugToDesc;
		}
		if ($cbMailingConfig_debugETitle) {
			$cbm->cbMailingConfig["debugETitle"] = $cbMailingConfig_debugETitle;
		}

		if ($cbm->writeConfig()) {
			$msg = JText::_( 'CB_MAILING_ADMIN_CONFIGSAVED' );
		}
		else {
			$msg = JText::_( 'CB_MAILING_ADMIN_CONFIGNOTWRITABLE' );
		}
		$url = 'index2.php?option=com_cbmailing&task=configure';
		// mosRedirect( $url, $msg ); 	// J1.0
		$app = &JFactory::getApplication();	// J1.5
		$app->redirect($url, $msg);	// J1.5

	}


}
?>