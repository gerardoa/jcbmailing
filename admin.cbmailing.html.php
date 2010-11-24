<?php
/**
* @version 2.3.4J1.5N
* @package CB Mailing list
* @copyright (c) 2006-2008 - Erik Happaerts  [erik@happaerts.be] / Guus Koning [guus.koning@hccnet.nl]
* @copyright (c) 2007-2009 - Mark Bradley (OSPS Ltd)
* @based on Mambo admin.massmail.php
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class HTML_cbmailing {
	function configureForm( &$cbMailingConfig, $configWritable, $option ) {
		$numCols = 2;
		$colSizes[0] = 'width="200px"';
		$colSizes[1] = '';
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
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGTITLE' ) ?>
			</th>
		</tr>
		<tr>
			<td>
				<?php
				if (! $configWritable ) {
					echo JText::_( 'CB_MAILING_ADMIN_CONFIGNOTWRITABLE' );
				}
				?>
			</td>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="<?php echo $numCols ?>">
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGFETITLE' ) ?>
			</th>
		</tr>
		<tr>
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGFEALLOWATT' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="cbMailingConfig_feAllowAtt" <?php echo ($cbMailingConfig["feAllowAtt"] ? "checked" : "") ?> />
			</td>
		</tr>
		<tr>
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGFEALLOWHTML' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="cbMailingConfig_feAllowHTML" <?php echo ( $cbMailingConfig["feAllowHTML"] ? "checked" : "")  ?> />
			</td>
		</tr>
		<tr>
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGFEALLOWSIGOVER' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="cbMailingConfig_feAllowSigOver" <?php echo ( $cbMailingConfig["feAllowSigOver"] ? "checked" : "") ?> />
			</td>
		</tr>
		<tr>
			<th colspan="<?php echo $numCols ?>">
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMTITLE' ) ?>
			</th>
		</tr>
		<?php $rowID = 0; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td colspan="<?php echo $numCols ?>">
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMHELP' ) ?>
			</td>
		</tr>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>file_uploads</td>
			<td <?php echo $colSizes[1] ?>>
<?php 
				echo ini_get('file_uploads') .
					(ini_get('file_uploads') == 0 ? JText::_( 'CB_MAILING_ADMIN_CONFIGMMHELPUPLOADS0' ) :
					JText::_( 'CB_MAILING_ADMIN_CONFIGMMHELPUPLOADS1' )); 
?>
			</td>
		</tr>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>max_execution_time</td>
			<td <?php echo $colSizes[1] ?>>
<?php 
				echo ini_get('max_execution_time') . JText::_( 'CB_MAILING_ADMIN_CONFIGMMHELPMAXEXEC' ); 
?>
			</td>
		</tr>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>post_max_size</td>
			<td <?php echo $colSizes[1] ?>>
<?php 
				echo ini_get('post_max_size') . JText::_( 'CB_MAILING_ADMIN_CONFIGMMHELPMAXPOST' ); 
?>
			</td>
		</tr>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>upload_max_filesize</td>
			<td <?php echo $colSizes[1] ?>>
<?php 
				echo ini_get('upload_max_filesize') . JText::_( 'CB_MAILING_ADMIN_CONFIGMMHELPMAXUP' ); 
?>
			</td>
		</tr>

		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMMETHOD' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="radio" name="cbMailingConfig_mmMethod" value="1"<?php
				if ($cbMailingConfig["mmMethod"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMMETHOD1' ) ?><br />
				<input type="radio" name="cbMailingConfig_mmMethod" value="2"<?php
				if ($cbMailingConfig["mmMethod"] == "2") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMMETHOD2' ) ?>
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMFROM' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="radio" name="cbMailingConfig_mmFrom" value="1"<?php
				if ($cbMailingConfig["mmFrom"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMFROM1' ) ?><br />
				<input type="radio" name="cbMailingConfig_mmFrom" value="2"<?php
				if ($cbMailingConfig["mmFrom"] == "2") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMFROM2' ) ?><br />
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMFROMLABEL' ) ?><br />
				<input type="text" name="cbMailingConfig_mmFromAddr" size="50" value="<?php echo $cbMailingConfig["mmFromAddr"] ?>">
				<input type="text" name="cbMailingConfig_mmFromDesc" size="50" value="<?php echo $cbMailingConfig["mmFromDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTO' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="radio" name="cbMailingConfig_mmReplyTo" value="1"<?php
				if ($cbMailingConfig["mmReplyTo"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTO1' ) ?><br />
				<input type="radio" name="cbMailingConfig_mmReplyTo" value="2"<?php
				if ($cbMailingConfig["mmReplyTo"] == "2") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTO2' ) ?><br />
				<input type="radio" name="cbMailingConfig_mmReplyTo" value="3"<?php
				if ($cbMailingConfig["mmReplyTo"] == "3") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTO3' ) ?><br />
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTOLABEL' ) ?><br />
				<input type="text" name="cbMailingConfig_mmReplyToAddr" size="50" value="<?php echo $cbMailingConfig["mmReplyToAddr"] ?>">
				<input type="text" name="cbMailingConfig_mmReplyToDesc" size="50" value="<?php echo $cbMailingConfig["mmReplyToDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMTO' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="radio" name="cbMailingConfig_mmTo" value="1"<?php
				if ($cbMailingConfig["mmTo"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMTO1' ) ?><br />
				<input type="radio" name="cbMailingConfig_mmTo" value="2"<?php
				if ($cbMailingConfig["mmTo"] == "2") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMTO2' ) ?><br />
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMTOLABEL' ) ?><br />
				<input type="text" name="cbMailingConfig_mmToAddr" size="50" value="<?php echo $cbMailingConfig["mmToAddr"] ?>">
				<input type="text" name="cbMailingConfig_mmToDesc" size="50" value="<?php echo $cbMailingConfig["mmToDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCC' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="radio" name="cbMailingConfig_mmBCC" value="1"<?php
				if ($cbMailingConfig["mmBCC"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCC1' ) ?><br />
				<input type="radio" name="cbMailingConfig_mmBCC" value="2"<?php
				if ($cbMailingConfig["mmBCC"] == "2") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCC2' ) ?><br />
				<input type="radio" name="cbMailingConfig_mmBCC" value="3"<?php
				if ($cbMailingConfig["mmBCC"] == "3") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCC3' ) ?><br />
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCCLABEL' ) ?><br />
				<input type="text" name="cbMailingConfig_mmBCCAddr" size="50" value="<?php echo $cbMailingConfig["mmBCCAddr"] ?>">
				<input type="text" name="cbMailingConfig_mmBCCDesc" size="50" value="<?php echo $cbMailingConfig["mmBCCDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGALLADDR' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="cbMailingConfig_allAddr" <?php echo ( $cbMailingConfig["allAddr"] ? "checked" : "")  ?> />
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGSIGNATURE' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<textarea cols="80" rows="10" name="cbMailingConfig_signature" class="inputbox"><?php echo $cbMailingConfig["signature"] ?></textarea>
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGINCBLOCK' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="cbMailingConfig_incBlocked" <?php echo ( $cbMailingConfig["incBlocked"] ? "checked" : "") ?> />
			</td>
		</tr>
		<tr>
			<th colspan="<?php echo $numCols ?>">
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGTITLE' ) ?>
			</th>
		</tr>
		<?php $rowID = 0; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUG' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="cbMailingConfig_debug" <?php echo ( $cbMailingConfig["debug"] ? "checked" : "") ?> />
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGFROM' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGFROMLABEL' ) ?><br />
				<input type="text" name="cbMailingConfig_debugFromAddr" size="50" value="<?php echo $cbMailingConfig["debugFromAddr"] ?>">
				<input type="text" name="cbMailingConfig_debugFromDesc" size="50" value="<?php echo $cbMailingConfig["debugFromDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGTO' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGTOLABEL' ) ?><br />
				<input type="text" name="cbMailingConfig_debugToAddr" size="50" value="<?php echo $cbMailingConfig["debugToAddr"] ?>">
				<input type="text" name="cbMailingConfig_debugToDesc" size="50" value="<?php echo $cbMailingConfig["debugToDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGETITLE' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="text" name="cbMailingConfig_debugETitle" size="50" value ="<?php echo $cbMailingConfig["debugETitle"] ?>">
			</td>
		</tr>
		</table>

		<input type="hidden" value="0" name="boxchecked"/>
		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		<input type="hidden" name="task" value=""/>
		</form>
<?php
	}

	function messageForm( &$lists, &$config, $option ) {
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
					alert( "<?php echo JText::_( 'CB_MAILING_FILLINSUBJECT' ) ?>" );
					return false;
				} else if (getSelectedValue('adminForm','mm_group') < 0){
					alert( "<?php echo JText::_( 'CB_MAILING_SELECTAGROUP' ) ?>" );
					return false;
				} else if (form.mm_message.value == ""){
					alert( "<?php echo JText::_( 'CB_MAILING_FILLINMESSAGE' ) ?>" );
					return false;
				}
				return true;
				}
			}
		</script>

		<form action="index2.php" name="adminForm" method="post" enctype="multipart/form-data">
		<table class="adminheading">
		<tr>
			<th class="massemail">
				<?php echo JText::_( 'CB_MAILING_TITLE' ) ?>
			</th>
		</tr>
		</table>

		<table class="adminform">
		<tr>
			<th colspan="2">
				<?php echo JText::_( 'CB_MAILING_SELECTGROUPTEXT' ) ?>
			</th>
		</tr>
		<tr>
			<td width="150" valign="top">
				<?php echo JText::_( 'CB_MAILING_GROUPTEXT' ) ?>
			</td>
			<td width="85%">
				<?php echo $lists['gid']; ?>
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JText::_( 'CB_MAILING_HMTLMODETEXT' ) ?>
			</td>
			<td>
				<input type="checkbox" name="mm_mode" value="1" />
			</td>
		</tr>
		<tr>
			<td>
			<?php echo JText::_( 'CB_MAILING_SUBJECTTEXT' ) ?>
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
			'. JText::_( 'CB_MAILING_ATTACHFILETEXT' ) .'
			</td>
			<td>
				<input class="inputbox" type="file" name="mm_attach" size="50"/><br />
				'. JText::_( 'CB_MAILING_ATTACHFILESIZETEXT' ) . ini_get("upload_max_filesize") .'
			</td>
		</tr>
';
			}
?>
		<tr>
			<td valign="top">
			<?php echo JText::_( 'CB_MAILING_MESSAGETEXT' ) ?>
			</td>
			<td>
			<textarea cols="80" rows="24" name="mm_message" class="inputbox"><?php echo $config["signature"]; ?></textarea>
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
<?php
	}

	function showMembersForm( &$lists, $option ) {
		// global $database;	// J1.0
		$database = &JFactory::getDBO();	// J1.5

//echo "<pre>\n";
//var_dump( $lists );
//echo "</pre>\n";
		$permsHTML = "";
		$rowType = 0;

		$includeAll = $lists['includeAll'];
		$emailFieldList = $lists['emailFieldList'];

		if (count ( $lists['groupData'] ) > 0) {
			foreach ($lists['groupData'] as $groupData) {
				$fromCount = count( $groupData['fromMembers'] );
				$toCount = count( $groupData['toMembers'] )
?>
<table class="adminlist">
	<tr>
		<th><?php echo $groupData['permission'][0]->fromtitle ." (". $fromCount .")"; ?></th>
		<th>-&gt;</th>
		<th><?php echo $groupData['permission'][0]->totitle ." (". $toCount .")"; ?></th>
	</tr>
	<tr>
		<td>
			<table>
<?php
				if ( $fromCount > 0) {
					$rowType = 0;
					foreach ($groupData['fromMembers'] as $thisMember) {
?>
				<tr class="row<?php echo $rowType; ?>">
					<td><?php echo $thisMember->name . " (". $thisMember->username ."): ". $thisMember->email;
					$rowType = ($rowType == 0 ? 1 : 0);
					?></td>
				</tr>
<?php 
					}
				}
?>
			</table>
		</td>
		<td>&nbsp;</td>
		<td>
			<table>
<?php
				if ( $toCount > 0 ) {
					$rowType = 0;
					foreach ($groupData['toMembers'] as $thisMember) {
?>
				<tr class="row<?php echo $rowType; ?>">
					<td><?php echo $thisMember->name ." (".$thisMember->username ."): ". $thisMember->email;
						if ( $includeAll ) {
							foreach ($emailFieldList as $field) {
								if ( $thisMember->{$field->name} != null ) {
									if ( $thisMember->{$field->name} != "" ) {
										echo ", ". $thisMember->{$field->name};
									}
								}
							}
						}
						$rowType = ($rowType == 0 ? 1 : 0);
					?></td>
				</tr>
<?php 
					}
				}
?>
			</table>
		</td>
	</tr>
	<tr>
	</tr>
</table><br />
<?php
			}
		}
	}

	function errorMessage( $msg, $option ) {
		echo "$msg";
	}
}
?>