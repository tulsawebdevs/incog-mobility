<?php
if($requestType) {
	echo $this->element("requests/add".ucfirst($requestType)."Request");
} else {
	echo $this->element("requests/addCreativeRequest");
}
?>