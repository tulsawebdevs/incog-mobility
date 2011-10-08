<b>Showing <?php echo sizeof($dataObjects) ?> Results</b><br/> 
<table>
<tr>
<?php
foreach ($dataObjects[0][$tableModel] as $field=>$val) {
	?>
	<td><?php echo Inflector::humanize($field)?></td>
	<?php
}
?>
</tr>

<?php
foreach ($dataObjects as $index=>$models) {
	$obj = $models[$tableModel];
	?>
	<tr>
	<?php
		foreach ($obj as $field=>$val) {
	?>
			<td><?php echo  $val?></td>
		<?php
		}
		?>
	</tr>
<?php
}
?>
</table>