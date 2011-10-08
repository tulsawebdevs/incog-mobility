<?php
function nest($obj) {
	foreach  ($obj as $field=>$val) {
		if (is_numeric($field)) {
			$out .= "<item>";
		}
		
		if (is_array($val)) {
			$out .= nest($val) ;
		}else{
			$tag = is_numeric($field)? "field$field" : $field; 
			$out .= "<$field>$val</$field>";	
		}
		if (is_numeric($field)) {
			$out .= "</item>";
		}
	}
	return $out;
}

header('Content-type: text/xml');

foreach ($dataObjects as $label=>$obj) {
	echo  is_numeric($label) ? "<dataObjects>" : "<$label>";
	
	$i++;
	echo nest($obj);
}

echo  is_numeric($label) ? "</dataObjects>" : "</$label>";
?>

