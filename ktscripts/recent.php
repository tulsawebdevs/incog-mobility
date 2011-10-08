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
$h = mysql_connect("localhost","dmsadmin","js9281djw");
mysql_select_db("dms");
$q = "select * from documents d left join document_metadata_version dmv on d.id=dmv.document_id order by 
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

//exit;
	?>
	<p><a href="/documents/download/<?php echo $row["document_id"]?>"><?php echo $row["name"]?></a><br/>
	<i>Modified <?php echo $row["modified"]?></i></p>
<?php
}
echo "<pre>";
//print_r($row);
echo "</pre>";

?>
