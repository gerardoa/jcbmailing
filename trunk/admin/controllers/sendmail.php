<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CbmailingControllerSendMail extends JController
{
	var $group;
	var $emailModel;

	function __construct() {
		parent::__construct();
		$this->group = JRequest::getInt('mm_group', null, 'post');
	}

	function display()
	{
		$this->mailing();
		//parent::display();
	}

	function mailing() {
		$model = $this->getModel( 'cbmailing' );

		if(!$this->allowSend()) {
			// No permissions apply to this user - cannot send
			$this->setError(JText::_( 'CB_MAILING_NOPERMISSIONATALL' ));
			return false;
		}

		$view = $this->getView( 'mailing', 'html' );
		$view->setModel( $model, true );
		$view->display();
		return true;
	}

	function send() {
		if(!$this->prepareToSend()) {
			$this->mailing();
			return false;
		}
		return $this->sendEmail();
	}

	function allowSend($group = null) {
		$model = $this->getModel('cbmailing');
		// Create the list of possible TO groups for this user
		$toList = $model->getListOfOkayToGroups();
		if ($group == null) {
			return count($toList) != 0;
		}
		// Check if the group specified for the send is in the possible list
		return $toList[$this->group] == 1;
	}

	function prepareToSend() {

		if ($this->group === null) {
			JError::raiseWarning(0, JText::_( 'CB_MAILING_NOGROUP' ));
			return false;
		}

		if(!$this->allowSend()) {
			$my = JFactory::getUser();
			$messageDetails = JText::_( 'CB_MAILING_DEBUG_ERRORINTRO' ) . '\n'
			. $my->name . ' (' . $my->email . ') =>' . $this->group . '\n';
			$msg = JText::_( 'CB_MAILING_NOPERMISSION' ) . '<br/>' . $messageDetails;
			JError::raiseWarning(0, $msg);
			return false;
		}

		$this->emailModel = $this->getModel('cbemail');
		if (!$this->emailModel->populate()) {
			return false;
		}
		return true;
	}

	function sendEmail() {
		$e = $this->emailModel;
		$p = JComponentHelper::getParams('com_cbmailing');
		if ($p->get('cbMailingConfig_mmMethod') === 1)
		{
			$rs = JUtility::sendMail( $e->fromEmail, $e->fromName, $e->recipients , $e->subject, $e->messageBody, $e->mode, NULL, $e->bbcEmail, $e->attachmet, $e->replyEmail, $e->replyName );
		} else {
			foreach ($e->recipients as $receiver) {
				$rs = JUtility::sendMail( $e->fromEmail, $e->fromName, $e->recipients, $e->subject, $e->messageBody, $e->mode, NULL, $e->bbcEmail, $e->attachmet, $e->replyEmail, $e->replyName );
			}
		}

		// Check for an error
		if ( JError::isError($rs) ) {
			$msg	= $rs->getError() . JText::_( 'CB_MAILING_ADMIN_SENDERRORLINKTEXT' ). $mailedDetails;
		} else {
			$msg = $rs ? JText::sprintf( 'E-mail sent to', count( $e->recipients ) ) : JText::_('The mail could not be sent');
		}

		// Redirect with the message
		$this->setRedirect( 'index.php?option=com_cbmailing', $msg );

		//TODO: implementare successCOunt in maniera adeguata
		/*$msg = JText::_( 'CB_MAILING_EMAILSENTTOXUSERS' ) .' '. $successCount
		.' ('. JText::_( 'CB_MAILING_EMAILSENTTOXUSERSTOTAL' ) ." ". count( $rows )
		. (count( $rows ) != $sendToCount ? '/'. $sendToCount : '')
		.')';*/
	}
}