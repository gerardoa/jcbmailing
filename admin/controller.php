<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class CbmailingController extends JController
{
	function cancel() {
		$url = 'index.php?option=com_cbmailing';
		$this->setRedirect($url);
	}

	function permissions() {
		$view = $this->getView( 'permissions', 'html' );
		$model = $this->getModel( 'cbmailing' );
		$view->setModel( $model, true );
		$view->display();
	}

	function addPerm() {
		$model = $this->getModel( 'permission' );
		$model->save();
		$this->setRedirect('index.php?option=com_cbmailing&task=permissions');
	}

	function delPerm() {
		$model = $this->getModel( 'permission' );
		$model->delete();
		$this->setRedirect('index.php?option=com_cbmailing&task=permissions');
	}

	function configure() {
		$view = $this->getView( 'configuration', 'html' );
		$view->display();
	}

	function saveConfig() {
		$component = 'com_cbmailing';

		$table =& JTable::getInstance('component');
		if (!$table->loadByOption( $component ))
		{
			JError::raiseWarning( 500, 'Not a valid component' );
			return false;
		}

		$post = JRequest::get( 'post' );
		// threat checkbox as radio to sync with preferences view
		$checkBox = array('cbMailingConfig_feAllowAtt'    , 'cbMailingConfig_feAllowHTML',
						  'cbMailingConfig_feAllowSigOver',	'cbMailingConfig_allAddr',
						  'cbMailingConfig_incBlocked'    ,	'cbMailingConfig_debug');
		foreach ($checkBox as $cb) {
			$post['params'][$cb] = empty($post['params'][$cb]) ? 0 : 1;
		}
		$post['option'] = $component;
		$table->bind( $post );

		// pre-save checks
		if (!$table->check()) {
			JError::raiseWarning( 500, $table->getError() );
			return false;
		}

		// save the changes		
		if(!$table->store()) {
			JError::raiseWarning( 500, $table->getError() );
			return false;
		}
		
		$this->setRedirect('index.php?option=com_cbmailing&task=configure', JText::_( 'CB_MAILING_ADMIN_CONFIGSAVED' ));		
		return true;
	}

	function showMembers() {
		$view = $this->getView( 'members', 'html' );
		$model = $this->getModel( 'cbmailing' );
		$view->setModel( $model, true );
		$view->display();
	}
}
?>