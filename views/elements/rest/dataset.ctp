<?php
//SnapLogic URIs are like 
//   http://XXXX:8188/feed/[PATH]/Output1?sn.content_type=text/html
//	emulate this convention

//in case just "html" or "text/html" are passed.. 
if (strstr($outputType,"html")) {
	$outputType = "html";
}
switch($outputType) {
	case "dump":
		//cake dump
		pr($dataObjects);
	break;
	
	case "html":
		//default to a plain table
		echo $this->element("rest/html_table");
	break;
	
	case "xml":
		echo $this->element("rest/xml");
	break;

	case "csv":
		echo $this->element("rest/csv");
	break;
	
	case "json":
		echo $this->element("rest/json");
	
	break;
	case  "jqgrid":
	echo $this->element("rest/jqgrid");
	break;
	
}	

?>
