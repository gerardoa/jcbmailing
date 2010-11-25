<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class CbmailingModelCbemail extends JModel
{
	var $mode;
	var $subject;
	var $group;
	var $attachmet;
	var $fromEmail;
	var $fromName;
	var $emailFieldList;
	var $recievers;
	var $messageBody;
	var $bbcName;
	var $bbcEmail;
	var $replyName;
	var $replyEmail;

	var $params;

	var $debugReceiverEmail;
	var $debugReceiverName;

	function __construct($config) {
		parent::__construct($config);
		$this->params = JComponentHelper::getParams( 'com_cbmailing' );
	}

	function __destruct() {
		if (isset($this->attachmet) && file_exists($this->attachmet)) @unlink($this->attachmet);
	}

	function populate() {
		$this->checkRequest();
		if($this->getError()) {
			$this->setError(JText::_( 'CB_MAILING_FILLFORMCORRECTLY' ));
			return false;
		}

		$this->manageAttachement();

		$this->parametersOptions();

		$this->getReciervers();

		$this->buildMessage();

		$this->sendEmail();

	}

	function checkRequest() {
		$this->mode   	= JRequest::getVar( 'mm_mode', 0, "post" );
		$subject	    = JRequest::getVar( 'mm_subject', '', "post" );
		$this->$group	= JRequest::getVar( 'mm_group', NULL, "post" );
		// pulls message information either in text or html format
		$message_body = ($mode) ? $_POST['mm_message'] : JRequest::getVar( 'mm_message', '', 'post' );
		$this->messageBody = stripslashes( $message_body );

		if (!$this->messageBody) $this->setError(JText::_( 'CB_MAILING_NOMESSAGEBODY' ));
		if (!$subject) {
			$this->setError(JText::_( 'CB_MAILING_NOSUBJECT' ));
		} else {
			$app = JFactory::getApplication();
			$this->subject = $app->getCfg('sitename') .' / '. stripslashes( $subject);	// J1.5
		}
		if ($this->$group === null) $this->setError(JText::_( 'CB_MAILING_NOGROUP' ));
	}

	function manageAttachement() {
		$attachment = null;
		if (isset( $_FILES['mm_attach'] )) {
			if (file_exists($_FILES['mm_attach']['tmp_name'])) {
				$uploadDir = "uploads";
				// Does the upload dir exist?

				if (! file_exists( JPATH_SITE.DS. $uploadDir ))
				{
					// No, so create it

					if (! mkdir( JPATH_SITE.DS. $uploadDir ))
					{
						// Couldn't create it, so set it to be blank
						$uploadDir = "";
					}
				}
				else if (! is_writable( JPATH_SITE.DS. $uploadDir ))
				{
					// It's not writeable, so we'll create our own
					$uploadDir = "cbmailing_uploads";
					if (! mkdir( JPATH_SITE.DS. $uploadDir ))
					{
						// Couldn't create it, so set it to be blank
						$uploadDir = "";
					}
				}
				if ($uploadDir != "")
				{
					$uploadfile = JPATH_SITE.DS. $uploadDir .DS. basename($_FILES['mm_attach']['name']);
					if (move_uploaded_file($_FILES['mm_attach']['tmp_name'], $uploadfile)) {
						$this->attachment = $uploadfile;
					}
				}
			}
		}
	}

	function getReciervers() {
		// Copy the config value so that in future we might allow more complex derivation of whether to send to all, such as controlled per user
		$includeAllAddresses = $this->params->get('cbMailingConfig_allAddr'); //$this->cbMailingConfig["allAddr"];
		if ( $includeAllAddresses ) {
			$database = &JFactory::getDBO();	// J1.5
			$query = "SELECT name FROM #__comprofiler_fields WHERE type = ". $database->Quote( "emailaddress" ) ;
			$database->setQuery( $query );
			$emailFieldList = $database->loadObjectList();
		}
		$model = new CbmailingModelCbmailing();
		$cbUsers = $model->listMembers($this->group, $includeAllAddresses, $this->emailFieldList);

		if ( $includeAllAddresses ) {
			foreach ($cbUsers as $cbUser) {
				$this->recievers[] = $cbUser->email;
				// look for more email for this user
				foreach ($emailFieldList as $field) {
					if ( !empty($cbUser->{$field->name})) {
						$this->recievers[] = $cbUser->{$field->name};
					}
				}
			}
		}
	}

	function buildMessage() {
		if ($this->params->get('cbMailingConfig_feAllowSigOver')) {
			$this->messageBody .= "\r\n". $this->params->get('cbMailingConfig_signature');
		}
	}

	function parametersOptions() {
		$p = $this->params;
		$user = JFactory::getUser();


		switch ( $p->get('cbMailingConfig_mmBCC'))
		{
			case 1:	// No one
				$this->bccEmail  = null;
				$this->bbcName  = null;
				break;

			case 2:	// List addresses
				$this->bccEmail  = $this->recievers;
				$this->bbcName  = "";
				break;

			case 3:	// Specific address
			default:
				$this->bccEmail  = $this->cbMailingConfig["mmBCCAddr"];
				$this->bbcName  = $this->cbMailingConfig["mmBCCDesc"];
				break;
		}

		if ($p->get('cbMailingConfig_mmTo')) {
			$this->debugReceiverEmail = $p->get('cbMailingConfig_mmToAddr');
			$this->debugReceiverName  = $p->get('cbMailingConfig_mmToDesc');
			// this is strange
			$this->recievers = $this->debugReceiverEmail;
		}

		if ($p->get('cbMailingConfig_mmFrom')) {
			$this->fromEmail = $p->get('cbMailingConfig_mmFromAddr');
			$this->fromName  = $p->get('cbMailingConfig_mmFromDesc');
		} else {
			$this->fromName = $user->name;
			$this->fromEmail = $user->email;
		}

		switch ( $p->get('cbMailingConfig_mmReplyTo') )
		{
			case 1:	// No one
				$this->replyEmail = NULL;
				$this->replyName   = NULL;
				break;

			case 2:	// Logged in user
				$this->replyEmail = $user->email;
				$this->replyName   = $user->name;
				break;

			case 3:	// Specific address
			default:
				$this->replyEmail = $this->cbMailingConfig["mmReplyToAddr"];
				$this->replyName  = $this->cbMailingConfig["mmReplyToDesc"];
				break;
		}


	}
}