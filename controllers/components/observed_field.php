<?php

class ObservedFieldComponent {
	var $changedBy;
	
	function handle($fieldName) {
		//default handler .. will return an error and bail if specified field is not in skippable
		$skippable = array("account_id_c");
		if (in_array($fieldName,$skippable)) {
			return 1;
		}
		$err = "handle() doesn't have anything to deal with $fieldName"; 
		return $err;
	}
	function handleValidationChange($data) {
		$id  = $data["id_c"];
		if (!$id) {
			pr("no id: full dataset:");
			pr($data);
		}
		$exst = "select cc.username_c,
			cc.id_c,cc.validated_c,
			ac.dealer_number_c, ac.location_number_c, a.name 
 			from contacts_cstm cc 
			left join users u on u.user_name=cc.username_c
			left join accounts_contacts x on cc.id_c=x.contact_id
			left join accounts_cstm ac on x.account_id=ac.id_c
			left join accounts a on ac.id_c=a.id 
			where cc.id_c='$id' AND x.deleted='0'";
		$userInstance = ClassRegistry::init('TopconUser');
		$userInstance->useDbConfig = "sugar";
		$rexst = $userInstance->query($exst);
/*		pr("OF::handleValidation change res:");
		pr($rexst);		
*/
		if ($rexst[0]["cc"]["validated_c"]=="0"
			&& $data["validated_c"]=="on") {
				$ret = "genAndSend";
				$info = array(
				"dealer_number_c"=>$rexst[0]["ac"]["dealer_number_c"],
				"location_number_c"=>$rexst[0]["ac"]["location_number_c"],
				"name"=>$rexst[0]["a"]["name"]
				
				);
			return (array($ret,$info));
//		exit;
			
		}else{
			pr("no need for gen and send " .$rexst[0]["cc"]["validated_c"]."-> ".$data["validated_c"]);
			$ret = array($rexst,"none");
		}
		return $ret;
	}
	function handleEmlChange($data) {
		$id  = $data["id_c"];
		if (!$id) {
			if (isset($data["id_c"])
				&& !$data["id_c"]) {
					pr("skipping .. addition of new user doesn't trigger OFs now");
					//adding a user.. can skip this
					return 1;
				}
			pr("no love: full dataset:");
			pr($data);
		}
//		pr("using id $id");
		$exst = "select u.user_hash, cc.username_c,
			cc.id_c from contacts_cstm cc left join users u on u.user_name=cc.username_c
			where id_c='$id'";
		$userInstance = ClassRegistry::init('TopconUser');
		$userInstance->useDbConfig = "sugar";
		$rexst = $userInstance->query($exst);
		$oldeml = isset($rexst[0])
		?$rexst[0]["cc"]["username_c"]
		:"";
		$neweml = $data["username_c"];
		if (($oldeml && $neweml) && $oldeml != $neweml) {
			$updateresult = $userInstance->updateEmail($oldeml,$neweml);
			//$changedBy = $this->changedBy;
			$q = "insert into content_audit
				(content_id,before_value,after_value)
				values('emailChange','$oldeml','$neweml')
				";
			$userInstance->useDbConfig = "sugar";
			$userInstance->query($q);
			
			return $updateresult;
		}else{
			if (!$oldeml || !$neweml) {
				$str = "old or new eml was blank..  query was $exst and response was:  ";
				pr($rexst);
				return $str;
			}
			return 1;
		}
	}
}
?>