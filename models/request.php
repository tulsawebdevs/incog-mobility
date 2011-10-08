<?php
class Request extends AppModel {

	var $belongsToMany = array(
		"Rider");
	var $recursive=1;
}
?>
