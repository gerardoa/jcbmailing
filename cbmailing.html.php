<?php

// no direct access
defined( '_VALID_MOS' ) or die( 'Restricted access' );

class HTML_cbmailing {
	function messageForm( &$lists, $option ) {
		?>
		<script language="javascript" type="text/javascript">
			//function getSelectedValue(
			function submitbutton(self) {
				var form = document.adminForm;
				/*if (pressbutton == 'cancel') {
					submitform( pressbutton );
					return;
				} */
				// do field validation
				if (form.mm_subject.value == ""){
					alert( "<?php echo _CB_MAILING_FILLINSUBJECT ?>" );
					return false;
				} else if (getSelectedValue('adminForm','mm_group') < 0){
					alert( "<?php echo _CB_MAILING_SELECTAGROUP ?>" );
					return false;
				} else if (form.mm_message.value == ""){
					alert( "<?php echo _CB_MAILING_FILLINMESSAGE ?>" );
					return false;
				} else {
					submitform( 'send' );
				}
			}
		</script>

		<form action="index.php" name="adminForm" method="post" enctype="multipart/form-data" onsubmit="return submitbutton(this);">
		<table class="adminheading">
		<tr>
			<th class="massemail">
			<?php echo _CB_MAILING_TITLE ?>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="2">
			<?php echo _CB_MAILING_SELECTGROUPTEXT ?>
			</th>
		</tr>
		<tr>
			<td width="150" valign="top">
			<?php echo _CB_MAILING_GROUPTEXT ?>
			</td>
			<td width="85%">
			<?php echo $lists['gid']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _CB_MAILING_HMTLMODETEXT ?>
			</td>
			<td>
			<input type="checkbox" name="mm_mode" value="1" />
			</td>
		</tr>
		<tr>
			<td>
			<?php echo _CB_MAILING_SUBJECTTEXT ?>
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
			'. _CB_MAILING_ATTACHFILETEXT .'
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
			<?php echo _CB_MAILING_MESSAGETEXT ?>
			</td>
			<td>
			<textarea cols="80" rows="25" name="mm_message" class="inputbox"></textarea>
			</td>
		</tr>
		</table>

		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value="send"/>
		<input type="submit" class="button" name="buttonpress" value="<?php echo _CB_MAILING_SENDEMAILTEXT ?>" />
		</form>
		<?php
	}

	function errorMessage( $msg, $option ) {
		echo "$msg";
	}
}
?>