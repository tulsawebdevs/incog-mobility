<?php
//
class AppController extends Controller {
	var $helpers = array ('Html','Javascript','Form','ThsForm','Interface');
	var $components = array ("Mail","Session");
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


	function beforeFilter()  {
		return  parent::beforeFilter();
	}

	function beforeRender() {
$this->set("controllerName",$this->name);
$this->set("actionName",$this->action);
		return  parent::beforeRender();
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
