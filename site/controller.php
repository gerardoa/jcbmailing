<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CbmailingController extends JController
{
	function __construct()
	{
		parent::__construct();
		//$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.DS."models");
	}

	function display()
	{
		$this->mailing();
	}

	function send() {
		$cbm = new cbmailings();
		$cbm->sendMail();
	}

	function cancel() {
		$url = 'index.php';
		$this->setRedirect($url);
	}

	function mailing() {
		$cbm = new cbmailings();
		$cbm->messageForm( $option );
	}
}
