<?php

echo $thsForm->radio("Participant.housing_type",array(
	"Apartment"=>"Apartment","Condo"=>"Condo","House"=>"House"), 
	array(
		"label"=>"Housing Type",
	));
echo $thsForm->dob($demographicRequired);
echo $thsForm->education();
echo $thsForm->race();
echo $thsForm->gender();

echo $thsForm->income($demographicRequired);
echo $thsForm->employmentStatus();
// TODO: proofread the job title tree
echo $thsForm->jobTitleTree();
// FIXME: I don't think Business Name is required for personal apps?
echo $thsForm->input("business_name",$demographicRequired);
echo $thsForm->input("total_employees",array(
	"label"=>"Number of Employees"));
echo $thsForm->address("business_address");
echo $thsForm->phone("work_phone","extension");

echo $thsForm->maritalStatus();

$kidsFollowups["1"][]= array("1","repeatable",array("gender","age"),array(
	"associationModel"=>"Child",
	"label"=>"Child's Age"
	));
echo $thsForm->yesno(
	"Participant.have_kids",
	"Do you have kids?",
	array("followup"=>$kidsFollowups)
);
echo $thsForm->yesno(
	"Participant.own_a_computer",
	"Do you own a computer at home?"
	);
echo $thsForm->yesno(
	"Participant.use_a_computer_at_work",
	"Do you use a computer at work?"
	);
echo $thsForm->yesno(
	"Participant.own_a_business",
	"Do you own a business?"
	);
echo $thsForm->yesno(
	"Participant.self_employed",
	"Are you self-employed?"
	);
echo $thsForm->yesno(
	"Participant.travel",
	"Do you travel for business?"
	);
echo $thsForm->yesno(
	"Participant.use_internet",
	"Do you use the Internet from home?"
	);
echo $thsForm->yesno(
	"Participant.smoker",
	"Are you a smoker?"
	);
echo $thsForm->yesno(
	"Participant.voter",
	"Are you a voter?"
	);
echo $thsForm->yesno(
	"Participant.drink_alcohol",
	"Do you drink alcohol?"
	);
echo $thsForm->yesno(
	"Participant.home_owner",
	"Are you a home owner?"
	);

if($formMode=="submitting") {
	$classifyFollowup = array(
		"1","radio","video_classify","How would you classify yourself?",
		array("Casual"=>"Casual","Intermediate"=>"Intermediate","Serious"=>"Serious")
		);
	$videoGamesFollowups["1"][]=$classifyFollowup;
	$videoGamesFollowups["1"][]=array("1","gameSystems");
	echo $thsForm->yesno(
		"Participant.play_video_games",
		"Do you play video games?",
		array("followup"=>$videoGamesFollowups)
		);
}else{
	$videoGamesFollowups=false;
	echo $thsForm->yesno(
		"Participant.play_video_games",
		"Do you play video games?"
		);
	echo  $thsForm->gameSystems();
}
	
echo $thsForm->healthConditions();

/*graveyard
 * /*
 * example radio from housing type:
 * echo $thsForm->radio("Participant.housing_type",array(
	"Apartment"=>"Apartment","Condo"=>"Condo","House"=>"House"), 
	array(
		"label"=>"Housing Type",
	));


$classifyFollowup = array(
	"1","radio","video_classify","How would you classify yourself?",
	array("Casual"=>"Casual","Intermediate"=>"Intermediate","Serious"=>"Serious")
	);

$videoGamesFollowups["1"][]=$classifyFollowup;
//$videoGamesFollowups["1"][] =  array("videogameClassify");

$videoGamesFollowups["1"][]=array("gameSystems");


echo $thsForm->yesno(
	"Participant.play_video_games",
	"Do you play video games?",
	array("followup"=>$videoGamesFollowups)
	);
*/


?>
