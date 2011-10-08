<?php
/* SVN FILE: $Id: helper.php 8166 2009-05-04 21:17:19Z gwoo $ */
/**
 * Backend for helpers.
 *
 * Internal methods for the Helpers.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 8166 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2009-05-04 16:17:19 -0500 (Mon, 04 May 2009) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Included libs
 */
App::import('Core', 'Overloadable');

/**
 * Backend for helpers.
 *
 * Long description for class
 *
 * @package       cake
 * @subpackage    cake.cake.libs.view
 */
class AppHelper extends Helper {

/*
 * It seems we don't land in this call__ unless the helper method DNE
 * .. so I don't have to check for that I guess
 */
	function call__($method, $params) {
		if (defined("QUESTIONS_INI")) {
			$questionsIni = ROOT . DS . APP_DIR . DS.QUESTIONS_INI;
			if (!file_exists(ROOT . DS . APP_DIR . DS. QUESTIONS_INI)) {
				trigger_error('QUESTIONS_INI file '.ROOT . DS . APP_DIR . DS.QUESTIONS_INI.' DNE', E_USER_WARNING);
			}
		}else{
			trigger_error("QUESTIONS_INI is not defined and unkown helper method '$method' was called", E_USER_WARNING);
		}
		$questionsConfig =parse_ini_file($questionsIni,true);
		if (isset($questionsConfig["helperMethodMap"][$method]))  {
			$methodParams = explode(",",$questionsConfig["helperMethodMap"][$method]);
			$actualMethod = array_shift($methodParams);
			if (method_exists($this,$actualMethod)) {
				switch($actualMethod)  {
					case "checklist":
					//there should be an ini value for the method in the questionOptions sxn.. 
					//parse them.
					$varName = $method."Opts";
					$field = array_shift($methodParams);
					$question = array_shift($methodParams);
//					$question=$params[0];
//					pr("got  question $question");
					if (sizeof($methodParams) ) {
						pr("Warning: unexpected elements in $questionsIni for $method");
						pr("move checklist options into the var called $varName . Proceeding.. ");
					}
					$myOptions = explode(",",$questionsConfig["questionOptions"][$varName]);
					$params = array($field,$myOptions,$question);
					break;
					case "select":
					$varName = $method."Opts";
					$field = array_shift($methodParams);
					$emptyLabel = array_shift($methodParams);
					if (!$emptyLabel) $emptyLabel = Inflector::humanize($field);	
					$myOptions = explode(",",$questionsConfig["questionOptions"][$varName]);
					foreach($myOptions as $opt) {
						$selectOptions[$opt] = $opt;
					}
					$params = array($field,$selectOptions,null,null,$emptyLabel);
					break;
					case "radio":
					$varName = $method."Opts";
					$field = array_shift($methodParams);
					$emptyLabel = array_shift($methodParams);
					if (!$emptyLabel) $emptyLabel = Inflector::humanize($field);	
					$myOptions = explode(",",$questionsConfig["questionOptions"][$varName]);
					foreach($myOptions as $opt) {
						$selectOptions[$opt] = $opt;
					}					
					$params = array($field,$selectOptions,array("label"=>$emptyLabel));
					break;
					default:
					$params = $methodParams;
					break;
				}
				$ret = call_user_func_array(array($this,$actualMethod),$params);
				return $ret;
			}else{
				trigger_error(sprintf(__('Method %1$s::%2$s is specified by file %3$s but does not exist', true), get_class($this), $actualMethod, $questionsIni), E_USER_WARNING);
//				pr("method is in $questionsIni but DNE in class ".get_class($this));
			}
		} else{
			//call proceeds as normal.. should end up with Method DNE  warning
			trigger_error(sprintf(__('Method %1$s::%2$s does not exist', true), get_class($this), $method), E_USER_WARNING);
			pr("call received method:");
			pr($method);
			pr("and params:");
			pr($params);
		}
	}
}
?>