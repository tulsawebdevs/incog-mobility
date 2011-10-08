<?php
class RestHelper extends FormHelper
{
	var $helpers = array('Html',"Form");

	function userEdit($user,$user_type="employee") {
		echo $this->create("User",array(
			"url"=>array("controller"=>"users",
			"action"=>"edit")));
		//user should be a simple key=>value array repping  the fields.. 
		foreach($user as $key=>$value) {
			echo FormHelper::input("User.$key",array("value"=>$value));
		}
		
		switch($user_type)  {
			case "regional":
			break;
			
			case "primary":
			echo $this->accessLevelEditor();
			break;
			
			default:
			break;
		}
		
		echo $this->submit();
		echo $this->end();
	}

	function dealerEdit($dealer) {
		return "<b>No dealer edit form in rest helper /. </b>";
	}
	
	function accessLevelEditor() {
		echo "asdlfk jalsdkf jalskdjf asldfkj ";
	}
}
?>
