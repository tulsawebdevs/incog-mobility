<?php
foreach($lstParticipants as $Participant) {
	if (isset($Participant["phone"]))  {
		if (strlen($Participant["phone"])  != 10)  {
			//someone  goofed
		}else{
			$startPhone = $Participant["phone"];
			$finalPhone = "(".substr($startPhone,0,3).") ".substr($startPhone,3,3)."-".substr($startPhone,6,4);
		}
		$Participant["phone"] = $finalPhone;
	}
	if  (!isset($Participant["Participant"])) {
		$Participant = array("Participant"=>$Participant);
		//$Participant["Participant"] = $Participant;
	}
	echo $html->tableCells(array($Participant["Participant"]),null,false);
}
?>