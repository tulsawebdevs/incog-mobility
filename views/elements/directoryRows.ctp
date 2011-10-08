<?php
foreach ($lstItems as $index=>$item) {
	if (!is_object($item) && !is_array($item)) continue;
	if (!isset($item["item_type"])) continue;
	if ($item["item_type"] == "D") {
	?>
	<tr>
		<td><a href="#">oneCare custom credit application.doc</a></td>
		<td><?php echo strftime("%D",strtotime($item["modified_date"]))?></td>
		<td><?php echo strtoupper($item["mime_icon_path"])?></td>
	</tr>	

			</td>
			<td style="vertical-align:middle;"></td>
		</tr>
		<?php
	} else { //if a folder Item Type..
		// TODO: Verify that this will work ok with jQuery tablesorter
		// TODO: Update colspan as <th> elements are added to <thead>
		$cms_user_type = false;
		if (!isset($item->id) && !isset($item["id"])) {
		pr("folder problem?");
		pr($item["title"]);
			continue;
		}
		echo '<tr class="folder"><td>';
		echo '</td><td>' . $dms->folderLink($item,null,$cms_user_type) . '</td>';
		?>
		<td>&nbsp;</td><td>&nbsp;</td><?php
		echo '</tr>';
		//	exit;
	}
}
?>