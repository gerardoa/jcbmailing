<?php
jimport('joomla.application.component.view');

class CbmailingViewPermissions extends JView
{
	function display( $tpl = null )
	{
		JToolBarHelper::title( JText::_('Permissions') );
		JToolBarHelper::save('addPerm');
		JToolBarHelper::deleteList( JText::_( 'CB_MAILING_ADMIN_PERMDELWARNING' ),'delPerm');
		JToolBarHelper::spacer();
		JToolBarHelper::custom('showMembers','publish.png','publish_f2.png', JText::_( 'CB_MAILING_ADMIN_LISTMEMBUTTONTEXT' ),false);
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();

		$this->permissionsForm();
		parent::display( $tpl );
	}

	function permissionsForm() {
		$model = $this->getModel();				
		$lists['gidto'] = JHTML::_('select.genericlist', $this->get('tolists2'), 'mm_togroup', 'size="10"', 'value', 'text', 0 );
		$lists['gidfrom'] = JHTML::_('select.genericlist', $this->get('fromlists'), 'mm_fromgroup', 'size="10"', 'value', 'text', 0 );		
		$lists['permissions'] = $this->get('permissions');
		$this->assignRef('lists', $lists);
	}

}