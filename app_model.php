<?php
/*
 * TODO: establish all  vars that end up in application state[] through fat models
 */
class AppModel extends Model {
	var $actsAs = array (
		'Containable'
	);
	var $assocs =array();

	function getMyTree() {
		if (!in_array("Tree",$this->actsAs)) {
			return "cannot getMyTree unless ".$this->name." acts as a tree";
		}
		$branch = $this->find('threaded', array(
	'conditions' => array(
		$this->name.'.lft >=' => $this->data[$this->name]['lft'], 
		$this->name.'.rght <=' => $this->data[$this->name]['rght']
	)
   ));
		return $branch;
	}

	function toggle($id,$field,$idTwo="")  {
		$schema = $this->schema($field);
		if  (!isset($schema["type"]))  {
//			pr("Assuming you're toggling an xref entry.. ");
			$modelTwo = ucfirst($field);
			$habtm  = array_keys($this->hasAndBelongsToMany);
			if  (!$idTwo) {
				pr("You have not passed an Id for associated model $modelTwo. You get *no *toggle !");
				pr("field '$field' is not in the ".$this->name." model");
				return false;
			}
			if  (!in_array($modelTwo,$habtm  ))  {
				pr("model $modelTwo is not in habtms for ".$this->name);
				var_dump($this->hasAndBelongsToMany);
				var_dump($modelTwo);
				return false;
			}
			pr("removing association between ".
			$this->name." #$id and $modelTwo #$idTwo not yet implemented");
				return false;
		}
		switch($schema["type"]) {
			case "boolean":
			$q  = "update ".$this->useTable." set $field=1 
			XOR $field where id='$id'";
			$resp = $this->query($q);
			if (!$this->getAffectedRows()) {
				pr("update Failed");
				return false;
			}
			break;
			case "text":
			//for a 'set' datatype, we'll need the possible values
			$q  = "show columns from ".$this->useTable." like  '$field'";
			$r=$this->query($q);
			$str  = $r[0]["COLUMNS"]["Type"];
			$options=explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$str));
			if  (sizeof($options) != 2) {
				pr("You really want cycling, not toggling. But someday we'll accomodate  you.. ");
				pr($options);
				return false;
			}
			$if = "if ($field='".$options[0]."','".$options[1]."','".$options[0]."')";
			$xor = "update ".$this->useTable." set $field=$if where id='$id'";
			$resp = $this->query($xor);
			if (!$this->getAffectedRows()) {
				pr("update failed");
				return false;
			}
			break;
			default:
			pr("toggling field $field, type ".$schema["type"]." not yet implemented");
			return false;
			break;
		}
		$resp = $this->query("select $field from ".$this->useTable." where id='$id'");
		$newval = $resp[0][$this->useTable][$field];
		$ret = array(true,$newval);
		return $ret;
	}

/**
    * Returns a resultset array with DISTINCT fields from database matching given conditions.
    *
    * @param   mixed    $conditions SQL conditions as a string or as an array('field' =>'value',...)
    * @param   mixed    $fields Either a single string of a field name, or an array of field names
    * @return  array    Array of records
    */
   function findDistinct($conditions = null, $fields = null)
   {
      $db =& ConnectionManager::getDataSource($this->useDbConfig);


  $str = 'DISTINCT ';
  if (!is_array($fields))
  {
     $str .= '`' . $fields . '`';
  }
  else
  {
     foreach ($fields as $field)
     {
     	if ($field=="id") continue;
        $str .= '`' . $field . '`, ';
     }
     $str = substr($str, 0, -2);   
  }

  $queryData = array(
                 'conditions'   => $conditions,
                 'fields'       => $str,
                 );
  $data = $db->read($this, $queryData, false);
  return $data;      



}

	function deformatPhones($Model) {
		$phoneFields = array("phone","work_phone","fax","business_phone","alternate_phone");
		foreach($phoneFields as $f) {
			pr("started with phone  ".$Model[$f]);
			$Model[$f] = str_replace(array("(",")","-"),"",$Model[$f]);
			pr("ended with phone  ".$Model[$f]);
		}
		return  $Model;
	}
}
?>
