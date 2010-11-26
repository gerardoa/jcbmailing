<?php
jimport('joomla.application.component.view');

class CbmailingViewMembers extends JView
{
	function display( $tpl = null )
	{
		JToolBarHelper::title( JText::_('Members') );
		
		$lists = $this->get('members');
		$this->assignRef('lists', $lists);
		
		parent::display( $tpl );
	}
}