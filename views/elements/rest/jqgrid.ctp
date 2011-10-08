<?php
$outObject["total"]=$total_pages;
$outObject["page"]=$page;
$outObject["records"]=$count;
foreach($dataObjects as $modelData) {
	$cellObject  = array();
	$modelName = $Interface["model"];
	$cellObject["id"]=$modelData[$modelName]["id"];
//	unset($modelData[$modelName]["id"]);
	$cellObject["cell"]=array_values($modelData[$modelName]);
	$cellObjects[] = $cellObject;
}
$outObject["rows"] =$cellObjects; 
echo json_encode($outObject);
?>