<?php
/*
TRACY
$kidsFollowups["1"][]= array("1","input","child_age","Child's Age");
*	array("followup"=>$kidsFollowups)
**/

//from "edit_related.ctp"
//	echo  $thsForm->repeatable("input","age",array("associationModel"=>"Child"));

//$kidsFollowups["1"][]= array("1","repeatable","Child.0.age","Child's Age");
//$kidsFollowups["1"][]= array("1","Child.0.age","repeatable","Child's Age");
$kidsFollowups["1"][]= array("1","repeatable","age",array(
	"associationModel"=>"Child",
	"label"=>"Child's Age"
	));
echo $thsForm->yesno(
	"Participant.have_kids",
	"Do you have kids?",
	array("followup"=>$kidsFollowups)
);
/*  no followup -- 
 * echo $thsForm->yesno(
	"Participant.have_kids",
	"Do you have kids?"
	);
*/




//TRACY
$videoGamesFollowups["1"][]=array("1","classify");
$videoGamesFollowups["1"][]=array("1","gameSystems");
/*	array("followup"=>$videoGamesFollowups)

**/

echo $thsForm->yesno(
	"Participant.play_video_games",
	"Do you play video games?",
	array("followup"=>$videoGamesFollowups)
);

echo $thsForm->healthConditions();
?>