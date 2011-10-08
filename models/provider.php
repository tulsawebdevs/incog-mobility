<?php
class Provider extends AppModel {

	var $hasAndBelongsToMany = array(
		"Type");
	var $recursive=1;
}
?>
