<?php
foreach ($dataObjects[0][$tableModel] as $field=>$val) {
	echo "\"".Inflector::humanize($field)."\",";
}
echo  "\n";

foreach ($dataObjects as $index=>$models) {
	$obj = $models[$tableModel];
	$out .= "\"".implode("\",\"",$obj)."\"\n"; 
}
?>