<?php
jimport('joomla.application.component.view');

class CbmailingViewMembers extends JView
{
	function display( $tpl = null )
	{
		JToolBarHelper::title( JText::_('Members') );
				
		parent::display( $tpl );		
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
	
}