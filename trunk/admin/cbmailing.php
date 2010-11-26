<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_COMPONENT.DS.'controller.php' );
require_once( JPATH_COMPONENT.DS.'controllers'.DS.'sendmail.php' );

$task = JRequest::getCmd('task');
$suffix = ($task === 'send' || $task === 'mailing' || empty($task) ) ? 'SendMail' : '';
$classname = 'CbmailingController'.$suffix;
$controller = new $classname();
$controller->execute($task);
$controller->redirect();
?>