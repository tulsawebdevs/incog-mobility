<?php
foreach($lstItems as $topLevelFolderId=>$topLevelFolder) {
				//var_dump($topLevelFolder);
				$name = isset($topLevelFolder["name"])
				?$topLevelFolder["name"]
				:$topLevelFolder["title"];
				$href  = "/documents/view/$topLevelFolderId";
				$arel = '';
			pr($topLevelFolder);

	if (isset($topLevelFolder["subfolders"]) && sizeof($topLevelFolder["subfolders"])) {
	?>
	<a class="hasChildren" href="<?php echo $href?>"><h2><span><?php echo $name?></span></h2></a>
	<ul>
		<?php foreach($topLevelFolder["subfolders"] as $subfolder) {
			pr($subfolder);
			$sname = isset($subfolder["name"])
				?$subfolder["name"]
				:$subfolder["title"];
				$shref  = "/documents/view/".$subfolder["id"];
				?>
				<li><a href="<?php echo $shref?>"><?php echo $sname?></a></li>
				<?php
			}
		?>
		</ul>
	<?php
	}else{
	?>
	<a href="<?php echo $href?>"><h2><?php echo $name?></h2></a>

<?php
	}
}
?>