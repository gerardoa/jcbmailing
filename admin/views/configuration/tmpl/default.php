<?php 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
global $option;
$cbMailingConfig = $this->cbMailingConfig;
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
				<input type="checkbox" name="params[cbMailingConfig_feAllowAtt]" <?php echo ($cbMailingConfig["feAllowAtt"] ? "checked" : "") ?> />
			</td>
		</tr>
		<tr>
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGFEALLOWHTML' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="params[cbMailingConfig_feAllowHTML]" <?php echo ( $cbMailingConfig["feAllowHTML"] ? "checked" : "")  ?> />
			</td>
		</tr>
		<tr>
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGFEALLOWSIGOVER' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="params[cbMailingConfig_feAllowSigOver]" <?php echo ( $cbMailingConfig["feAllowSigOver"] ? "checked" : "") ?> />
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
				<input type="radio" name="params[cbMailingConfig_mmMethod]" value="1"<?php
				if ($cbMailingConfig["mmMethod"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMMETHOD1' ) ?><br />
				<input type="radio" name="params[cbMailingConfig_mmMethod]" value="2"<?php
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
				<input type="radio" name="params[cbMailingConfig_mmFrom]" value="1"<?php
				if ($cbMailingConfig["mmFrom"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMFROM1' ) ?><br />
				<input type="radio" name="params[cbMailingConfig_mmFrom]" value="2"<?php
				if ($cbMailingConfig["mmFrom"] == "2") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMFROM2' ) ?><br />
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMFROMLABEL' ) ?><br />
				<input type="text" name="params[cbMailingConfig_mmFromAddr]" size="50" value="<?php echo $cbMailingConfig["mmFromAddr"] ?>">
				<input type="text" name="params[cbMailingConfig_mmFromDesc]" size="50" value="<?php echo $cbMailingConfig["mmFromDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTO' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="radio" name="params[cbMailingConfig_mmReplyTo]" value="1"<?php
				if ($cbMailingConfig["mmReplyTo"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTO1' ) ?><br />
				<input type="radio" name="params[cbMailingConfig_mmReplyTo]" value="2"<?php
				if ($cbMailingConfig["mmReplyTo"] == "2") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTO2' ) ?><br />
				<input type="radio" name="params[cbMailingConfig_mmReplyTo]" value="3"<?php
				if ($cbMailingConfig["mmReplyTo"] == "3") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTO3' ) ?><br />
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMREPLYTOLABEL' ) ?><br />
				<input type="text" name="params[cbMailingConfig_mmReplyToAddr]" size="50" value="<?php echo $cbMailingConfig["mmReplyToAddr"] ?>">
				<input type="text" name="params[cbMailingConfig_mmReplyToDesc]" size="50" value="<?php echo $cbMailingConfig["mmReplyToDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMTO' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="radio" name="params[cbMailingConfig_mmTo]" value="1"<?php
				if ($cbMailingConfig["mmTo"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMTO1' ) ?><br />
				<input type="radio" name="params[cbMailingConfig_mmTo]" value="2"<?php
				if ($cbMailingConfig["mmTo"] == "2") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMTO2' ) ?><br />
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMTOLABEL' ) ?><br />
				<input type="text" name="params[cbMailingConfig_mmToAddr]" size="50" value="<?php echo $cbMailingConfig["mmToAddr"] ?>">
				<input type="text" name="params[cbMailingConfig_mmToDesc]" size="50" value="<?php echo $cbMailingConfig["mmToDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCC' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="radio" name="params[cbMailingConfig_mmBCC]" value="1"<?php
				if ($cbMailingConfig["mmBCC"] == "1") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCC1' ) ?><br />
				<input type="radio" name="params[cbMailingConfig_mmBCC]" value="2"<?php
				if ($cbMailingConfig["mmBCC"] == "2") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCC2' ) ?><br />
				<input type="radio" name="params[cbMailingConfig_mmBCC]" value="3"<?php
				if ($cbMailingConfig["mmBCC"] == "3") {
					echo " checked";
				}
				?>><?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCC3' ) ?><br />
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGMMBCCLABEL' ) ?><br />
				<input type="text" name="params[cbMailingConfig_mmBCCAddr]" size="50" value="<?php echo $cbMailingConfig["mmBCCAddr"] ?>">
				<input type="text" name="params[cbMailingConfig_mmBCCDesc]" size="50" value="<?php echo $cbMailingConfig["mmBCCDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGALLADDR' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="params[cbMailingConfig_allAddr]" <?php echo ( $cbMailingConfig["allAddr"] ? "checked" : "")  ?> />
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGSIGNATURE' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<textarea cols="80" rows="10" name="params[cbMailingConfig_signature]" class="inputbox"><?php echo $cbMailingConfig["signature"] ?></textarea>
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGINCBLOCK' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="checkbox" name="params[cbMailingConfig_incBlocked]" <?php echo ( $cbMailingConfig["incBlocked"] ? "checked" : "") ?> />
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
				<input type="checkbox" name="params[cbMailingConfig_debug]" <?php echo ( $cbMailingConfig["debug"] ? "checked" : "") ?> />
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGFROM' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGFROMLABEL' ) ?><br />
				<input type="text" name="params[cbMailingConfig_debugFromAddr]" size="50" value="<?php echo $cbMailingConfig["debugFromAddr"] ?>">
				<input type="text" name="params[cbMailingConfig_debugFromDesc]" size="50" value="<?php echo $cbMailingConfig["debugFromDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGTO' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGTOLABEL' ) ?><br />
				<input type="text" name="params[cbMailingConfig_debugToAddr]" size="50" value="<?php echo $cbMailingConfig["debugToAddr"] ?>">
				<input type="text" name="params[cbMailingConfig_debugToDesc]" size="50" value="<?php echo $cbMailingConfig["debugToDesc"] ?>">
			</td>
		</tr>
		<?php $rowID = 1 - $rowID; ?>
		<tr class="<?php echo "row$rowID"; ?>">
			<td <?php echo $colSizes[0] ?>>
				<?php echo JText::_( 'CB_MAILING_ADMIN_CONFIGDEBUGETITLE' ) ?>
			</td>
			<td <?php echo $colSizes[1] ?>>
				<input type="text" name="params[cbMailingConfig_debugETitle]" size="50" value ="<?php echo $cbMailingConfig["debugETitle"] ?>">
			</td>
		</tr>
		</table>

		<input type="hidden" value="0" name="boxchecked"/>
		<input type="hidden" name="option" value="<?php echo $option; ?>"/>
		
<!--		<input type="hidden" name="id" value="echo $this->component->id;" />-->
		<input type="hidden" name="component" value="<?php echo 'com_cbmailing';?>" />
		<input type="hidden" name="controller" value="component" />
<!--		<input type="hidden" name="option" value="com_config" />-->
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>