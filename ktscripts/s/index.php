<?php
/*
 dbHost           = localhost
 dbName           = knowledgetree
 ;dbName		= kt1
 dbUser           = knowledgetree
 dbPass           = RgTTYDHD1q
 dbPort            = default
 dbAdminUser       = ktadmin
 dbAdminPass       = gjmY45NTk23e!d
 */

//$h = mysql_connect("localhost","knowledgetree","RgTTYDHD1q");
//mysql_select_db("knowledgetree");
$searchTerm = mysql_escape_string($_GET["keyword"]);
if(!$searchTerm) {
echo json_encode("");
exit;
}
$h = mysql_connect("localhost","dmsadmin","js9281djw");
mysql_select_db("dms");
$q = "select * from documents d 
	left join document_metadata_version dmv on d.id=dmv.document_id
	where dmv.name like '%".$searchTerm."%'  
	order by 
	d.modified desc limit 5 ";
$res = mysql_query($q);

//var_dump($res);
if(mysql_error()) {
	echo "<pre>";
	print_r(mysql_error());
	print_r($q);
	echo "</pre>";
}
?>
<?php

while ($row = mysql_fetch_assoc($res)) {
	
	if($_GET["d"]) {
		echo "<pre>";
		print_r($row);
		echo "</pre>";
	}
	
	$out[] = $row;
}
echo json_encode($out);
?>
