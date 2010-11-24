<?php
/**
* @version 2.3.4J1.5N
* @package CB Mailing list
* @copyright (c) 2006-2008 - Erik Happaerts  [erik@happaerts.be] / Guus Koning [guus.koning@hccnet.nl]
* @copyright (c) 2007-2009 - Mark Bradley (OSPS Ltd)
* @based on Mambo admin.massmail.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


class TOOLBAR_cbmailing {
	function _DEFAULT() {
		// mosMenuBar::startTable();	// J1.0
		JToolBarHelper::custom('send','publish.png','publish_f2.png', JText::_( 'CB_MAILING_ADMIN_SENDBUTTONTEXT' ),false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		// mosMenuBar::endTable();	// J1.0
	}

	function _CONFIGURATION() {
		// mosMenuBar::startTable();	// J1.0
		JToolBarHelper::save('saveConfig');
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		// mosMenuBar::endTable();	// J1.0
	}

	function _PERMISSIONS() {
		// mosMenuBar::startTable();	// J1.0
		JToolBarHelper::save('addPerm');
		JToolBarHelper::deleteList( JText::_( 'CB_MAILING_ADMIN_PERMDELWARNING' ),'delPerm');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('showMembers','publish.png','publish_f2.png', JText::_( 'CB_MAILING_ADMIN_LISTMEMBUTTONTEXT' ),false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		// mosMenuBar::endTable();	// J1.0
	}

	function _TEST() {
		// mosMenuBar::startTable();	// J1.0
		JToolBarHelper::cancel();
		// mosMenuBar::endTable();	// J1.0
	}
}
?>