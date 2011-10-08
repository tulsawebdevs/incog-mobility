<?php
//
class AppController extends Controller {
	var $helpers = array ('Html','Javascript','Form','ThsForm','Interface');
	var $uses =  array("User");
	var $components = array ("Mail","Session","Authobject");
	var $cacheAction = false;
	var $debugMsgs = array ();
	var $flashMsgs = array ();
	var $appErrors = array(
		"unknown"=>"An unknown application error has occurred.",
		"perms"=>"You do not have permission to access the requested page",
		"form"=>"An error occurred submitting the form.. "
		);
	var $viewContent;
	var $logged_in=false;
	var $logged_in_as=null;
	var $logged_in_name=null;
	var $publicActions = array(
		"Users::login","Users::apitest"
	);

	function setNav() {
		$lstTabs = array ();
		$lstTabs[] = array (
			"label" => "Participant Mgmt",
			"target" => "",
			"tooltip" => "Participant Management"
		);
	}
	function setFlashHTML() {
		$prepared = array();
		if (!sizeof($this->Session->read("flashMsgs") )) { return false;}
		foreach($this->Session->read("flashMsgs") as $raw) {
			$prepared[] = "<span class='msg'>".$raw."</span>";
		}
		$this->set("msg",$prepared);
		$this->Session->setFlash(implode("<br/>",$prepared));
	}



	function beforeFilter()  {
		return  parent::beforeFilter();
	}
	function checkObservedFields() {
	}
	function handleObservedField($field,$handleData="") {
		//so that we don't get methods with names like 'handleValidated_C_Change()'
		$fieldMethodMap = array(
			"validated_c"=>"handleValidationChange"
			);
		if (isset($fieldMethodMap[$field])) {
			$methodName = $fieldMethodMap[$field];
			if (method_exists($this->ObservedField,$methodName)) {
				$resp = call_user_func(array($this->ObservedField,$methodName),$this->data["Contacts"]);
			}else{
				pr("requested changeHandler $methodName not found in OF component, using default handle()");
				//try the default
				$resp = $this->ObservedField->handle($field);
				if (!is_numeric($resp)) {
					pr("non-numeric resp from handle() :");
					pr($resp);
				}
			}
			return $resp;
		}
		//other fields don't have methods for them yet.. just little blocks
		//phase these out eventually and make proper methods
		switch($field) {
			case "fieldNotHandled":
			break;
			default:
			$this->redirect("/pages/msg/?msg_code=form");
			break;
		}
		
	}



	function beforeRender() {
		$this->set("Interface",array(
			"model"=>Inflector::singularize($this->name)
			)
		);
		App::import('Helper', 'Interface');
		$Interface  = new InterfaceHelper;
		$Interface->model = Inflector::singularize($this->name);
		$Interface->additionalBodyClasses  = array($this->action);

		$this->set("controllerName",$this->name);
		$this->set("actionName",$this->action);
		
		if  (!isset($this->viewVars["tableId"])) {
			$hyphenated  =  strtolower($this->name)."-".strtolower($this->action);
			$this->set("tableId",$hyphenated);
		}
		if  (!isset($this->viewVars["siteTitle"])) {
			$this->set("siteTitle",$_SERVER["SERVER_NAME"]);
		}
		if  (!isset($this->viewVars["pageTitle"])) {
			$this->set("pageTitle","Dashboard");
		}
/*		if(!isset($this->Kt) || !$this->Kt->session) {
			if($this->name != "Requests") {
				trigger_error($this->name. " needs some acl as it's not 'requests'");
	//			pr("pages cont? call autho");
				$ic =$this->Authobject->isComplete($this); 
	//			pr($ic);
			}else{
				$this->logged_in = "request test user";
			}
		}*/
		if($this->logged_in) {
			$this->set("logged_in",1);
			$this->set("logged_in_as",$this->logged_in_as);
		}
		if($this->logged_in_name) {
//			trigger_error("awesom, ".$this->logged_in_name." logged in ");
		}else if ($this->Session->read("ktName")) {
			trigger_error(" ".$this->logged_in_name." retrieved from sess var ktName ");
		}
		$this->set("logged_in_name",$this->logged_in_name);
		
		$this->set("Interface",$Interface);
		if ($logged_in_as =$this->Session->read("logged_in_as")) {
//			$this->set("logged_in_as",$logged_in_as);
		}
		return  parent::beforeRender();
	}

	function unique(){
		$this->layout=null;
		if  (isset($this->data)) {
//			pr("data: ");
//			pr($this->data);
//			pr($this->params);
			$field = $this->params["named"]["match"];
			$model = Inflector::Singularize($this->name);
			$checkVal = $this->data["$model"]["$field"];
			$exists = $this->$model->find(
			"count",
			array(
				"conditions"=>array(
				"$field='$checkVal'"
				),
				"recursive"=>-1
				)
			);
			if($exists) {
				echo "false";
			}else{
				echo "true";
			}
			exit;
		}
	}

