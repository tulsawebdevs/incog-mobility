<?php 

class LogComponent
{
	var $controller=true;
	
	function profileChange($data,$Model,$type="Contacts") {
		switch ($type) {
			case "Accounts":
				break;
			default:
				$contact_id = $data["id_c"];
			$q1 = "select * from sugarcrm.contacts_cstm cc left join sugarcrm.contacts c on c.id=cc.id_c
				where c.id='$contact_id' limit 1";
			$Model->useDbConfig = "oldRoot";
			$r = $Model->query($q1);
			$all = array_merge($r[0]["c"],$r[0]["cc"]);
			foreach($data as $field=>$val) {
				if (is_array($val) || !isset($all[$field]))  continue;
					if ($val=="on" && $all[$field] = 1) continue;
				if ($val != $all[$field]) {
					pr("val $val dneq old ".$all[$field]);
					$changed[$field] = $val;
				}  
			}
			if (isset($changed)) {
				$str = "profile for user ".$data["username_c"]." updated ";
				foreach ($changed as $field=>$newval) {
					$str .= "$field:$newval; ";
				}
				list($userId,$session_id) = $this->getKtSessionInfo($_SESSION["sugar_user_name"],$Model);
				$q = "insert into kt3.user_history 
					(datetime,user_id,action_namespace,comments,session_id)
					values
					(NOW(),'$userId',
					'profileUpdated',
					'$str',
					'$session_id'
					)";
				$Model->query($q);
				break;
			}
		}
	}
	function getKtSessionInfo($userName,$Model) {
		$q = "select user_id, id
		from active_sessions
		where user_id=(select id from users where username='$userName')
		limit 1";
		$r = $Model->query($q);
		if (sizeof($r)) {
			return array(
				$r[0]["active_sessions"]["user_id"],
				$r[0]["active_sessions"]["id"]
				);
		}else{
			// no active sessions it's probably the login action.. look in posted data
			$q = "select id
			from users 
			where username='$userName' 
			limit 1";
			$r = $Model->query($q);
			if (sizeof($r)) {
				$user_id = isset($r[0]) && isset($r[0]["users"]["id"])
				? $r[0]["users"]["id"]
				:"NOT FOUND";
				return array(
					$user_id,
					"logging in.. no active sess"
				);
			}else{
				pr("dang,getKtSessionInfo didn't get session from dB or posted data.. look to't ");
			}
		}
	}
	function folderAccessed($Model, $userName, $folderName) {
		list($userId,$session_id) = $this->getKtSessionInfo($userName,$Model);
		$q  = "insert into user_history
		(datetime,user_id,action_namespace,comments,session_id)
		values
		(NOW(),'$userId',
		'ktcore.user_history.folder_access',
		'$folderName',
		'$session_id'
		)";
		$Model->query($q);
	}
	function loginAction($Model,$userName,$inout) {
		list($userId,$session_id) = $this->getKtSessionInfo($userName,$Model);
		
		$q = "insert into user_history 
		(datetime,user_id,action_namespace,comments,session_id)
		values
		(NOW(),'$userId',
		'ktcore.user_history.$inout',
		'from Support Site, IP: ".$_SERVER['REMOTE_ADDR']."',
		'$session_id'
		)";
//		pr($q);
		$Model->query($q);
		
	}
	function documentDownloaded($Model,$userName,$documentId)  {
		list($userId,$session_id) = $this->getKtSessionInfo($userName,$Model);
				$q  = "insert into user_history
		(datetime,user_id,action_namespace,comments,session_id)
		values
		(NOW(),'$userId',
		'ktcore.user_history.document_download',
		(select full_path from documents where id='$documentId'),
		'$session_id'
		)";
		$Model->query($q);
	}

	function authProblem($str) {
		$output = "Error logged on ".strftime("%D %H:%M")."\n".$str;
		$l = fopen("logs/authLog.error","a");
		fwrite($l,$output);
	}
}
?>