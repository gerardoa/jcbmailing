<?php
$permsHTML = "";
$rowType = 0;
$lists = $this->lists;
foreach ($lists['groupData'] as $groupData):
	$fromCount = $groupData['fromCount']; 
	$toCount = $groupData['toCount'];
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
</table>
<br />
<?php endforeach;?>