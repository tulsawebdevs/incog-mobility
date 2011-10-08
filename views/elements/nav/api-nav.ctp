<?php
if (!isset($topDocumentFolder)) {
	$topDocumentFolder = "1697";
}
$user_secondary_tabs  = array();
$user_secondary_tabs[] = array(
	"url"=>"/documents/view/".$topDocumentFolder,
	"label"=>"View Documents"
);
if (!isset($user_type)) $user_type = "public";
$exit_link =  "http://www.topconpositioning.com";
switch($user_type) {
	case "primary":
	$user_secondary_tabs[] = array(
		"url"=>"/dealers/index/",
		"label"=>"Manage Locations"
	);
	$user_secondary_tabs[] = array(
		"url"=>"/users/employees",
		"label"=>"Manage Employees"
	);
	break;
	case "administrative" : 
	$user_secondary_tabs[] = array(
		"url"=>"/dealers/index/",
		"label"=>"Manage Dealers"
	);
	$user_secondary_tabs[] = array(
		"url"=>"/users/index",
		"label"=>"Manage Employees"
	);
	
	break;
	case "employee":
	break;
	
	case "regional":
	$user_secondary_tabs[] = array(
		"url"=>"/dealers/index/",
		"label"=>"Manage Dealers"
	);
	$url = "/users/indexByRm/".$logged_in_cid;
	$user_secondary_tabs[] = array(
		"url"=>$url,
		"label"=>"Manage Employees"
	);
	break;
	
	case "local":
		$user_secondary_tabs[] = array(
			"url"=>"/dealers/updateDealer",
			"label"=>"Edit My Dealer"
		);
	$user_secondary_tabs[] = array(
		"url"=>"/users/index",
		"label"=>"Manage Employees"
	);
	break;
	
	default: // public
	//wipe above defaults otu
		$user_secondary_tabs = array();
if ($logged_in_id) {	
	$user_secondary_tabs[] = array(
		"url"=>"/documents/view/".$topDocumentFolder,
		"label"=>"View Documents"
	);
}else{
		$user_secondary_tabs[] = array(
			"url"=>"/users/register/",
			"label"=>"Register"
		);
}
	break;
}
if ($user_type != "public") {
	$user_secondary_tabs[] = array(
		"url"=>"/users/updateProfile/".$logged_in_id,
		"label"=>"Edit My Profile"
	);

	$user_secondary_tabs[] = array(
		"url"=>"/users/logout",
		"label"=>"Log Out"
	);
	if(isset($allowConferenceRegistration) && $allowConferenceRegistration ) {
		$user_secondary_tabs[] = array(
	"url"=>"/registrations/conference2009",
	"label"=>"Register for Fall Sales Conference"
		);
	}
}
$availableNav["brochures"] = array(
	"url"=>"/users/brochures",
	"label"=>"Brochure Ordering"
);
$availableNav["survey"] = array(
	"url"=>"/users/survey",
	"label"=>"Customer Satisfaction Survey"
);
$availableNav["almanac"] = array(
	"url"=>"/secure_site/almanac.php",
	"label"=>"GPS Almanac System"
);
$availableNav["firmware"] = array(
	"url"=>"/secure_site/firmware.php?ss_id=".$logged_in_id,
	"label"=>"Firmware Upload System"
);
$availableNav["press"] = array(
	"url"=>"/users/press",
	"label"=>"Press Releases"
);
$availableNav["firmwareDownload"] = array(
	"url"=>"/secure_site/firmware.php?ss_id=".$logged_in_id,
	"label"=>"Firmware Downloads"
);

$availableNav["exit"] = array(
	"url"=>$exit_link,
	"label"=>"Exit Support Site"
);
$availableNav["eCustomer"] = array(
	"url"=>"http://65.51.121.25/ecustomer/login/login.asp",
	"label"=>"eCustomer"
);

