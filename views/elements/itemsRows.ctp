<?php

foreach ($lstItems as $index=>$item) {
	if (!is_object($item) && !is_array($item)) continue;
	//if (!isset($item["item_type"])) continue;
	if(!isset($item["item_type"]) && $item["document_id"]){
			$item["id"] = $item["document_id"];
		$item["item_type"]="D";
	}
	if ($item["item_type"] == "D") {
		pr($item);
		if(!isset($item["title"]) && isset($item["name"]) ) {
			$item["title"] = $item["name"];
		}
		if(!isset($item["modified_date"]) && isset($item["modified"]) ) {
			$item["modified_date"] = $item["modified"];
		}
	?>
	<tr>
		<td><a href="/documents/download/<?php echo $item["id"]?>">
		<?php echo $item["title"]?></a></td>
		<td><?php echo strftime("%D",strtotime($item["modified_date"]))?></td>
		<td>
		<?php
		//echo strtoupper($item["mime_icon_path"])
		?></td>
	</tr>	

		<?php
	} else { //if a folder Item Type..
		$cms_user_type = false;
		if (!isset($item->id) && !isset($item["id"])) {
		pr("folder problem?");
		pr($item["title"]);
			continue;
		}
		echo '<tr class="folder">';
		echo '<td>' . $dms->folderLink($item,null,false) . '</td>';
		?>
		<td>&nbsp;</td><td>FOLDER</td><?php
		echo '</tr>';
		//	exit;
	}
}
?>