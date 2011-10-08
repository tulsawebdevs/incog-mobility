<h2 id="breadcrumbTitle">
<?php
$i=1;
foreach($abreadcrumb as $arr) {
	$folder_id = $arr["id"];
	$folder_name = $arr["name"];
	?>
	<a href="#/documents/table/<?php echo $folder_id?>" rel="history" >	
<?php echo $folder_name?></a>
<?php
	if ($i<sizeof($abreadcrumb)) {
		echo " &raquo "; 
	}
	$i++;
}
?>
</h2>