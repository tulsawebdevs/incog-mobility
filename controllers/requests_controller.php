<?php
class RequestsController extends AppController {
	var $helpers = array("Javascript","Form","Cache","Interform");
	var $uses = array("Request","Rider");

	function add()  {
		if(!empty($this->data)) {
			if($this->data["Request"]["name"]) {
				$this->data["Request"]["detail"] .= "submitted name: ".$this->data["Request"]["name"]."\n";
			}
			if($this->data["Request"]["phone"]) {
				$this->data["Request"]["detail"] .= "submitted phone: ".$this->data["Request"]["phone"]."\n";
			}
			$this->data["Request"]["status"] = "submitted";
			$lstProviders = $this->resolveProviders($this->data["Request"]);
			pr("bam");
			pr($lstProviders);
			if(!sizeof($lstProviders)) {
				$this->data["Request"]["status"] = "dispatched";
			}else{
				$this->data["Request"]["status"] = "provider undetermined";
			}
			$this->Request->save($this->data);
			pr("added");
			exit;
		}

	}

	function resolveProviders($Request) {
		if($Request["phone"]) {
			$riderMatch = $this->Rider->find("all",array(
			"conditions"=>array("phone"=>$Request["phone"])
			));
			
		}
		pr($riderMatch);
		//TODO: if more than one rider match phone
		if(isset($riderMatch)
			&& sizeof($riderMatch) == 1
		 && $riderMatch[0]["Rider"]["id"]) {
		pr("submission matched existing Rider ".$riderMatch["Rider"]["id"]);
		$lstProviders = array();
		foreach($riderMatch[0]["Type"] as $Type) {
			$typeId = $Type["id"];
			$q = "select * from providers where id in 
			(select provider_id from providers_types where type_id='$typeId' ) ";
			$typeProviders = $this->Provider->query($q);
			$lstProviders = array_merge($lstProviders,$typeProviders);
			
		}
		return $lstProviders;
		exit;
		}		
	}

	function detail($requestId) {
		$res = $this->Request->read(null,$requestId,array("recursive"=>1));
		
	}


	function dashboard() {
		$allRequests = $this->Request->find("all");
		foreach($allRequests as $Request) {
		}
		$this->set("lstRequests",$allRequests);
	}

}		
?>
