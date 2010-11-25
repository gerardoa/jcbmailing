<?php
/**
* @version 2.3.4J1.5N
* @package CB Mailing list
* @copyright (c) 2006-2008 - Erik Happaerts  [erik@happaerts.be] / Guus Koning [guus.koning@hccnet.nl]
* @copyright (c) 2007-2009 - Mark Bradley (OSPS Ltd)
* @based on Mambo admin.massmail.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

function com_install ()
{
	$url = "index2.php?option=com_cbmailing";
	$msg = "CbMailing Successfully Installed";
	$app = &JFactory::getApplication();	// J1.5
	$app->redirect( $url, $msg );	// J.15
}


?>
                                          