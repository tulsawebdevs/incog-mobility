<?php
$d  = isset($_GET["d"]) 
?$d
:0;
$modelName = isset($this->name)
?$this->name
:"unknown";
//e.g. lstParticipants
$var = "lst".$modelName;
if (isset($$var))  {

	if  (isset($_GET["q"])) {
		//diff format for autocomplete
		foreach($$var as $item)  {
			echo $item["Participant"]["phone"]."|".$item["Participant"]["id"]."\n";
		}
	}else{
		$dataObjects = $$var;
		echo $this->element("rest/json",array("dataObjects"=>$dataObjects));
	}
}
?>