$download_tabs[] = array(
	"url"=>"http://www.winzip.com/",
	"label"=>"Get WINZip",
	"ext"=>1
);
$download_tabs[] = array(
	"url"=>"http://www.adobe.com/",
	"label"=>"Get Adobe Reader",
	"ext"=>1
);

	$extra_nav = array();
	if ($sugarUser["brochure_access_c"]) {
		$extra_nav[] = "brochures";
	}
if ($logged_in_as) {
	$extra_nav[] = "almanac";
}
	$extra_nav[] = "press";
	$mALStr = is_array($myAccessLevels)  
	? implode(",",$myAccessLevels)
	:"";
	if (strstr($mALStr,"Firmware")) {	
		$extra_nav[] = "firmware";	
	}
	if (false) {
		$extra_nav[] = "survey";	
	}
if ($logged_in_as) {
	$extra_nav[] = "firmwareDownload";
}

if ($sugarUser["servicepart_access_c"]) {
		$extra_nav[] = "eCustomer";	
}
if (isset($sugarUser)) {
		$extra_nav[] = "exit";	
}
if (isset($extra_nav)) {
	foreach($extra_nav as $privName) {
		$application_tabs[] = $availableNav[$privName];
	}
}
//build controller/method values for highlighting class
foreach($user_secondary_tabs as $i=>$arr)  {
	if (strstr($arr["url"],"http")) {
		$relative = str_replace(FULL_BASE_URL,"",$arr["url"]);
	}else{
		$relative = $arr["url"];
	}
	//always begins with slash, so
	$slashfree  = substr($relative,1,strlen($relative));
	$all = explode("/",$slashfree);
	$controller = $all[0];
	$method = isset($all[1])? $all[1] : "index";
	$user_secondary_tabs[$i]["controller"] = $controller;
	$user_secondary_tabs[$i]["method"] = $method;
}
//if secondary nav must be explicitly set
//$style = isset($sec) ? "":'style="display:none"';
$style = "";

?>
<h3>Support Site</h3>
<ul class="secondary-nav" <?php echo $style?>>
	<li>
		<ul>
			<?php
//			pr($highlightPath);
			foreach ($user_secondary_tabs as $arr) {
//				pr("cmp ".$arr["controller"]."/".$arr["method"]." to $client_controller / $client_action");
				if (
				(isset($highlightPath) && $arr["controller"]."/".$arr["method"] == $highlightPath) 
			|| $arr["controller"] == "dealers" && $client_controller == "dealers"
			|| ($client_action == "employees" && $arr["controller"] == "users" && $arr["method"] == "index")
			|| (!isset($highlightPath) 
					&& $arr["controller"] == $client_controller 
					&& $arr["method"] == $client_action))
				{
					$aclass="class='active'";
					$aclass="active";
				}else{
					$aclass="";
				}
				?>
				<li>
				<?php
				if (!isset($api) && $_GET["api"] == 1) {
					$api=1;
				}
				echo $html->link($arr["label"],$arr["url"],array("class"=>$aclass,"api"=>$api));
				?>
				</li>
				<?php
			}
			?>
		</ul>
	</li>
</ul>

<?php
if (isset($application_tabs) && sizeof($application_tabs)) {
	?>
<h3>Applications</h3>
<ul class="secondary-nav">
	<li>
		<ul>
<?php
			foreach ($application_tabs as $arr) {
				if (!isset($api) && $_GET["api"] == 1) {
					$api=1;
				}
				if (isset($arr["ext"]) && $arr["ext"]) {
					//a true external link .. should not use API loading
					$api=false;
				}
				?>
				<li>
				<?php
				echo $html->link($arr["label"],$arr["url"],array("class"=>$aclass,"api"=>$api));
				?>
				</li>
				<?php
			}
?>
		</ul>
	</li>
</ul>
<?php
}
?>
<h3>Utilities</h3>
<ul class="secondary-nav">
	<li>
		<ul>
<?php
foreach ($download_tabs as $arr) {
?>	
	<li><a href="<?php  echo $arr["url"]?>"><?php echo $arr["label"]?></a></li>
<?php
}
?>
		</ul>
	</li>
</ul>