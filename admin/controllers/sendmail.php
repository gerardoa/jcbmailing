<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CbmailingControllerSendMail extends JController
{
	var $group;
	var $emailModel;
	
	function send() {
		$this->group = JRequest::getVar( 'mm_group', null, 'post' );
		if ($this->group === null) {
			$this->setError(JText::_( 'CB_MAILING_NOGROUP' ));
			return false;
		}

		if(!$this->checkPermission()) {
			$messageDetails = JText::_( 'CB_MAILING_DEBUG_ERRORINTRO' ) . '\n'
			. $my->name . ' (' . $my->email . ') =>' . $this->group . '\n';
			$msg = JText::_( 'CB_MAILING_NOPERMISSION' );
			$this->setError($messageDetails);
			$this->setError($msg);
			return false;
		}

		$emailModel = $this->getModel('cbemail');
		if (!$emailModel->populate()) {
			$this->setError($model->getErrors());
			return false;
		}
		
		$this->sendEmail();
	}

	function checkPermission() {
		$model = $this->getModel('cbmailing');
		// Create the list of possible TO groups for this user
		$toList = $model->getListOfOkayToGroups();
		// Check if the group specified for the send is in the possible list
		return $toList[$group] == 1;
	}

	function sendEmail() {
		$e = $this->emailModel;
		$p = JComponentHelper::getParams('com_cbmailing');
		if ($p->get('cbMailingConfig_mmMethod') === 1)
		{
			$result = JUtility::sendMail( $e->fromEmail, $e->fromName, $e->recievers , $e->subject, $e->messageBody, $e->mode, NULL, $e->bbcEmail, $e->attachmet, $e->replyEmail, $e->replyName );
		} else {
			foreach ($e->recievers as $receiver) {
				$result = JUtility::sendMail( $e->fromEmail, $e->fromName, $e->recievers , $e->subject, $e->messageBody, $e->mode, NULL, $e->bbcEmail, $e->attachmet, $e->replyEmail, $e->replyName );
			}
		}

		// MRCB 20090324 - V2.3.3 - First attempt at J1.5 legacy support
		if (is_object( $result ) && isset( $result->message )) {
				$sendError = $result->message .JText::_( 'CB_MAILING_ADMIN_SENDERRORLINKTEXT' ). $mailedDetails;
		}

		//TODO: implementare successCOunt in maniera giusta
		/*$msg = JText::_( 'CB_MAILING_EMAILSENTTOXUSERS' ) .' '. $successCount
		.' ('. JText::_( 'CB_MAILING_EMAILSENTTOXUSERSTOTAL' ) ." ". count( $rows )
		. (count( $rows ) != $sendToCount ? '/'. $sendToCount : '')
		.')';*/
	}
}