<?php
class RequestsController extends AppController {
	var $helpers = array("Javascript","Form","Cache","Interform");
	var $uses = array("Request","Rider","Provider");

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
		pr("submission matched existing Rider ".$riderMatch[0]["Rider"]["id"]);
		$lstProviders = array();
		foreach($riderMatch[0]["Type"] as $Type) {
			$typeId = $Type["id"];
			$q = "select * from providers where id in 
			(select provider_id from providers_types where type_id='$typeId' ) ";
			pr($q);
			$typeProviders = $this->Provider->query($q);
			$lstProviders = array_merge($lstProviders,$typeProviders);
			
		}
		pr("returning ".sizeof($lstProviders));
		return $lstProviders;
		exit;
		}		
	}

	function detail($requestId) {
echo FULL_BASE_URL;
//exit;
		$res = $this->Request->read(null,$requestId,array("recursive"=>1));
		
	}
	function digest() {
		$lstProviders = $this->Provider->find("all");
		foreach($lstProviders as $Provider) {
			foreach($Provider["Type"] as $Type) {
$thisTypeId = $Type["id"];
$q = "select
	Request.*, Rider.*
	from riders_types x
	left join riders Rider on (x.rider_id=Rider.id and x.type_id='$thisTypeId' )
	left join requests Request on Request.rider_id=Rider.id
where Request.id is not null and Request.status='dispatched'  order by created_at 
";
//pr($q);
				$lstRequests = $this->Request->query($q);
			}
			if(sizeof($lstRequests) ) {
//pr(sizeof($lstRequests)."requestS: for type $thisTypeId provider ".$Provider["Provider"]["name"]);
//pr($lstRequests);
			$Mail = new IncogMail;
			$Mail->buildFromRequests($lstRequests);
			$Mail->To($Provider["Provider"]["contact_email"]);
			}else{
			//log "No requests open for provider $Provider["id"]
			}
//pr("MAil body going to ".$Provider["Provider"]["contact_email"]);
pr($Mail->body);
pr("<hr/>");
//pr($Mail);
if($Mail->Send()) {
echo "Mail sent to ".$Provider["Provider"]["name"]." (".$Provider["Provider"]["contact_email"].") successfully\n";
}
		}
	exit;
	}

	function claim($requestId) {
		$res = $this->Request->read(null,$requestId,array("recursive"=>1));
        $hash = $this->params["url"]["hash"];
        /*
        $riderMatch = $this->Rider->find("all",array(
        "conditions"=>array("phone"=>$Request["phone"])
        ));
		$provider = $this->Provider->
        */
	}

	function admin() {
		$conditions = array("status in ('provider undetermined') ");
		$undeterminedRequests = $this->Request->find("all",
		array("conditions"=>$conditions)
		);
		pr($undeterminedRequests);
		exit;

	}
	
	function dashboard() {
		$allRequests = $this->Request->find("all");
		foreach($allRequests as $Request) {
		}
		$this->set("lstRequests",$allRequests);
	}

function getUser($receivedFromPhone) {
}

function twilio() {
if(!Configure::read("debug")) {
    header("content-type: text/xml");
}
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";


$receivedFromPhone = str_replace("+","",$people[$_REQUEST['From']]);


$username = $this->getUser($receivedFromPhone);
$zip = "74136";
        $str = '';
        $str .= "<Pause length=\"1\" /><Say voice=\"woman\"> To get a ride from {$zip}, press 1 </Say>";

$TwilioResponse =<<<EOF
<Response>
    <Say voice="woman">Hello <?php echo $username?> . Welcome to the INCOG Mobility center.</Say>
{$str}
</Response>
EOF;
	echo $TwilioResponse;
exit;
}

}		
?>
