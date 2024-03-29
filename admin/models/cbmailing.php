<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class CbmailingModelCbmailing extends JModel
{
	var $params;

	function CbmailingModelCbmailing() {
		parent::__construct();
		$this->params = JComponentHelper::getParams( 'com_cbmailing' );
	}

	function getToLists() {
		$toList = $this->getListOfOkayToGroups();
		$query = "SELECT listid AS value, title AS text" .
					" FROM #__comprofiler_lists" .
					" WHERE published=1" . 
					" AND (";
		$listids = "";
		$db = $this->getDBO();
		foreach (array_keys($toList) as $thisList) {
			$listids .= ($listids == "" ? "" : " OR " ) .
							" listid=". $db->Quote( $thisList );
		}
		$query .= $listids .") ORDER BY ordering";
		return  $this->_getList($query);
	}


	/* ............................................................................. */
	function getListOfOkayToGroups() {

		$my = &JFactory::getUser();

		$toList = array();
		$query = "SELECT id, fromid, toid
					FROM #__cbmailing_permissions;";

		$permissions = $this->_getList($query);


		foreach ($permissions as $permission) {
			$users = $this->listMembers( $permission->fromid, false, null );
			foreach( $users as $thisUser ) {
				if ($thisUser->id == $my->id) {
					// User was a member, so add permission from list to list so far (to display)
					$toList[$permission->toid] = 1;
					break;
				}
			}
		}

		return $toList;

	}

	/* ............................................................................. */
	// New function in V2.3.0
	function listMembers( $group, $includeAllAddresses, $emailFieldList )
	{
		$acl	=& JFactory::getACL();		// J1.5

		// RECUPERA IL usergroupids e il filtro DATO L'ID di una lista
		$query = "SELECT usergroupids,filterfields " .
				" FROM #__comprofiler_lists" .
				" WHERE listid = $group";
		$groupResults = $this->_getList($query);
		$filterby = $groupResults[0]->filterfields;
		$userGroupIds = $groupResults[0]->usergroupids;


		// V2.2 - find the fields that are e-mail addresses
		$extraEmailFields = "";
		if ( $includeAllAddresses && ( $emailFieldList != NULL)) {
			foreach ($emailFieldList as $field) {
				$extraEmailFields .= ",". $field->name;
			}
		}

		$allusergids=array();
		$usergids=explode(",",$userGroupIds);
		foreach($usergids AS $usergid) {
			$allusergids[]=$usergid;
			// 29 is the GID for the front end, 30 is the GID for the backend
			if ($usergid==29 || $usergid==30) {
				$groupchildren = array();
				$groupchildren = $acl->get_group_children( $usergid, 'ARO','RECURSE' );
				$allusergids = array_merge($allusergids,$groupchildren);
			}

		}

		$usergids = "-1";
		$usergids=implode(",",$allusergids);


		// Get all users email
		//$query = "SELECT email FROM #__users u, #__comprofiler ue WHERE u.id=ue.id AND u.block!=1 and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1";
		//$query = "SELECT * FROM #__users u, #__comprofiler ue WHERE u.id=ue.id and ue.approved=1 AND ue.banned!=1 AND ue.confirmed=1";
		$query = "SELECT u.id AS id,name,username,email". $extraEmailFields .
				" FROM #__users u, #__comprofiler ue" . 
				" WHERE u.id=ue.id     " .
				"  AND  ue.approved=1  " . 
				"  AND  ue.banned!=1   " .
		        "  AND  ue.confirmed=1 ".
				"  AND  u.gid IN (". $usergids .")";
		if (! $this->params->get('incBlocked'))
		{
			$query .= " AND u.block!=1";
		}
		$selection = $this->utf8RawUrlDecode(substr($filterby,1));
		if ($selection != "") {
			$query .= " AND " . $selection;
		}

		return $this->_getList($query);
	}

	function getToLists2() {
		// get list of groups as destinations
		$query = 'SELECT listid AS value, title AS text ' .
		         'FROM #__comprofiler_lists ' .
		         'WHERE published=1 ' .
		         'ORDER BY ordering';
		return $this->_getList($query);
	}

	function getFromLists() {
		// get list of groups for those allowed to send (includes unpublished lists)
		$query = 'SELECT listid AS value, title AS text ' .
		         'FROM #__comprofiler_lists ' .
		         'ORDER BY ordering';
		return $this->_getList($query);
	}

	function getPermissions() {
		// list of existing permissions
		$query = 'SELECT a.id as id, b1.title as totitle, b2.title as fromtitle ' .
				 'FROM #__cbmailing_permissions as a, ' . 
				 '     #__comprofiler_lists     as b1,' .
		         '     #__comprofiler_lists     as b2 ' .
				 'WHERE a.toid   = b1.listid ' .
				 '  AND a.fromid = b2.listid ';
		return $this->_getList($query);
	}

	function getMembers( ) {
		$ids = JRequest::getVar( 'ids', null, 'post', 'array' );
		JArrayHelper::toInteger($ids, 0);

		if (count( $ids ) > 0) {

			$lists['groupData'] = array();
			$lists['includeAll'] = $this->params->get('cbMailingConfig_allAddr');
			$emailFieldList = NULL;
			if ( $lists['includeAll'] ) {
				$emailFieldList = $this->extraAddressFields();
			}
			$lists['emailFieldList'] = $emailFieldList;

			foreach ($ids as $pid) {
				// get list of existing permissions
				$query = 'SELECT p.id as id, p.toid as toid, l1.title as totitle, p.fromid as fromid, l2.title as fromtitle
						FROM #__cbmailing_permissions as p ,' .
						'    #__comprofiler_lists     as l1,' .
				        '    #__comprofiler_lists     as l2 ' . 
						'WHERE p.toid   = l1.listid ' .
				 	    '  AND p.fromid = l2.listid ' . 
				 	    '  AND p.id     = '. $pid;
				$data = array();
				$data['permission']  = $this->_getList($query);
				$data['fromMembers'] = $this->listMembers( $data['permission'][0]->fromid, false,                $emailFieldList );
				$data['toMembers']   = $this->listMembers( $data['permission'][0]->toid,   $lists['includeAll'], $emailFieldList );
				$data['fromCount']   = count($data['fromMembers']);
				$data['toCount']     = count($data['toMembers']);
				$lists['groupData'][$pid] = $data;
			}
			return $lists;
		}
	}
	
	function extraAddressFields() {
		$query = 'SELECT name ' .
				 'FROM #__comprofiler_fields ' .
		         'WHERE type = emailaddress';
		return $this->_getList($query);
	}

	// this works on the list filter field
	function utf8RawUrlDecode ($source) {
		$decodedStr = '';
		$pos = 0;
		$len = strlen ($source);
		while ($pos < $len) {
			$charAt = substr ($source, $pos, 1);
			if ($charAt=='%') {
				$pos++;
				$charAt = substr ($source, $pos, 1);
				if ($charAt=='u') { // we got a unicode character
					$pos++;
					$unicodeHexVal = substr ($source, $pos, 4);
					$unicode = hexdec ($unicodeHexVal);
					$entity = "&#". $unicode . ';';
					$decodedStr .= utf8_encode ($entity);
					$pos += 4;
				} else { // we have an escaped ascii character
					$hexVal = substr ($source, $pos, 2);
					$decodedStr .= chr (hexdec ($hexVal));
					$pos += 2;
				}
			} else {
				$decodedStr .= $charAt;
				$pos++;
			}
		}
		return $decodedStr;
	}
}