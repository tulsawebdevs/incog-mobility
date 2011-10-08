<?php
/**
 * interface.php contains information to be made available in template form to jquery/css
 * 
 */

/**
 * @package ThsPmp
 *  
 * TODO: research & document the intention of this layer (MVIC anyone?)
 *
 */
class InterfaceHelper extends AppHelper {
	var $additionalBodyClasses;
	var  $model="Participant";
	/*
	 * 	hasForm, index, edit?
	 * 
	 */
	function formInfo($obj)  {
		$model = $obj->model;
	$html =<<<EOF
	<form action="#">
	<input type="hidden" id="mainFormModel" name="formModel" value="{$model}">
	</form>
EOF;
	return  $html;
	
	}	
}
?>