	//modelMap must die
	function modelMap($uses) {
		//this sucks, skip it
		return 1;
		$modelMap  = array();
		$User =  array("email","first_name","last_name");
		$Participant = array();
		$Application = array();
		/* controller's 'uses' var is processed so that
		 * when you call thsForm->email it know that 'email' field
		 * belongs  by default to User model, and can
		 * give you data[User][email]  for the input name
		 */
		 foreach($uses as $Model) {
		 	foreach($$Model as $field) {
		 		if  (!isset($modelMap[$field])) {
		 		 $modelMap[$field] = $Model;
		 		}
		 	}
		 }
//		 pr("setting ModelMap");
//		 pr($modelMap);
//		 exit;
		 $this->set("modelMap",$modelMap);
	}
	function errOut($msg = "") {
		if (!$msg) {
			$msg = "An unknown error occurred" . print_r($this, 1);
		}
		$this->set("msg", $msg);
	}
/*
 * Gets a couple things ready  for REST/SnapLogic representations
 */
	function prepTop($stem) {
		$csvurl = $stem ."content_type:csv";
		$printableurl = $stem ."?printable=1";
		$top = "<p><a target='_blank' href='".FULL_BASE_URL."$printableurl'>Printable version</a> of this.<br/>";
		$top .= "To download as a CSV, click <a target='_blank' href='".FULL_BASE_URL."$csvurl'>here</a></p>"; 
		if (isset($_GET["printable"])) {
			$this->layout=null;
			$top = "";
		}
		return $top;
	}
	function prepRest() {
		if (isset($this->params["named"]["outputType"])) {
			$content_type = $this->params["named"]["outputType"];
		}else{
			$content_type = "html";
		}
		$this->params["outputType"] = $content_type;
		$this->set("outputType",$content_type);
//		$this->set("content_type",$content_type);
		if ($content_type != "html") {
			$this->layout = null;
			if ($content_type == "csv") {
				//blank out any header that was set
			    $this->set("headline","");
				if (sizeof($this->viewVars["dataObjects"]) == 1) {
					foreach($this->viewVars["dataObjects"] as $key=>$arr) {
						$this->set("dataObjects",$arr);
						break;
					}
				}
				header('Content-type: application/csv');
			    header('Content-Disposition: attachment');
			}
		}
	}
	function doLogin() {
			return true;
	}

