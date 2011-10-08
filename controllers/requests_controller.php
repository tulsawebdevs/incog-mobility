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
//echo FULL_BASE_URL;
//exit;
                $res = $this->Request->read(null,$requestId,array("recursive"=>1));
	$requestAudio = $res["audio_url"];

    include("./Services/Twilio.php");

    include("./Services/Twilio/Capability.php");
    $accountSid = 'AC6dacc852e3782d9f8f034ce8e406ff2d';
    $authToken = '1c4747dc43eb9199d2c04dc8ed19d3ff';

    // Instantiate a new Twilio Rest Client
    $client = new Services_Twilio($accountSid, $authToken);
    echo ("<table>");
    foreach($client->account->recordings as $recording) {
pr($recording);
exit;
    echo "<tr><td>{$recording->duration} seconds</td> ";
    echo "<td><audio src=\"https://api.twilio.com/2010-04-01/Accounts/$accountSid/Recordings/{$recording->sid}.wav\" controls preload=\"auto\" autobuffer></audio></td>";
    echo "<td>{$recording->date_created}</td>";
    echo "<td>{$recording->sid}</td></tr>";
    }
    echo ("<table>");


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

function getRider($receivedFromPhone) {
	$conditions = array("phone"=>$receivedFromPhone);
	$Rider = $this->Rider->find("first",
	array("conditions"=> $conditions)
	);
	if($Rider) {
	return $Rider;
	
	}else{
		trigger_error("no rider matched conditions");
		pr($conditions);
	}
}

function twilio() {
if(!Configure::read("debug")) {
    header("content-type: text/xml");
}
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

$receivedFromPhone = str_replace("+","",$_REQUEST['From']);


$Rider = $this->getRider($receivedFromPhone);
$defaultZip = $Rider["Rider"]["default_zip"];
//pr($Rider);
$zip = $Rider["Rider"]["default_zip"];
//exit;
        $str = '';
        $str .= "<Pause length=\"2\" />";
$FULL_BASE_URL = FULL_BASE_URL;
$TwilioResponse =<<<EOF
<Response>
    <Say voice="woman">
    Welcome to the INCOG Mobility center.</Say>
    <Gather action="$FULL_BASE_URL/requests/twilio2" numDigits="1">
<Say voice="woman"> To get a ride from {$zip}, press 1 </Say>
<Say voice="woman"> To use another zip code, press 2 </Say>
<Say voice="woman"> To speak to a mobility assistant, press 0 </Say></Gather>
{$str}
</Response>
EOF;
	echo $TwilioResponse;
exit;
}

function twilio2() {
  
  pr($this->params);
  
  $menu = array();
  
  $menu = array('calloffice', 'record-known');
    
  $index = $_REQUEST['Digits'];
  
  $destination = $menu[$index];

  if(!Configure::read("debug")) {
      header("content-type: text/xml");
  }
  
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?><Response>\n";
  

  if ( $destination == 'record-known' ) {
    ?>
    <Say>Please record your request. Press * when you are finished.</Say>
    <Record action="<? echo FULL_BASE_URL . '/requests/completetwilio'; ?>" 
            finishOnKey="*"
            maxLength="30"
            />
    <?php
  } else {
    ?>
    <Say>We are forwarding you to a human.</Say>
    <?php
  }
  
  ?></Response><?php
  exit;
}

	function completeTwilio() {
		pr($this->params);
		if(!Configure::read("debug")) {
			header("content-type: text/xml");
		}
		$newAudioFile =  $_REQUEST['RecordingUrl'];
		$requestData = array("id"=>2,"audio_url"=>$newAudioFile);
		$data = array("Request"=>array($requestData));
		
		$this->Request->save($requestData);
		pr("saved ");
		pr($data);
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";


$TwilioResponse =<<<EOF
		<Response>
<Say>We have received your ride request. You should hear for a transportation provider soon.</Say>
<Say>Goodbye.</Say>
</Response>
EOF;
echo $TwilioResponse;
exit;
	}
	
  function playTwilio() {
    include("./Services/Twilio.php");
    include("./Services/Twilio/Capability.php");

    $accountSid = 'AC6dacc852e3782d9f8f034ce8e406ff2d';
    $authToken = '1c4747dc43eb9199d2c04dc8ed19d3ff';
    
    // Instantiate a new Twilio Rest Client
    $client = new Services_Twilio($accountSid, $authToken);
    echo ("<table>");
    foreach($client->account->recordings as $recording) {
    echo "<tr><td>{$recording->duration} seconds</td> ";
    echo "<td><audio src=\"https://api.twilio.com/2010-04-01/Accounts/$accountSid/Recordings/{$recording->sid}.wav\" controls preload=\"auto\" autobuffer></audio></td>";
    echo "<td>{$recording->date_created}</td>";
    echo "<td>{$recording->sid}</td></tr>";
    }
    echo ("<table>");
exit;
  }
	
}		
?>
