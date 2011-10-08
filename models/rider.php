<?php
class Rider extends AppModel {

	var $hasMany = array(
		"Request");
	var $hasAndBelongsToMany = array(
		"Type");
	var $recursive=1;
}
?>
