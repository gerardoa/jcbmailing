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
				
		parent::display( $tpl );		
	}
	
function permissionsForm( $option ) {
	// global $database;	// J1.0
	// global $acl;			// J1.0
	$database = &JFactory::getDBO();	// J1.5
	$acl	=& JFactory::getACL();		// J1.5

	// get list of groups as destinations
	$query = "SELECT listid AS value, title AS text FROM #__comprofiler_lists WHERE published=1 ORDER BY ordering";
	$database->setQuery( $query );
	$lists['tolists'] = $database->loadObjectList();
	// $lists['gidto'] = mosHTML::selectList( $lists['tolists'], 'mm_togroup', 'size="10"', 'value', 'text', 0 );
	// MRCB - J1.5
	$lists['gidto'] = JHTML::_('select.genericlist', $lists['tolists'], 'mm_togroup', 'size="10"', 'value', 'text', 0 );
	// MRCB - J1.5

	// get list of groups for those allowed to send (includes unpublished lists)
	$query = "SELECT listid AS value, title AS text FROM #__comprofiler_lists ORDER BY ordering";
	$database->setQuery( $query );
	$lists['fromlists'] = $database->loadObjectList();
	// $lists['gidfrom'] = mosHTML::selectList( $lists['fromlists'], 'mm_fromgroup', 'size="10"', 'value', 'text', 0 );
	// MRCB - J1.5
	$lists['gidfrom'] = JHTML::_('select.genericlist', $lists['fromlists'], 'mm_fromgroup', 'size="10"', 'value', 'text', 0 );
	// MRCB - J1.5

	// get list of existing permissions
	$query = "SELECT a.id as id, b1.title as totitle, b2.title as fromtitle 
				FROM #__cbmailing_permissions as a, #__comprofiler_lists as b1, #__comprofiler_lists as b2 
				WHERE a.toid=b1.listid AND a.fromid=b2.listid;";
	$database->setQuery( $query );
	$lists['permissions'] = $database->loadObjectList();

	HTML_cbmailing::permissionsForm( $lists, $option );
}
	
}