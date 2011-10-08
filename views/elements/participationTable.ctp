<h3><span class="numParticipants"><?php echo sizeof($lstParticipants); ?></span> Existing Participants</h3>
<?php

if (!isset($lstParticipants)) {
?>
<p class="notice">No participants have been added to this study.</p>
<?php
} else {
?>
<table id="participation-results">
<thead>
<?php
$keys = array("id","last_name","first_name","phone") ;
$headerLabels = array();
foreach ($keys as $key)  {
	$headerLabels[] = Inflector::humanize($key);
}
echo  $html->tableHeaders($headerLabels);
?>
</thead>
<tbody>
<?php
foreach($lstParticipants as $i=>$Participant) {
	if  (!isset($Participant["Participant"])) {
		$Participant = array("Participant"=>$Participant);
	}
	$Participant["Participant"] = $thsForm->formatPhones($Participant["Participant"]);
	$trimmed  = array(
	$Participant["Participant"]["id"],
	$Participant["Participant"]["last_name"],
	$Participant["Participant"]["first_name"],
	$Participant["Participant"]["phone"]
	);
//	echo $html->tableCells(array($trimmed),null,null);
	echo $html->tableCells(array($trimmed),null,false);
}
}
?>
</tbody>
</table>
