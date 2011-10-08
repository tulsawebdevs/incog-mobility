<?php
class Type extends AppModel {

	var $hasAndBelongsToMany = array(
		"Rider");
	var $hasAndBelongsToMany = array(
		"Provider");
	var $recursive=1;
}
?>
