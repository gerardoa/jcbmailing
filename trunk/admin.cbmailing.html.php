<?php

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_cbmailing {
	function messageForm( &$lists, $option ) {
		?>
		<script language="javascript" type="text/javascript">
			//function getSelectedValue(
			function submitbutton(pressbutton) {
				var form = document.adminForm;
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}
				// do field validation
				if (form.mm_subject.value == ""){
					alert( "Please fill in the subject" );
				} else if (getSelectedValue('adminForm','mm_group') < 0){
					alert( "Please select a group" );
				} else if (form.mm_message.value == ""){
					alert( "Please fillin the message" );
				} else {
					submitform( pressbutton );
				}
			}
		</script>

		<form action="index2.php" name="adminForm" method="post" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th class="massemail">
			CbMailing
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="2">
			Details
			</th>
		</tr>
		<tr>
			<td width="150" valign="top">
			Group:
			</td>
			<td width="85%">
			<?php echo $lists['gid']; ?>
			</td>
		</tr>
		<tr>
			<td>
			Send in HTML mode:
			</td>
			<td>
			<input type="checkbox" name="mm_mode" value="1" />
			</td>
		</tr>
		<tr>
			<td>
			Subject:
			</td>
			<td>
			<input class="inputbox" type="text" name="mm_subject" value="" size="50"/>
			</td>
		</tr>
		<?php
			if ((bool)ini_get('file_uploads')) {
				print '
		<tr>
			<td>
			Attach a file:
			</td>
			<td>
			<input class="inputbox" type="file" name="mm_attach" size="50"/>
			</td>
		</tr>
';
			}
		?>
		<tr>
			<td valign="top">
			Message:
			</td>
			<td>
			<textarea cols="80" rows="25" name="mm_message" class="inputbox"></textarea>
			</td>
		</tr>
		</table>

		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value=""/>
		</form>
		<?php
	}

	function permissionsForm( &$lists, $option ) {
		?>
		<script language="javascript" type="text/javascript">
			function submitbutton(pressbutton) {
				var form = document.adminForm;
				if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				}
				// do field validation
				var alertmessage = "";
				if (pressbutton == 'addPerm') {
					if (getSelectedValue('adminForm','mm_fromgroup') < 0){
						alertmessage = "No FROM group selected; ";
					}
					if (getSelectedValue('adminForm','mm_togroup') < 0){
						alertmessage = alertmessage + "No TO group selected";
					}
					if (alertmessage != "") {
						alert( alertmessage );
					} else {
						submitform( pressbutton );
					}
				} else if (pressbutton == 'delPerm') {
					// no need to test if a check box is checked, since the in-form handling does this (we hope!)
					submitform( pressbutton );
				} else {
					submitform( pressbutton );
				}
			}
		</script>

		<form action="index2.php" name="adminForm" method="post" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th class="massemail">
				CbMailing - Permission Setting
			</th>
		</tr>
		</table>

		<?php
			$permsHTML = "";
			$rowType = 0;
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
			if ( $permsHTML != "") {
				echo "
<table class=\"adminlist\">
	<tr>
		<th>Existing permissions</th>
		<th></th>
		<th>Members of this group may send to...</th>
		<th>...members of this group</th>
	</tr>
	<tr>
		<td colspan=\"4\">To delete permissions, check the box and then click Delete</td>
	</tr>
". $permsHTML ."
</table>
";
			}
		?>
		<table class="adminform">
		<tr>
			<th colspan="3">
				New Permissions
			</th>
		</tr>
		<tr>
			<td width="150" valign="top">
				To add a new permission choose one from each list FROM and TO and then click Save
			</td>
			<td width="42%">
				List permitted to send FROM:<br />
				<?php echo $lists['gidfrom']; ?>
			</td>
			<td width="43%">
				is permitted to send TO:<br />
				<?php echo $lists['gidto']; ?>
			</td>
		</tr>
		</table>

		<input type="hidden" value="0" name="boxchecked"/>
		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value=""/>
		</form>
		<?php
	}

}
?>