	function msg()  {
		$this->setState();
		$msg = isset($this->params["url"]["msg_code"]) 
			? $this->params["url"]["msg_code"]
			: "unknown";
		
		$this->set("msg",$this->appErrors[$msg]);
	}
	function prstate()  {
		foreach($this->state as $index=>$item) {
			if (!is_object($item)) {
				pr($index.":");
				pr($item);
			}
		}
	}
	function getStateList() {
				$state_list = array('AL'=>"Alabama",
                'AK'=>"Alaska", 
                'AZ'=>"Arizona", 
                'AR'=>"Arkansas", 
                'CA'=>"California", 
                'CO'=>"Colorado", 
                'CT'=>"Connecticut", 
                'DE'=>"Delaware", 
                'DC'=>"District Of Columbia", 
                'FL'=>"Florida", 
                'GA'=>"Georgia", 
                'HI'=>"Hawaii", 
                'ID'=>"Idaho", 
                'IL'=>"Illinois", 
                'IN'=>"Indiana", 
                'IA'=>"Iowa", 
                'KS'=>"Kansas", 
                'KY'=>"Kentucky", 
                'LA'=>"Louisiana", 
                'ME'=>"Maine", 
                'MD'=>"Maryland", 
                'MA'=>"Massachusetts", 
                'MI'=>"Michigan", 
                'MN'=>"Minnesota", 
                'MS'=>"Mississippi", 
                'MO'=>"Missouri", 
                'MT'=>"Montana",
                'NE'=>"Nebraska",
                'NV'=>"Nevada",
                'NH'=>"New Hampshire",
                'NJ'=>"New Jersey",
                'NM'=>"New Mexico",
                'NY'=>"New York",
                'NC'=>"North Carolina",
                'ND'=>"North Dakota",
                'OH'=>"Ohio", 
                'OK'=>"Oklahoma", 
                'OR'=>"Oregon", 
                'PA'=>"Pennsylvania", 
                'RI'=>"Rhode Island", 
                'SC'=>"South Carolina", 
                'SD'=>"South Dakota",
                'TN'=>"Tennessee", 
                'TX'=>"Texas", 
                'UT'=>"Utah", 
                'VT'=>"Vermont", 
                'VA'=>"Virginia", 
                'WA'=>"Washington", 
                'WV'=>"West Virginia", 
                'WI'=>"Wisconsin", 
                'WY'=>"Wyoming");
          return $state_list;
	}
	function render($action = null, $layout = null, $file = null) {
		$content = parent::render($action, $layout, $file);
    	$this->viewContent = $content;
		return $content;
	}
	function getConditionsFromUrl($fieldMap) {
		$searchParams=$_GET;
		unset($searchParams["url"]);
		$this->data["searchType"] = $searchParams["searchType"];
		$this->set("searchType",$searchParams["searchType"]);
		
		unset($searchParams["searchType"]);
		$this->data["Participant"]=$searchParams;
		list($fields,$order) =   $this->buildSearchFields();
		
		$conditions = $this->buildSearchConditions($fieldMap);
		unset($searchParams["searchType"]);
		return array($fields,$order,$conditions);
	}
	function buildSearchFields($fieldMap=array()) {
		$order = "id  desc";
		$searchType = isset($this->data["searchType"])
		?$this->data["searchType"]
		:"search";
		$this->set("searchType",$searchType);
		switch($searchType)  {
			case "participationReport";
			$fields = array("id","first_name","last_name","email","phone","address_city",
			"allow_calls");
			break;
			case  "generate":
			if ($this->data["Participant"]["application_type"] == "personal") {
				$fields = array("id","phone","work_phone","first_name","last_name",
				"gender","race","dob","education","marital_status","annual_income","email");
			}else{
				$fields = array("id","phone","work_phone","first_name","last_name",
				"gender","race","dob","education","marital_status","annual_income","email");
			}
			$order = "last_name";
			break;
			
			default:
			$fields = array("id","phone","first_name","last_name","application_type","address_city",
			"allow_calls");
			break;
		} 
		$this->set("viewFields",$fields);
		return array($fields,$order);
	}
	function buildSearchConditions($fieldMap=array())  {
		$remap  = array_keys($fieldMap);
		$searchInfoStr="";
		foreach($this->data["Participant"]  as $field=>$val) {
			if (isset($this->data["calculatedFieldsMap"][$field])
			&& trim($val)) {
				$method  = "build".ucfirst($field)."Clause";
				$thisCondition  = $this->$method();
				if (trim($thisCondition)) $conditions[] = $thisCondition;
			}else{
				if ($val==='0' || (trim($val) && $val != "Ignore"))  {
					$op = strstr($val,"%") 
					? "like"
					: "=";
					if (in_array($field,$remap))  {
					//	pr("$field = $val remapping");
						//exit;
						$val=strtolower($val);
						
						$mappedField = $fieldMap[$field];
						$thisCondition ="$mappedField $op '$val'";
						pr($thisCondition);
					}
					
					$thisCondition ="$field $op '$val'";
					$conditions[] = $thisCondition;
					$searched[$field] = $val;
					$searchInfoStr .= Inflector::humanize("$field").": $val<br/>";
				}
			}
			
		}
		if (isset($this->data["searchType"]) && $this->data["searchType"]=="generate" && $searchInfoStr){
			$this->set("searchInfo",$searchInfoStr);
			$this->set("searched",$searched);
		}
		return  $conditions;
	}	

function ginsu($data, $sortingIndex, $ascdesc = "ASC") {
		if(is_object($data)) {
			foreach($data as $attr=>$val) {
				if(is_object($val)) {
//					pr("arrayize val?.. ");
//					pr($val);
					foreach($val as $vattr=>$vval) {
						$valArr[$vattr] = $val->$vattr;
					}
					$odata[$attr] = $valArr;
				}else{
					$odata[$attr] = $data->$attr;
				}
			}
			$data = $odata;
		}
		//pr("ginsu keys: ");
	//	pr(array_keys($data));
	$Sorter = array();		
		foreach ($data as $key => $row) {
			//    $delta[$key]  = $row['delta'];
			//    $$sortingIndex[$key]  = $row['delta'];
			if(is_array($row) && is_array($Sorter)) {
			$Sorter[$key]  = $row[$sortingIndex];
			}else{
				return $data;
			}
		}
		// Add $data as the last parameter, to sort by the common key
		//array_multisort($delta, SORT_ASC, $data);
		$flag = $ascdesc=="ASC"
		?SORT_ASC
		:SORT_DESC;
		array_multisort($Sorter,$flag, $data);
		$endArr = $data;
		return $endArr;
	}
	function mcConn() {
		if(function_exists("memcache_connect")) {
			$m = memcache_connect("localhost");
			return $m;
		}
		return false;
	}
}
