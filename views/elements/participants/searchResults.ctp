<?php
if  (!isset($viewFields)) {
	$viewFields = array("id","phone","first_name","last_name","application_type","address_city","allow_calls","edit");
}
if ($searchType !="generate"  && $searchType != "report"){
	$viewFields[] = "Edit";
}

if ($searchType == "generate") {
	?>
	<h2 class="with-button"><?php echo sizeof($lstParticipants)?> Results 
	<?php
	//TODO: set in  controller ..
	$searchParamsData  = "?";
	foreach($searched as $field=>$val) {
		$searchParamsData.="$field=$val&";
	}
	$searchParamsData .="searchType=$searchType";
	echo $thsForm->button("Gather Email Addresses",array("id"=>"gather-email"),"email"); 
	echo $thsForm->button("Print",array("data-href"=>"/participants/index/outputType:printableHtml/$searchParamsData"),"printer");
	echo $thsForm->button("Export",array("data-href"=>"/participants/index/outputType:csv/$searchParamsData"),"page_excel");
	?></h2>
<?php 
} else { 
?>
	<h2><?php echo sizeof($lstParticipants); ?> Results</h2>
<?php 
} 
?>

<table id="<?php echo $tableId; ?>">
<thead>
<?php
echo  $html->tableHeaders($viewFields);
?>
</thead>
<tbody>
<?php
foreach($lstParticipants as $i=>$Participant) {
	$Participant["Participant"] = $thsForm->formatPhones($Participant["Participant"]);
	$cellArr  = array($Participant["Participant"]);
	$toggleFields=false;
	$actions=null;
	
	switch($searchType)  {
		case "search":
		//need to map this properly
//		if (!$Participant["Participant"]["address_city"] 
//			&& $Participant["Participant"]["business_address_city"]) {
//			$Participant["Participant"]["address_city"]  = $Participant["Participant"]["business_address_city"];
//		}
//		echo $html->tableCells(array($Participant["Participant"]),$toggleFields);
			$toggleFields=array("allow_calls");
		break;
		case "generate":
		$actions=false;
		break;
		case "report":
//			$dArr = array_merge($lstParticipants[$i]["Participant"],$lstParticipants[$i]["Study"]);
			$cellArr =array_merge($Participant["Participant"],$Participant["Study"]);
			$actions=false;
			//echo $html->tableCells($dArr,null,null);
		break;
	}
	echo  $html->tableCells($cellArr,$toggleFields,$actions);
}
?>
</tbody>
</table>