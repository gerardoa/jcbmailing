<?php 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
global $option;
$lists = $this->lists; 
?>		
		<script language="javascript" type="text/javascript">
			function submitbutton(pressbutton) {
				var form = document.adminForm;
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return true;
				}
				// do field validation
				var alertmessage = "";
				if (pressbutton == 'addPerm') {
					if (getSelectedValue('adminForm','mm_fromgroup') < 0){
						alertmessage = "<?php echo JText::_( 'CB_MAILING_ADMIN_PERMSETNOFROMGRP' ) ?>";
					}
					if (getSelectedValue('adminForm','mm_togroup') < 0){
						alertmessage = alertmessage + "<?php echo JText::_( 'CB_MAILING_ADMIN_PERMSETNOTOGRP' ) ?>";
					}
					if (alertmessage != "") {
						alert( alertmessage );
						return false;
					} else {
						submitform( pressbutton );
						return true;
					}
				} else if (pressbutton == 'delPerm') {
					// no need to test if a check box is checked, since the in-form handling does this (we hope!)
					submitform( pressbutton );
					return true;
				} else {
					submitform( pressbutton );
					return true;
				}
				return true;
			}
		</script>

		<form action="index2.php" name="adminForm" method="post" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th class="massemail">
				<?php echo JText::_( 'CB_MAILING_ADMIN_PERMSETTITLE' ) ?>
			</th>
		</tr>
		</table>

<?php
			$permsHTML = "";
			$rowType = 0;
			if (count( $lists['permissions'] ) > 0) {
				foreach ($lists['permissions'] as $permission) {
					$permsHTML .= "
	<tr class=\"row". $rowType ."\">
		<td></td>
		<td>
			<input type=\"checkbox\" name=\"ids[]\" value=\"". $permission->id."\" onclick=\"isChecked(this.checked)\"/>
		</td>
		<td>
			". $permission->fromtitle . "
		</td>
		<td>
			". $permission->totitle . "
		</td>
	</tr>
";
					$rowType = ($rowType == 0 ? 1 : 0);
				}
			}
			if ( $permsHTML != "") {
				echo "
<table class=\"adminlist\">
	<tr>
		<th>". JText::_( 'CB_MAILING_ADMIN_PERMSETEXISTING' ) ."</th>
		<th></th>
		<th>". JText::_( 'CB_MAILING_ADMIN_PERMSETLEFTGRP' ) ."</th>
		<th>". JText::_( 'CB_MAILING_ADMIN_PERMSETRIGHTGRP' ) ."</th>
	</tr>
	<tr>
		<td colspan=\"4\">". JText::_( 'CB_MAILING_ADMIN_PERMSETHOWTODEL' ) ."</td>
	</tr>
". $permsHTML ."
</table>
";
			}
?>
		<table class="adminform">
		<tr>
			<th colspan="3">
				<?php echo JText::_( 'CB_MAILING_ADMIN_PERMSETNEWPERM' ) ?>
			</th>
		</tr>
		<tr>
			<td width="150" valign="top">
				<?php echo JText::_( 'CB_MAILING_ADMIN_PERMSETNEWPERMINSTR' ) ?>
			</td>
			<td width="42%">
				<?php echo JText::_( 'CB_MAILING_ADMIN_PERMSETNEWPERMLEFT' ) ?><br />
				<?php echo $lists['gidfrom']; ?>
			</td>
			<td width="43%">
				<?php echo JText::_( 'CB_MAILING_ADMIN_PERMSETNEWPERMRIGHT' ) ?><br />
				<?php echo $lists['gidto']; ?>
			</td>
		</tr>
		</table>

		<input type="hidden" value="0" name="boxchecked"/>
		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value=""/>
		</form>