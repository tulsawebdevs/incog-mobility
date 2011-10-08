<?php
class RequestsController extends AppController {
	var $helpers = array("Javascript","Form","Cache","Interform");
	var $uses = array("Request");

	function add()  {
		if(!empty($this->data)) {
		}

	}


	function detail($requestId) {
		$res = $this->Request->read(null,$requestId,array("recursive"=>1));
		
	}


	function dashboard() {
		$allRequests = $this->Request->find("all");
		foreach($allRequests as $Request) {
		}
	}

}		
?>
