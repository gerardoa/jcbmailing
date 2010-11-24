<?php
jimport('joomla.application.component.view');

class CbmailingViewMailing extends JView
{
	function display( $tpl = null )
	{
		JToolBarHelper::title( JText::_('Mailing') );
		JToolBarHelper::publish('send', JText::_('CB_MAILING_ADMIN_SENDBUTTONTEXT'));
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::preferences( 'com_campioni', 600, 700 );

		$this->messageForm();
		
		parent::display( $tpl );		
	}
	
/* ............................................................................. */
	function messageForm() {

		// Need logic in here ot:
		// 1 - are there any permissions? If no bail out
		// 2 - is the user a member of any of the FROM groups? If no bail out
		// 3 - build a list of all FROM groups of which user is a member
		// 4 - build unique list of all TO groups permitted from FROM groups of which users is a member
		// 5 - display that list

		$allowedToSend = false;

		$toList = $this->get('ListOfOkayToGroups');
		$allowedToSend = (count( $toList ) != 0);
		if  ( !$allowedToSend ) {
			// No permissions apply to this user - cannot send
			$this->setError(JText::_( 'CB_MAILING_NOPERMISSIONATALL' ));
		} else {
			// Now have to create an HTML select list of TO groups based on the built list
			$model = $this->getModel();
			$users = $model->getToLists($toList);
			$lists['gid'] = JHTML::_('select.genericlist', $users, 'mm_group', 'size="10"', 'value', 'text', 0 );
			$this->assignRef('lists', $lists);
			$params = JComponentHelper::getParams('com_cbmailing');
			$this->assignRef('signature', $params->get('signature'));
		}
	}
}