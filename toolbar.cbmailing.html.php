<?php

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );


class TOOLBAR_cbmailing {
	function _DEFAULT() {
		mosMenuBar::startTable();
		mosMenuBar::custom('send','publish.png','publish_f2.png','Send Mail',false);
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}

	function _PERMISSIONS() {
		mosMenuBar::startTable();
		mosMenuBar::save('addPerm');
		mosMenuBar::deleteList('The permission will be deleted and cannot be undone!','delPerm');
		mosMenuBar::spacer();
		mosMenuBar::cancel();
		mosMenuBar::endTable();
	}
}
?>