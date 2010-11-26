<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class CbmailingModelPermission extends JModel
{
	function save() {
		$app = JFactory::getApplication();
		$fromgroup	= JRequest::getInt( 'mm_fromgroup', null, 'post' );
		$togroup	= JRequest::getInt( 'mm_togroup',   null, 'post' );

		// let the user know what they did wrong
		if ($fromgroup == null) {
			$msg = JText::_( 'CB_MAILING_ADMIN_FROMGRPNOTSET' );
			$this->setError($msg);
			$app->enqueueMessage($msg);
		}
		if ($togroup == null) {
			$msg = JText::_( 'CB_MAILING_ADMIN_TOGRPNOTSET' );
			$this->setError($msg);
			$app->enqueueMessage($msg);
		}
		if($this->getError()) return false;

		// Only try to do something if both groups are set
		// First check if there is already such a permission, if so, don't add it in and let the user know they tried this
		$query = 'SELECT id ' .
				 'FROM #__cbmailing_permissions ' . 
				 'WHERE toid   =' . $togroup .
				 '  AND fromid =' . $fromgroup;
		if($this->_getListCount($query)) {
			$msg = JText::_( 'CB_MAILING_ADMIN_EXISTINGPERM' );
			$this->setError($msg);
			$app->enqueueMessage($msg);
			return false;
		}

		$query = 'INSERT INTO #__cbmailing_permissions (id, toid, fromid) ' .
				 'VALUES (NULL, ' . $togroup . ',' . $fromgroup . ')';
		$db = $this->getDBO();
		$db->setQuery( $query );
		if (!$db->query()) {
			JError::raiseNotice('DB', $db->getErrorMsg());
			return false;
		}
		$app->enqueueMessage(JText::_( 'CB_MAILING_ADMIN_PERMADDED' ));
		return true;
	}

	function delete() {
		$app = JFactory::getApplication();
		$db  = $this->getDBO();
		$ids = JRequest::getVar( 'ids', null, 'post', 'array' );
		JArrayHelper::toInteger($ids, 0);

		foreach ($ids as $id) {
			$query = 'DELETE FROM #__cbmailing_permissions ' .
				     'WHERE id=' . $id;
			$db->setQuery( $query );
			if (!$db->query()) {
				JError::raiseNotice('DB', $db->getErrorMsg());
			} else {
				$deletedCount++;
			}
		}
		if ($deletedCount > 0) {
			$app->enqueueMessage(JText::_( 'CB_MAILING_ADMIN_PERMISSIONSDEL' ) . $deletedCount);
			return true;
		}
		return false;
	}

}