<?php
echo $thsForm->create("Participant",
array("id"=>"professional-signup-form"),$formMode);

echo $thsForm->input("business_name");
echo $thsForm->address("business_address","required");

echo $thsForm->phone("business_phone");
echo $thsForm->phone("alternate_phone");
echo $thsForm->phone("fax");

echo $thsForm->input("business_yearly_revenue");
echo $thsForm->input("business_website_address");

echo $thsForm->jobTitleTree();
echo $thsForm->textarea("primary_job_responsibilities",
	array(
		"label"=>"Primary Job Responsibilities"
	));
echo $thsForm->input("reports_to",array(
	"label"=>"Title of person you report to"));
?>
<h3>Employee Information</h3>
<?php
echo $thsForm->input("total_employees",array(
	"label"=>"How many total employees are at your business (all locations)?"));
echo $thsForm->input("location_employees",array(
	"label"=>" How many employees are at your location?  "));
echo $thsForm->radio("Participant.business_range",array(
	"Domestic"=>"Domestic","International"=>"International"), 
	array(
		"label"=>"Is your business international or domestic?",
		"value"=>"Domestic"
	));
echo $thsForm->yesno(
	"Participant.business_is_subsidiary",
	"Is your business a subsidiary/division?"
	);
echo $thsForm->yesno(
	"Participant.business_has_parent",
	"Is there a parent company?"
	);
?>