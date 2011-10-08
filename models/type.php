<?php
class Type extends AppModel {

	var $hasAndBelongsToMany = array(
		"Rider", "Provider");
	var $recursive=1;
}
?>
