<?php
$kidsFollowups["Yes"][]= array("Yes","input","why","Why?");
$kidsFollowups["Yes"][]= array("Yes","input","how","and how?");

echo $thsForm->yesno(
	"Participant.have_kids",
	"Do you have kids?",
	array("followup"=>$kidsFollowups)
	);

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

echo $thsForm->jobTitleTree();
echo $thsForm->input("business_name",$demographicRequired);
echo $thsForm->input("total_employees",array(
	"label"=>"Number of Employees"));
echo $thsForm->address("business_address");
echo $thsForm->phone("work_phone","extension");

echo $thsForm->maritalStatus();

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
	
/*
 * example radio from housing type:
 * echo $thsForm->radio("Participant.housing_type",array(
	"Apartment"=>"Apartment","Condo"=>"Condo","House"=>"House"), 
	array(
		"label"=>"Housing Type",
	));

 */

$classifyOpts = array(
	"Casual"=>"Casual","Intermediate"=>"Intermediate","Serious"=>"Serious"
);
$classifyFollowup = array(
	"Yes","radio","video_classify","How would you classify yourself?",
	$classifyOpts
	);
$videoGamesFollowups["Yes"][]=$classifyFollowup ; 

$systemOpts = array(
	"Sony"=>"Sony","Nintendo"=>"Nintendo","Microsoft"=>"Microsoft"
);
$systemFollowup = array(
	"Yes","checkbox","video_system","What game systems do you use?",
	$systemOpts
);
//$videoGamesFollowups["Yes"][]= $systemFollowup;

$videoGamesFollowups["Yes"][]=array("Yes","gameSystems");

echo $thsForm->yesno(
	"Participant.play_video_games",
	"Do you play video games?",
	array("followup"=>$videoGamesFollowups)
);

echo $thsForm->healthConditions("Do you have any of the following health conditions?");
?>