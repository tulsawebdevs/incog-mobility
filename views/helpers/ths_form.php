<?php
//TODO: find the  "label" index of options that was getting passed to parent's  checkbox
//  and becoming an attribute -- unset this after processing

/**
 * ths_form.php contains the meat of the form framework so far
 *
 */

/**
 * @package ThsPmp
 *
 * SOMEDAY: review checklist/checkbox options for better encapsulation e.g. nore four args to checklist()
 *
 */
class ThsFormHelper extends FormHelper {
	var $modelMap;
	var $formMode="submitting";
	var $followup = array();
	var $editingModel;
	var  $ignoreLabelMap = array(
	"gender"=>"Either"
	);
	var $reportingQuestionMap = array(
	"VideoGameConsole"=>"Game Consoles"
	);

/*
 * Overload some existing methods
 */

	function create($model,$options=array(),$mode='submitting') {
		if  (!$options) $options = array();
		$this->formMode  = $mode;
		if (!isset($options["action"]) && !isset($options["url"])) {
			switch($mode) {
				//sets a different action based on mode
				case "reporting":
					$options["action"] = isset($options["action"])
					? $options["action"]
					: $this->model()."/index";
				break;
				case "editing":
					$options["action"] = isset($options["action"])
					? $options["action"]
					: $this->model()."/edit";

				break;
				default:
				//in submitting mode, default action
				// (this->model() . "/add" )  is fine
				break;
			}
		}
		$ret  = parent::create($model,$options);
		return  $ret;
	}
	function input($fieldName, $options = array(),$skipDiv=false) {
		if  (isset($this->editingModel[$this->model()])) {
//			pr("thismodel  is ".$this->model()." field $fieldName   in editing ".$this->model()." ?");
//			pr($this->editingModel[$this->model()]);
			$options["value"] =  $this->editingModel[$this->model()][$fieldName];
//			pr("set  val  to ".$this->editingModel[$this->model()][$fieldName]);
		}
		if (is_scalar($options) && $options == "required"
		&& $this->formMode  != "reporting" ) {
			$options = array("class"=>"required");
		}
		if ($this->formMode == "reporting") {
			if (isset($options["class"]))  {
				$newclass = str_replace("required","",$options["class"]);
				$options["class"]  = $newclass;
			}
		}
		$options["div"] = false;
		$inputArg = $fieldName;
		if  (!$this->model()) {
			$str = "model() returned false.. did  you thsForm->create()?";
			if  (!isset($this->modelMap))  {
				$str .=  "did  you forget to thsForm->initmodelMap() in the  view?..";
			}
			$str .= "we can still make an input but the element's  name will just be '$inputArg'.. ";
			pr($str);
		}else{
			$Model = $this->modelMap[$fieldName];
			$inputArg = $Model. "." . $fieldName;
		}
		// when options are string use the string as an url to populate the select options
		if (isset ($options['options']) && is_string($options['options'])) {
			$options['options'] = $this->requestAction($options['options']);
		}
		$options["class"] = isset($options["class"])
		?$options["class"]." text"
		:"text";
		if (is_scalar($options["class"]) && $options == "required") {
			$options["class"] = isset($options["class"])
			? $options["class"]." required"
			:$options["class"];
		}
		$out  = $this->modifiedCakeInput($inputArg, $options);
		$ret = $skipDiv
		?$out
		:"<div class=\"field\">$out</div>";
		return $ret;
	}
	function textarea($fieldName, $options = array()) {
		$html ="<div class=\"field textarea\">";
		if  (isset($options["label"])) {
			$html .= $this->label($fieldName,$options["label"]);
			unset($options["label"]);
		}
		$html .= parent::textarea($fieldName,$options);
		if  (isset($options["description"])) {
			$html .= "<span class=\"description\">".$options["description"]."</span>";
			unset($options["description"]);
		}
		$html .= "</div>";
		return $html;
	}
	//When in reporting mode, select() prepends "Ignore" option
	function select($fieldName,$myOptions,$selected,$attributes=array(),$showEmpty=null)  {
		//move the showEmpty var into enclosing div, then unset.
		$inputId = $this->model().ucfirst(Inflector::camelize($fieldName));
		$html = "<div class=\"field\">";
		$html .= "<label for=\"$inputId\">$showEmpty</label>";
		if ($blankOffset = array_search("- Select -",$myOptions)) {
			$fixt = array(""=>"- Select -");
			unset($myOptions[$blankOffset]);
			$showEmpty = $fixt;
		}else{
			$showEmpty = null;
		}
		if ($this->formMode == "reporting")  {
			$ignoreLabel = isset($this->ignoreLabelMap[$fieldName])
			?$this->ignoreLabelMap[$fieldName]
			:"Any";
			$new = array("Ignore"=>$ignoreLabel);
			foreach($myOptions as $val=>$label)  {
				$new[$val] = $label;
			}
			$myOptions = $new;
			if  (!is_array($attributes))  {
				$attributes = is_null($attributes)
				?array()
				:array($attributes);
			}
			if (sizeof($myOptions)>3
			&& !strstr($fieldName,"state")) {
				$attributes  = array_merge($attributes,array("multiple"=>"multiple","size"=>"5"));
			}
			
		}
//		pr($attributes);
		$html .= parent::select($fieldName,$myOptions,$selected,$attributes,$showEmpty);
		$html .= "</div>";
		return  $html;

	}
	//When in reporting mode, radio() prepends "Ignore" option
	//also looks for followups
	function radio($fieldName, $options = array(), $attributes=array(),$skipDiv=false) {
		$html = $this->radioDL($attributes);
		unset($attributes["label"]);
		$attributes["separator"] = "</dd><dd>";
		$attributes = $this->_initInputField($fieldName, $attributes);
		$legend = false;
		if ($this->formMode == "reporting")  {
			$ignoreLabel = isset($this->ignoreLabelMap[$fieldName])
			?$this->ignoreLabelMap[$fieldName]
			:"Any";
			$new = array("Ignore"=>$ignoreLabel);
			foreach($options as $val=>$label)  {
				$new[$val] = $label;
			}
			$options = $new;
		}
		if (isset($attributes['legend'])) {
			$legend = $attributes['legend'];
			unset($attributes['legend']);
		} elseif (count($options) > 1) {
			$legend = __(Inflector::humanize($this->field()), true);
		}
		$label = true;
		$inbetween = null;

		if (isset($attributes['separator'])) {
			$inbetween = $attributes['separator'];
			unset($attributes['separator']);
		}

		if (isset($attributes['value'])) {
			$value = $this->formMode == "reporting"
			? "Ignore"
			:$attributes['value'];
		} else {
			if ($this->formMode == "reporting")  {
				$value = "Ignore";
			}else{
				$value =  $this->value($fieldName);
			}
		}
		$out = array();
		foreach ($options as $optValue => $optTitle) {
			$optionsHere = array('value' => $optValue);

//			if (isset($value) && $optValue == $value) {
			//so that radio  values can be zero without messing this up, use type & val comparison
//			pr($this->editingModel[]);
			$mod = $this->model();
			$fieldonly = str_replace($this->model().".","",$fieldName);
			//pr($this->editingModel[$mod][$fieldName]);
			if ($this->formMode=="editing") {
				if (!isset($value)&& isset($this->editingModel[$mod][$fieldonly])) {
					$value = $this->editingModel[$this->model()][$fieldonly];
				}
			}
			if (isset($value) && $optValue ==$value) {
				
				//pr("optValue $optValue equals $value");
				if ($this->formMode!="reporting" || $optValue ===$value) {
					$optionsHere['checked'] = 'checked';
				}
			}
			//could be an array, or an array of arrays
			// i.e. if more than one option has a followup
			$ehtml =  "";
			$thisHasFollowup = false;
			
			if (isset($attributes["followup"]) && !isset($attributes["followup"][0])) {
				
//				pr("attributes[follouwp] set but not a[f][0]..  processing syntax one or two (plural method) ");
//				pr("calling bF on field $fieldName against optValue '$optValue' attributes:");
//				pr($attributes);
				list($ehtml,$thisHasFollowup,$attributes) = $this->buildFollowups($attributes,$ehtml,$optValue);
			}
			if (isset($attributes["followup"])&& is_array($attributes["followup"])) {
				$fuKeys  = array_keys($attributes["followup"]) ;
			if (sizeof($fuKeys==1) && $fuKeys[0] =="1") {
//				$tmp  = array(
//				"0"=>
//				array($attributes["followup"][1])
//				);
				$attributes["followup"] = array(
				"0"=>
				array($attributes["followup"][1])
				);;
			}
			}
			
			if (isset($attributes["followup"])  
			&&  is_array($attributes["followup"])
			&& !is_array($attributes["followup"][0])
			&& $attributes["followup"][0]==$optValue) {
				if($optValue=="Ignore"  &&  $this->formMode=="reporting") {
				//no  followups  for ignore
				$attributes["followup"]=null;
				pr("got into followup but we're  on ignore.. ");
				}else{
//				pr("a[f] set, a[f][0] not an array, and a[f][0]==optValue.. processing syntax three");
//				pr(".. followup structure ");
//				pr($attributes["followup"]);
				$thisHasFollowup = true;
//				pr("val $optValue ==  first el of followup arr  (".$attributes["followup"][0].") attch hasclass");
				if(!isset($attributes["class"])) {
					$attributes["class"]  = "has-followup";
				}else{
					$newclass  =$attributes["class"]." has-followup";
					$attributes["class"] = $newclass;
				}
				$ehtml  = $this->buildFollowup($attributes["followup"]);
				}
			}
			$attrsForParsing = is_array($attributes)
			?$attributes
			:array();
			//so we don't get folowup='' as an attr of a normal input...
			if  (isset($attrsForParsing["followup"]) && is_array($attrsForParsing["followup"]))  {
				unset($attrsForParsing["followup"]);
			}
			$parsedOptions = $this->_parseAttributes(
				array_merge($attrsForParsing, $optionsHere),
				array('name', 'type', 'id'), '', ' '
			);
//			pr($parsedOptions);
			if (!$thisHasFollowup ) {
				$parsedOptions = str_replace("has-followup","",$parsedOptions);
			}
//			pr("parsedOptions optVal $optValue:");
//			pr($parsedOptions);

			$tagName = Inflector::camelize(
				$attributes['id'] . '_' . Inflector::underscore($optValue)
			);

			//reversing the order, so that label wraps button.. I hope
			//if this doesn't work, see original code in graveyard
			$myout  = "\n".sprintf(
				$this->Html->tags['radio'], $attributes['name'],
				$tagName, $parsedOptions, $optTitle
			);

			if ($label) {
				$optTitle =  sprintf($this->Html->tags['label'], $tagName, null, $myout);
			}
			if ($ehtml) {
				$optTitle .= $ehtml;
				$ehtml = "";
			}
			$out[] =  $optTitle;
		}
			$hidden = null;
			if (!isset($value) || $value === '') {
				//pr("wtf with the hidden..  ");
//				$hidden = $this->hidden($fieldName, array(
	//				'id' => $attributes['id'] . '_', 'value' => '', 'name' => $attributes['name']
		//		));
			}
			$out = $hidden . join("\n".$inbetween, $out);
//
//			if ($legend) {
//				$out = sprintf(
//					$this->Html->tags['fieldset'], '',
//					sprintf($this->Html->tags['legend'], $legend) . $out
//				);
//			}
			$out  = $this->output($out);
			$html .= $out ."</dd>";

			$html .= "</dl>";
//						echo "<textarea  rows=9 cols=90>$html</textarea>";
		return $html;
	}
	function checkbox($fieldName, $options = array()) {
		unset($options["label"]);
		$options = $this->_initInputField($fieldName, $options);
		$value = current($this->value());

		if (!isset($options['value']) || empty($options['value'])) {
			$options['value'] = 1;
		} elseif (!empty($value) && $value === $options['value']) {
			$options['checked'] = 'checked';
		}
		$hiddenOptions = array(
			'id' => $options['id'] . '_', 'name' => $options['name'],
			'value' => '0', 'secure' => false
		);
		if (isset($options['disabled']) && $options['disabled'] == true) {
			$hiddenOptions['disabled'] = 'disabled';
		}
		$output = "";
//		$output = $this->hidden($fieldName, $hiddenOptions);

		return $this->output($output . sprintf(
			$this->Html->tags['checkbox'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		));
	}
	function button($buttonLabel, $passedOptions = array(),$imageFile) {
		$options = array_merge(
			array('id'=>'thsButton','name'=>'thsButton','type' => 'button', 'value' => $buttonLabel),
			$passedOptions);
		if (isset($options['name']) && strpos($options['name'], '.') !== false) {
			if ($this->value($options['name'])) {
				$options['checked'] = 'checked';
			}
			$name = $options['name'];
			unset($options['name']);
			$options = $this->_initInputField($name, $options);
		}
//		$options = $this->_initInputField($name, $options);
		//if (!) {
		$class=isset($options["class"])
		?"class=\"".$options["class"]."\""
		:"";
//		}else{
//			$class=;
//		}
		$value=$options['value'];
		$type  = $options['type'];
		$html = "<button type=\"$type\" id=\"".$options["id"]."\" value=\"$value\" $class ";
		$attrString="";
//		pr($passedOptions);
		//additional Attributes
		foreach($passedOptions as $key=>$val)  {
			if ($key  !="id") {
				$attrString .= "$key=\"$val\"";
			}
		}
		$html .=  " $attrString >";
		$html .=  "<img src=\"/img/icons/$imageFile.png\" alt=\"$buttonLabel\" />&nbsp;&nbsp;$buttonLabel";
		$html .= "</button>";
		return $html;
	}

/*
 * Wrappers around existing methods
 */
	//yesno =  two radio buttons.
	function yesno($fieldName,$question,$attributes=array()) {
		if ($fieldName=="kids" &&  $this->formMode=="reporting") {
			$options = 	array("1"=>"Yes","0"=>"No");
			$attributes["label"] =  "Has kids";
			$attributes["class"]= "genderAgeReporting";
			$attributes["followup"]= array(
				1,"genderAgeSearch","Age and Gender"
			);
			//genderAgeReporting
			$khtml = $this->radio("Participant.have_kids",$options,$attributes);
			return $khtml;
		}
		if($fieldName=="Participant.have_kids" &&  $this->formMode=="reporting") {
			$html  = $this->genderAgeSearch($fieldName,null,array("label"=>"Participants with Children:"));
			return $html;
		}
		if  (!isset($attributes["label"])) {
			//a possibility: this is a followup yesno, in which case the call
			// was probably yesno("nice_kids",array("label"=>"Are they well behaved?"))
			// that is, no attributes but question is an array w/label index
			$attributes["label"]  = is_array($question)
			? $question["label"]
			: $question;
		}
		if ($this->formMode=="reporting") {
			//	rewrite 
			// or yank question stuff off yesnos
			if (isset($this->reportingQuestionMap[$fieldName])) {
				$attributes["label"] =$this->reportingQuestionMap[$fieldName]; 
			}else{
			$attributes["label"] = ucfirst(str_replace(array("Do you ","Are you "),"",$attributes["label"]));
			}
			
		}
		$options = 	array("1"=>"Yes","0"=>"No");
//		if (strstr($fieldName,"kids")) {
//			pr($options);
//			pr($attributes);
//			pr($this->reportingQuestionMap);
//			exit;
//		}
		return $this->radio($fieldName,$options,$attributes);
	}
	function childGender($fieldName,$question,$attributes=array()) {
		if  (!isset($attributes["label"])) {
			//a possibility: this is a followup yesno, in which case the call
			// was probably yesno("nice_kids",array("label"=>"Are they well behaved?"))
			// that is, no attributes but question is an array w/label index
			$attributes["label"]  = is_array($question)
			? $question["label"]
			: $question;
		}
		$options = 	array("M"=>"M","F"=>"F");
		return $this->radio($fieldName,$options,$attributes);
	}

	//checklist = arbitrary no. of checkboxen
	function checklist($fieldName,$myOptions,$question)  {
		$ret = "<dl class='fields'><dt>$question</dt>";
		foreach($myOptions as $opt) {

			$camelOpt = Inflector::camelize($opt);
			$inputId = $this->model().ucfirst(Inflector::camelize($fieldName))."_".Inflector::underscore($camelOpt);
			$ret .= "<dd><label for=\"$inputId\">";
			$ret .= $this->checkbox($fieldName,array("id"=>$inputId,"value"=>$opt,"label"=>$opt));
			$ret .= $opt ."</label></dd>";
		}
		$ret .="</dl>";
		return  $ret;
	}
	//actions() displays typical buttons based on formMode
	function actions() {

		switch ($this->formMode)  {
			case "submitting":
				$submitLabel = "Submit";
			break;
			case "editing":
				$submitLabel = "Save";
			break;
			case "reporting":
				$submitLabel = "Search";
			break;
			default: //?
				$submitLabel = "Default submit case in thsform::actions()";

			break;
		}
		// FIXME: Refactor with new button helper
		$html = "<hr /><div class=\"actions\">";
		$html .= "<button class=\"primary ".strtolower($submitLabel)."\" type=\"submit\">";
		$html .=  "<img src=\"/img/icons/".strtolower($submitLabel).".png\" alt=\"$submitLabel\" />&nbsp;&nbsp;$submitLabel";
		$html .= "</button>";
//		require_once("html.php");
		$htmlHelper  = new HtmlHelper;
		$html  .= $htmlHelper->link("Cancel",array("action"=>"index"));
		$html .= "</div>";
		return  $html;
	}

	//comon wrappers around input()
	function email($arg1="email",$params=array()) {
		if (!$params) {
			//by default emails have the class required when submitting
			$class = $this->formMode == "submitting"
			? "required email"
			: "";
			$params = array(
				"class"=>$class,
				"label"=>"Email",
				"div"=>false
			);
		}
		if  (is_scalar($arg1) ) {
			if (in_array($arg1,array("required","unique"))) {
			//dev has passed a single string here,
			// i.e.	a class should  be appended
			$fieldName = "email";
			$params["class"] .= " $arg1";
			}else{
				$fieldName = $arg1;
			}
		}else if (is_null($arg1)) {
			$fieldName = "email";
		}else{
			pr("arg1 to email() is not scalar or null: ");
		}
		$html = '<div class="field">';
		$html .= $this->input($fieldName, $params,true);
		$html .= "</div>";
		return $html;
	}
	function fullName($attrs=null) {
		if  ($attrs=="required") {
			$attrs = array_merge(array("class"=>$attrs),array("div"=>false));
		}else if (is_scalar($attrs)) {
			$attrs = array_merge(array($attrs),array("div"=>false));
		}
		// Decided against treating these as "related/range" data type
		// Name is the only non-range-but-related pattern in the entire site, so treat as two separate fields
		// Updated strs since all attrs MUST use double quotes
		$html = '<div class="field">';
		$html .= $this->input("first_name",$attrs,true);
		$html .= '</div>';
		$html .= '<div class="field">';
		$html .= $this->input("last_name",$attrs,true);
		$html .= '</div>';
		return $html;
	}
	function passwordWithRetype()  {
		$html = "";
		$html .= $this->input("password",array("type"=>"password"));
		$html .= $this->input("retype_password",array("type"=>"password"));
		return $html;
	}
	function phone($inputArg="",$includeExtension=null,$attrs=array()) {
		if (!$inputArg || is_null($inputArg)) {
			$inputArg="phone";
		}else if ($inputArg=="required") {
			$inputArg="phone";
			$attrs=array_merge($attrs,array("class"=>"required"));
		}
		if ($inputArg == "business_phone" ||  $includeExtension)  {
			if ($includeExtension=="required") {
				$attrs=array_merge($attrs,array("class"=>"required"));
			}
			$extension = true;
		}
		$fieldName = $inputArg;
		if (isset($this->modelMap[$fieldName]) &&
			$Model = $this->modelMap[$fieldName]) {
			$inputArg = $Model. "." . $fieldName;
		}
		$html = '<div class="phone field">';
		$defaultAttrs = array();
		if ($this->formMode  != "reporting") {
			$defaultAttrs["class"]="phone";
		}
		$defaultAttrs["maxlength"]=14;
		if  (is_array($attrs))  {
			$myAttrs = array_merge($defaultAttrs,$attrs);
		}else{
			$myAttrs =$defaultAttrs;
		}
		$html .= $this->input("$fieldName",$myAttrs,true);
		if (isset($extension)  && $extension)  {
			$html .= "Ext. ".$this->input($fieldName."_ext",array("label"=>false,"class"=>"extension"),true);
		}
		$html .= '</div>';
		return $html;
	}
	function address($inputArg="",$fieldName="address") {
		$defaultClass  = "";
		if ($inputArg == "required") {
			$defaultClass = "required";
			$inputArg == "";
		}else if ($fieldName=="required") {
			$defaultClass = "required";
			$fieldName=$inputArg;
		}
		if (!$inputArg) {
			$inputArg="address";
			$fieldName = $inputArg;
		}
		//TODO: test why  this  was comented  out
		$fieldName = $inputArg;
		$label = Inflector::humanize($fieldName);

		$html ='<div class="address field">';
		$stem = $fieldName."_street1";
		$labelFor = ucwords($this->model());
		$labelFor .= ucwords($this->camelize($stem));

		$options  = array(
		"label"=>false,
		"class"=>"street $defaultClass");
		if ($this->formMode  == "reporting") {
			$newclass  =  str_replace("required","",$options["class"] );
			$options["class"] = $newclass;
		}

		$reqspan = strstr($options["class"],"required")
		?"<span class=\"required\">*</span>"
		:"";
		$html .="<label for=\"{$labelFor}\">{$label}{$reqspan}</label>";
		$html .= $this->inputWithDescription(
		$stem,$options,"Street Address");

		$s2Opts = $options;
		$street2Class = str_replace("required","",$options["class"]);
		$s2Opts["class"]  = $street2Class;
		$html .= $this->inputWithDescription(
		$fieldName."_street2",$s2Opts,"Street Address 2");

		/*$html .= $this->input($stem,array(
		"label"=>false,
		"class"=>"$defaultClass address")
		);
		$html .= $this->input($fieldName."_street2",array(
		"label"=>false,
		"class"=>"$defaultClass address")
		);*/

		$options  = array(
		"label"=>false,
		"class"=>"city $defaultClass");
		$html .= $this->inputWithDescription(
		$fieldName."_city",$options,
		"City");

		$html .= $this->geographySelector($fieldName."_state","United States","Georgia");

		$options = array(
		"label"=>false,
		"class"=>"zip $defaultClass");
		$html .= $this->inputWithDescription(
		$fieldName."_zip",$options,
		"Zip Code");
		
		/*$html .= $this->input($fieldName."_zip",array(
		"label"=>false,
		"class"=>"zip $defaultClass")
		);
		$html .= '<span class="description">Zip Code</span>';*/
		$html .= '</div>';
		return $html;
	}
	function inputWithDescription($fieldName,$options,$description,$dskipDiv=false)  {
		$dclass = "field ".$options["class"];
		if (!$dskipDiv) {
			$html = "<div class=\"$dclass\">";
		}
		$html .= $this->input($fieldName,$options,$skipDiv=true);
		$html .= "<span class=\"description\">$description </span>";
		if (!$dskipDiv) {
			$html .= "</div>";
		}
		return $html;
	}
	function dob($arg1="dob",$params=array(),$skipDiv=false)  {
		if (!$arg1 || is_null($arg1)) {
			$arg1 = "dob";
		}
		if (!$params) {
			$class = $this->formMode == "submitting"
			? "date"
			: "";
			$params = array(
				"class"=>$class,
				"label"=>"Date of Birth",
				"div"=>false
			);
		}
		if  (is_scalar($arg1) ) {
			if (in_array($arg1,array("required","unique"))) {
			//dev has passed a single string here,
			// i.e.	a class should  be appended
			$fieldName = "dob";
			$params["class"] .= " $arg1";
			}else{
				$fieldName = $arg1;
			}
		}
		//$html = "<div class=\"field\">";
		$html = $this->inputWithDescription($fieldName,$params,"mm/dd/yyyy",$skipDiv);
		//$html .= "</div>";
//		echo "<textarea rows=7 cols=80>";
//		pr($params);
//		echo "$html </textarea>";
		return $html;
	}

/*
 * Inits and helpers..
 */
	function init($modelMap,$mode='submitting') {
		//$this->initModelMap($modelMap);
		$this->formMode  = $mode;
	}
	//opens the dl element, adds dt and opens first dd
	function radioDL($attributes) {
		$label  = isset($attributes["label"])
		?$attributes["label"]
		:"";
		$dlclass  = isset($attributes["class"])
		? $attributes["class"]." fields"
		: "fields";

		if (isset($attributes["followup"]))  $dlclass .= " progressive";

		$html = "<dl class=\"$dlclass\">";
		$html .= "<dt>$label</dt>";
		$html .= "<dd>";
		return $html;
	}

	function formatPhones($Model) {
		$phoneFields = array("phone","work_phone","fax","business_phone","alternate_phone");
		foreach($phoneFields as $f) {
			if (isset($Model[$f]) && $Model[$f])  {
				if (strlen($Model[$f])  != 10)  {
					if  (substr($Model[$f],0,1)=="(")  {
						pr("ignoring prefomatted '$f'.. ");
						pr($Model[$f]);
						//someone else formatted this already..
						continue;
					}
//					pr("Model[f] not 10 chars long @ $f  val  is ".$Model[$f]);
					//someone  goofed
				}else{
					$startPhone = $Model[$f];
					$finalPhone = "(".substr($startPhone,0,3).") ".substr($startPhone,3,3)."-".substr($startPhone,6,4);
				}
				$Model[$f] = $finalPhone;
			}else{
			}
		}
		return $Model;
	}

/*
 * Patterns
 */
//range ("minmax")
	function minmax($field,$type="match",$extra=array()) {
		$prefix="";
		$humanField = Inflector::humanize($field);
		if(!is_null($extra) && isset($extra["associationModel"])) {
				//e.g. search for records with related model in age range.. 
				$prefix=$extra["associationModel"].".";
	//			$humanField = $associationModel." ".$field; 
		}
		$html = "";
		$inputOpts  = array_merge($extra,array("label"=>false));
		$inputFrom = $this->input($prefix.$field."From",$inputOpts,true);
		$inputTo = $this->input($prefix.$field."To",$inputOpts,true);
		if (strstr($humanField,".")) {
			$humanField =  ucfirst(substr($humanField,strpos($humanField,".")+1,strlen($humanField)));
		}
//removed :	<p class="instructions">At least one value is required</p> from before label

$html .=<<<EOF
<div class="minmax">
	<div class="field">
		<label>{$humanField}</label>
		{$inputFrom}
		<span class="description">From</span>
	</div>
	<div class="field">
		<label>&nbsp;</label>
		{$inputTo}
		<span class="description">To</span>
	</div>
</div>
EOF;
		return  $html;
	}
//"Repeatable" pattern
	function repeatable($method,$fieldName="",$options=array()) {
		$options["class"] = isset( $options["class"])
		? $options["class"]. " repeatable"
		:  "repeatable";
		if (isset($options["associationModel"])) {
			$mainModel= $this->model();
			$optStr = print_r($options,1);
			$relatedModel =is_array($options)  && isset($options["associationModel"])
			? $options["associationModel"]
			: trigger_error(sprintf(__("you  have to pass a related model to repeatable or
			you will  die. Your options were: ".$optStr, true)), E_USER_ERROR);
		$objectLabel  = isset($options["objectLabel"]);
			$R =&  ClassRegistry::init($relatedModel);
			$M =&  ClassRegistry::init($this->model());
			$btKeys  = array_keys($R->belongsTo);
			if (!in_array($mainModel,$btKeys)) {
				pr("belongsTo keys for $relatedModel: ");
				pr($btKeys);
				pr("!in arr mainMOdel '$mainModel' ?  ");
				trigger_error(sprintf(__("relatedModel $relatedModel must be normal belongsTo  to work", true)), E_USER_ERROR);
			}
			$btKeys  = array_keys($M->hasMany);
			if (!in_array($relatedModel,$btKeys)) {
				pr("hasMany keys for $mainModel");
				pr($btKeys);
				trigger_error(sprintf(__("mainModel $mainModel must have hasMany $relatedModel for repeatable to work", true)), E_USER_ERROR);
			}
			$relatedFields = array_keys($R->_schema);
			if  (!is_array($fieldName) && !in_array($fieldName,$relatedFields)) {
				trigger_error(sprintf(__("field $fieldName specified for  repeatable DNE", true)), E_USER_ERROR);
			}
			if  (!is_array($fieldName) && !in_array($fieldName,$relatedFields)) {
				$fieldName ="$relatedModel.0.$fieldName";
}
		}
		unset($options["associationModel"]);
$html = "\n<div class=\"repeatable field\">";
		if (is_array($fieldName)) {
			foreach($fieldName as $relField) {
				$tfieldName ="$relatedModel.0.$relField";
//				pr("repeatable() in array of fieldnames calling $method with tfieldName $tfieldName");
				unset($options["maxlength"]);

				if($relField  == "gender") {
					$options["label"] = ucfirst($relField). " (M/F)";
					$options["maxlength"] = "1";
					$options["size"]=3;
					$method="radio";
					$options=array("M"=>"Male","F"=>"Female");
					pr("gender using method $method tFieldName $tfieldName options: ");
					pr($options);
				}else if($relField  == "dob") {
//					$options["label"] = ucfirst($relField);
					$options["label"] = "Date of Birth";
					$options["class"] = "date";
					$method = "dob";
				}else{
					$options["label"] = ucfirst($relField);
				}
//				pr("calling func $method in repeatablethingy.. ");
				$html .= call_user_func_array(array($this,$method),array($tfieldName,$options,true));
			}
		}else{
			$html = "\n<div class=\"repeatable field\">";
			$html .= call_user_func_array(array($this,$method),array($fieldName,$options,true));
		}
		$html .= "\n<span class=\"description\">Additional fields are optional</span>";
		$html .= $this->repeatableStub($fieldName,$relatedModel);
		$html .= $this->repeatableActions($objectLabel);
		$html .= "</div><!-- /.repeatable.field -->";
		return $html;

	}
	function repeatableStub($fieldName,$relatedModel="")  {
		$html ="<div class=\"src\">";
//		$html .= $this->input($fieldName,array("class"=>"repeatable"),true);
//		$html .= parent::input($fieldName,array("label"=>false,"class"=>"repeatable"),true);
		if(is_array($fieldName)){
			foreach($fieldName as $relField) {
				$options=array();
//				pr("building stub in array $relField.. ");
				$name = "data[$relatedModel][$relField]";
				$options["data-nameIndices"] ="$relatedModel-$relField";
				unset($options["maxlength"]);
				if ($relField=="gender")  {
//					$options["maxlength"]="1";
//					$options["size"]="3";
				}else if ($relField=="dob")  {
				}
				$html .= $this->repeatableInterior($relField,$relatedModel,$options);
			}
//			return $html;
		}else{
			$html .= $this->repeatableInterior($fieldName);
		}
	$html  .="<button type=\"button\" class=\"inline remove\">
				<img src=\"/img/icons/delete.png\" width=\"16\" height=\"16\" alt=\"Remove\" />
			</button>";
		$html .= "</div><!-- /#src -->";
		return $html;
	}
function repeatableInterior($fieldName,$intModel="",$options="") {
	if (!$intModel) $intModel=$this->model();
	$html ="";
if  (strstr($fieldName,".")) {
	$bits = explode(".",$fieldName);
	$name = "data[".implode("][",$bits)."]";
}else{
	$name = "data[".$intModel."][$fieldName]";
}
	$nameIndicesStr="";
	if ($fieldName=="dob") {
		$defaults  = array("name"=>$name,"class"=>"repeatable text");
		$options  = array_merge($options,$defaults);
		$html .= $this->dob(null,$options,true);
	}else{
		$defaults  = array("name"=>$name);
		$html .= $this->radio($fieldName,array("M"=>"Male","F"=>"Female"),$options);
//		echo "<textarea rows=7 cols=80>";
//		pr($options);
//		echo "$html </textarea>";
	}
	
	
	return $html;
}
	function repeatableActions($objectLabel) {
		$html =<<<EOF
		<div class="actions">
			<button type="button" class="add">
				<img src="/img/icons/add.png" alt="Add" />
				Add another {$objectLabel}
			</button>
		</div><!-- /.actions -->
EOF;
		return $html;
	}

//"Followup" pattern
	function buildFollowup($followup=array())  {
		$ehtml = "";
		//if a one-dim. array is passed, it's just of the form
		// ([optionVal],[helperMethod],[followupFieldName],[label]
		//SOMEDAY  optional fifth arg specifying full set of options/attributes
		//   and/or allow scalar *or array for fourth arg, defaulting to "Label" only
		
//		pr("bF() actingon ");
//		var_dump($followup);
		if  (is_scalar($followup)) {
			$ehtml = "scalar cannot be sliced... why is followup scalar?";
		}else{
			if  (!isset($followup[0])) {
				$keys  = array_keys($followup);

				$firstKey  = $keys[0];
				$tmp = $followup[$firstKey];
				$followup = array($tmp);
//				pr("buildFollowup()  got invalid  firstElement from  ");
//				pr($followup);
			}
			$firstElement = $followup[0];
			if  (is_array($firstElement)) {
				foreach($followup as $triggerValue=>$followupArray) {
					//allows the same structure.. i.e. multiple followups attached to
					// a single triggerValue,
					// *or a one-dim. four-element array specifying a single followup
					$ehtml = $this->buildFollowup($followupArray);
				}
			}else{
				$triggerValue = $followup[0];
				$method = $followup[1];
				if (!isset($followup[2]) || !isset($followup[3])) {
					if (method_exists($this,$method)) {
						//this is okay if the method is a relatedChecklist()  wrapper
						//or  something  that doesn't declare its field
						$relatedFuMethods =  array("gameSystems","tobaccoProducts","alcoholProducts");
						if (!in_array($method,$relatedFuMethods)) {
							//there's a problem
							pr("did not get arg 2 or 3 (field or label) from followup in method $method: ");
						}
					}else{
						//shouldn't be a problem.. $method will get its args from the ini file
						$field = "pseudomethod";
						$label = "pseudomethod";
					}
				}else{
					$field = $followup[2];
					$label =is_array($followup[3])
					?$followup[3]["label"]
					: $followup[3];
				}
				$ehtml = "</dd>\n<dd class=\"followup\">";
//				pr("structure  to just call a method -- i.e. gameSystems? method is $method");
//				pr($field);
				switch($method) {
					case "radio":
//						pr("have to do extra stuff for radios .. full  followup  var: ");
//						pr($followup);
						$radioOptions = $followup[4];
						$ehtml .= $this->$method($field,$radioOptions,array("label"=>$label));
					break;
					case "checkbox":
						$cboxOptions = $followup[4];
//						pr("got ".sizeof($cboxOptions)." options for chkcbx");
//						pr($cboxOptions);
						foreach($cboxOptions  as $val=>$label) {
							$ehtml .= $this->checkbox($field,array("value"=>$val));
							$ehtml .= $label;
						}
					break;
					case "repeatable":
						$optVal = $followup[0];
						$rptMethod = isset($followup[3]["method"])
						?$followup[3]["method"]
						:"input";
						$field  = $followup[2];
						$opts = $followup[3];
						//repeatable sig is method,fieldname,opts
						$ehtml .= $this->$method($rptMethod,$field,$opts);
					break;
					default:
						if (isset($label)  && isset($field)) {
							if(is_array($followup[3])) {
								$inputOpts = $followup[3];
							}else{
								$inputOpts =array("label"=>$label);
							}
//							pr("calling method $method label  $label field $field ");
							$ehtml .= $this->$method($field,$inputOpts);
						}else{
							$ehtml .= $this->$method();
						}
					break;
				}
//				$ehtml .= "</dd>";
			}
		}
		return $ehtml;
	}
	/*
	 * plural method  -- followup arg is *not indexed numerically but by "Yes" "No" etc. ?
	*/
	function buildFollowups($attributes,$ehtml,$optValue) {
		if (!is_array($attributes["followup"]) && strlen($attributes["followup"])==1) {
			return  "";
		}
		$keys = array_keys($attributes["followup"]);
//		pr("buildFollowups got ".sizeof($keys)." keys: ");
//		pr($keys);
		// pr("full attributes[followup]:");
//		pr($attributes["followup"]);
		$thisHasFollowup=false;
		if (sizeof($keys) == 1) {
			$key = $keys[0];
			$firstArr  = $attributes["followup"][$key];
			if  (sizeof($firstArr)>1) {
				//					got multiple followups on one key.
				foreach($attributes["followup"][$keys[0]] as $thisFollowup)  {
					//multiple followups attached to a single trigger
					$ehtml  .= $this->buildFollowup($thisFollowup);
				}
			}else{
				//a single question attached to a single triggerVal
				//we just have to rewrite as e.g.
				pr("simple single  followup.. formMode  ".$this->formMode);
//				pr($attributes["followup"]);
				$fuArr  = $attributes["followup"][$keys[0]][0];
				$key = $keys[0];
				$tmp=array();
				$tmp[0]=$key;
				$tmp[1]= $fuArr[1];
				$tmp[2]= $fuArr[2];
				$fuOpts  = $fuArr[3];
				pr("'fuArr opts':");
				pr($fuOpts);
				
				
				/* handle custom things like "repeatable"  as  follUps
				 * from buildFollowup($followup):
				 * 				if a one-dim. array is passed, it's just of the form
				 // ([optionVal],[helperMethod],[followupFieldName],[label]
				  //TODO  optional fifth arg specifying full set of options/attributes
				   //   and/or allow scalar *or array for fourth arg, defaulting to "Label" only
				    */
				if($this->formMode=="reporting"  && isset($fuOpts["associationModel"])) {
					pr("reporting on kids.. ");
					//semi-THS  specific block.. only kids' genders  and ages here for now.. 
					$ehtml = $this->genderAgeSearch();
					echo "<hr/><textarea  rows=9 cols=80>$ehtml</textarea><hr/>";
					return  $ehtml;
				}else{
					if  (!isset($fuArr[3])) {
						pr("caught funky fuArr: ");
						pr($fuArr);
						$useAttr  = $fuArr[2];
						$tmp= array($key,$fuArr[0],$fuArr[1],$useAttr);
						//				pr("rewrote tmp and passing to bFoll().. ");
						//				pr($tmp);
					}else{
						$tmp[3]= $fuArr[3];
						$tmp[4]= isset($fuArr[4])?$fuArr[4]:"";
					}
				}
				$ehtml  .= $this->buildFollowup($tmp);
			}
		}else if (sizeof($keys)>1 ) {
//						if  (!isset($fuArr[2])) {
//						$meth = $fuArr[0];
//						pr("followup is an ini method? $meth");
//						$ehtml =  $this->$meth();
//						return $ehtml;
//					}
			$key = $keys[0];
			//					got multiple followups on one key.
//			foreach($attributes["followup"][$keys[0]] as $thisFollowup)  {
//				//multiple followups attached to a single trigger
//				pr("making FU out of thisFollowup:  ");
//				pr($thisFollowup);
//				$ehtml  .= $this->buildFollowup($thisFollowup);
//				pr("built:  ");
//				pr($ehtml);
//			}
			unset($attributes["followup"]);
		}else{
			//SOMEDAY: other radio options could have followups, just not for this project
			//					pr("attaching followups to multiple keys got nixed for THS.. ");
		}
		if ($key==$optValue) {
//			pr("got match on optValue $optValue .. attaching has-followup class");
//			pr("plural is returning ehtml: ");
//			pr($ehtml);

			$thisHasFollowup = true;
			if(!isset($attributes["class"])) {
				$attributes["class"]  = "has-followup";
			}else{
				$newclass  =$attributes["class"]." has-followup";
				$attributes["class"] = $newclass;
			}
		}else{
			if (isset($attributes["class"])) {
				$attributes["class"] = str_replace("has-followup","",$attributes["class"]);
			}else{
			}
//			pr("optValue $optValue did not match.. blanking ehtml");
			$ehtml = "";
		}
		return  array($ehtml,$thisHasFollowup,$attributes);
	}

//"Tree" pattern
	function tree($fieldName,$startingElements,$hasChildren=true,$showLabel=true,$dstyle="") {
		$selectId = $this->camelize($fieldName);
		$inputArg = $fieldName;
		if (isset($this->modelMap[$fieldName]) &&
			$Model = $this->modelMap[$fieldName]) {
			$inputArg = $Model. "." . $fieldName;
		}
		$class="";
		if ($hasChildren) {
			$class = "field tree";
		}
		if ($fieldName=="job_titles") {
			$myLabel = $this->formMode=="submitting"
			?"Search for your job title"
			:"Filter job titles list";
			$dbField = "job_title_id";		
		}else{
			$myLabel="Spouse's Job Title";
			$dbField = "spouse_job_title_id";
		}

		$html =<<<EOF
		<div {$dstyle} class="field tree-search">
			<label for="job-title-search">{$myLabel}</label>
			<input id="job-title-search" type="text" class="text tree-search" />
			<span class="description">Begin typing a job title</span>

			<input type="hidden" name="data[Participant][{$dbField}]" id="selected-job-title" value="" />
		</div>
EOF;
		$html .= '<div '.$dstyle.' class="' . $class . '">';
		if  ($showLabel)  {
			// The tree is blowing this up so it's not shown.  Maybe move to prev div.field w/ search input?
			$html .= '<label>' . Inflector::humanize(Inflector::singularize($fieldName)) . '</label>';
		}
//		$html .= '<select name="job_title" size="9">';
		$html .= '<ul>';
		foreach($startingElements as $arr) {
			if (!isset($arr["id"]) || !isset($arr["label"]))  {
//				pr("you might be using a suboptimal arr for a select tree.. ");
//				pr($arr);
			}
			$id = isset($arr["id"])
			?$arr["id"]
			:array_shift($arr);
			$labelVal =  isset($arr["label"])
			?$arr["label"]
			:array_shift($arr);
//			$opt  = "<option value='$id'>$labelVal</option>";
//			$html .= $opt;
			$html .= "<li><a href=\"#\"><ins> </ins>$labelVal</a></li>";
		}
		$html .=  "</ul>";
	//	pr("other ".$this->formMode);
		$html .= "</div>";
		if ($this->formMode=="submitting") {
			//additional input to specify 'Other' title
			$html .= "<div class=\"field otherJob\" style=\"display:none\">";
			$html .= $this->input("other_job_title",array(),true);
			$html .= "</div>";
			$html .="<hr/>";
		}
		return $html;
	}

/*
 *  Special but  common methods:
 */
	//toggler should (maybe) extend html helper instead, as it creates a link  w/image..
	function toggler($fieldName,$currentValue,$recordId) {
		$togglerId = $this->model().ucfirst(Inflector::camelize($fieldName));
////<a class="toggler" data-id="foo" data-field="bar" href="#">
//<img src="/img/icons/off.png" alt="" width="" height="" /></a>

		$src = "/img/icons/$currentValue.png";
		$alt = is_int($currentValue)
		?""
		:$currentValue;
		$html =<<<EOF
<a class="toggler" data-id="{$recordId}" data-field="{$fieldName}">
<img alt="{$currentValue}" src="{$src}" /></a>
EOF;
		return $html;
	}
	// age() for specifying a range, calculated from a date field, in reporting mode
	function age($fieldName="age",$question="calculated",$attributes=array()) {
//		$html = "Age: ";
		$html  = $this->minmax($fieldName,$question,$attributes);
		return $html;
	}

/*
 *  Editing hasMany, HABTM, etc.
 */
	function relatedText($fieldName,$Model) {
	}
	function relatedChecklist($fieldName,$Model,$question="",$optionSrc="ini") {
		$checkedIds = array();		
		if (is_scalar($fieldName)  && !is_null($Model)) {
			$fieldModel = ucfirst(Inflector::camelize($fieldName));
			foreach($Model[$fieldModel] as $i=>$RM) {
				$checkedIds[] = $RM["id"];
			}
		}else{
			//passed e.g. "VideoGameConsole",null, default fieldname is name
			$fieldModel=$fieldName;
			$fieldName="name";
		}
		$var  = "lst".$fieldModel;
		if (!is_null($optionSrc)) {
			//the ini variable index should  have been passed
			if (defined("QUESTIONS_INI")) {
				$questionsIni = ROOT . DS . APP_DIR . DS.QUESTIONS_INI;
				if (!file_exists(ROOT . DS . APP_DIR . DS. QUESTIONS_INI)) {
					trigger_error('QUESTIONS_INI file '.ROOT . DS . APP_DIR . DS.QUESTIONS_INI.' DNE', E_USER_WARNING);
				}
			}else{
				trigger_error("QUESTIONS_INI is not defined and unkown helper method '$method' was called", E_USER_WARNING);
			}
			$questionsConfig =parse_ini_file($questionsIni,true);
			$myOptsetting = explode(",", $questionsConfig["questionOptions"][$optionSrc]);
			$out= array();
			foreach($myOptsetting as $name) {
				$out[] = array("$fieldModel"=>array("name"=>"$name"));
			}
			$$var = $out;
//			pr($myOptsettig );
//			pr($questionsConfig["questionOptions"]);
//			exit;		
		}else{
			App::import('Model', $fieldModel);
			$M  = new  $fieldModel;
			$displayField = "name";
			$$var  = $M->findDistinct(null,array("id","$displayField"));
		}
		if (!$question || $this->formMode=="reporting") {
			$question = Inflector::pluralize(Inflector::humanize($fieldModel));
			$question = Inflector::pluralize(Inflector::humanize(Inflector::underscore($fieldModel)));
		}
//		pr($$var);
//		exit;
		$ret = "<dl class='fields'><dt>$question</dt>";
		$n =0;
		foreach($$var  as  $option) {
			$options["checked"]="";
			$opt = $option[$fieldModel]["name"];
			$camelOpt = Inflector::camelize($opt);
//			$inputId = $this->model().ucfirst(Inflector::camelize($fieldName))."_".Inflector::underscore($camelOpt);
$inputId = $this->model().ucfirst(Inflector::camelize(Inflector::pluralize($fieldModel)))."_".Inflector::underscore($camelOpt);
			$ret .= "<dd><label for=\"$inputId\">";
			$attrs  =  array("label"=>$opt);
$options["id"]=$inputId;
			if (isset($option[$fieldModel]["id"]) && in_array($option[$fieldModel]["id"],$checkedIds)) {
//				pr("option ".$option[$fieldModel]["name"]." should  e checked");
				$options["checked"]  = "checked";
			}
			if (isset($options['disabled']) && $options['disabled'] == true) {
				$hiddenOptions['disabled'] = 'disabled';
			}
			$output = "";
			$options["name"] = "data[$fieldModel][$n][$fieldName]";
			$n++;
			$options["value"]  = $option[$fieldModel]["name"];
			$ret .= $this->output($output . sprintf(
			$this->Html->tags['checkbox'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')));

			$ret .= $opt ."</label></dd>";
		}
		$ret .="</dl>";
		return  $ret;
	}

/*
 * THS-specific  methods
 */
 	function income($demographicRequired=false,$desc="") {
		$fieldName = "annual_income";
		$options = array(
			"label"=>"Total Household Income (Yearly)",
			"class"=>$demographicRequired." numeric");
		
		return $desc
		?$this->inputWithDescription($fieldName,$options,$desc)
		:$this->input($fieldName,$options);

	}
	function participationMonths() {
		$html = "Participation months:";
		$html .= $this->input("participationMonths",array("label"=>false),true);
		return  $html;
	}
	function geographySelector($fieldName,$parentName,$defaultSelected=null)  {
		switch($parentName) {
			case "North America":
			$regions  = array();
			$regions[] = array(
			"optVal"=>"CAN",
			"optLabel"=>"Canada"
			);
			$description = "Country";
			$divClass = "country";
			break;
			default :
			$regions  = AppController::getStateList();
			//the NA example above should only override
			// this for special occasions. By default, we look for
			$description = "State";
			$divClass = "state";
			break;
		}

		if ($this->formMode == "reporting")  {
			$new = array("Ignore"=>"Any");
			foreach($regions as $val=>$label)  {
				$new[$val] = $label;
			}
			$regions = $new;
		}

		//SOMEDAY: make the emptyLabel part match our custom select() methods expectations,
		// see if this can use that method instead of coded loop

	//	array_splice($regions,0,0,array("- Select -","- Select -"));
//		$html = $this->select($fieldName,$regions,null,null,"");
		$selectId  = $this->model().ucfirst(Inflector::camelize($fieldName));
//		$selectName = "data[".$this->model()."][".ucfirst(Inflector::camelize($fieldName))."]";
		$selectName = "data[".$this->model()."][$fieldName]";
		$html ="<div class=\"$divClass field\">";
		$html .="<select id=\"$selectId\" name=\"$selectName\">";
		foreach($regions as $abbrev=>$name)  {
			$optval = $name=="Any" || $name=="Either"
			?"Ignore"
			:$name;
			$selected=$name==$defaultSelected
			?"selected=\"selected\""
			:"";
			$html .= "<option $selected value=\"$optval\">$name</option>";
		}
		$html .= '</select>';
		$html .= "<span class=\"description\">$description</span>";
		$html .= '</div>';
		return $html;
	}
	function jobTitleTree($prefix="") {
		//these should  go into the .field_tree class processing in global.js
		// i.e. derive the Model name and ajax fetch the ones w/parent_id=0

		App::import('Model', "JobTitle");
		$M  = new JobTitle;
		$res = $M->find("all",
		array("conditions"=>array("parent_id=0"),"recursive"=>-1,"order"=>"title")
		);
		foreach($res as $M) {
			$startingJobTitles[] = array(
			"id"=>$M["JobTitle"]["id"],
			"label"=>$M["JobTitle"]["title"]
			);
		}
//		($fieldName,$startingElements,$hasChildren=true,$showLabel=true,$dstyle=""

		if ($this->formMode=="reporting") {
			$html =$this->tree($prefix."job_titles",$startingJobTitles,true,true,"style=\"display:none\"");
			$options["rows"]=3;
			$options["cols"]=40;
			$options["label"] = "Job Titles";
			$options["description"] = "Enter one job title per line, or "
			.'<a class="revealTree" href="#">use the job title tree</a>';
			$html  .= $this->textarea($prefix."jobtitles",$options);
		}else{
			$html =$this->tree($prefix."job_titles",$startingJobTitles);
		} 

		return $html;
	}
	function participationExclusions() {
		$html = "";
		$html .= "<p class=\"no-margin\"><strong>Exclude study participants from the past number of months</strong></p>";
		$html .=$this->input("par_months",array("label"=>false,"name"=>"data[par_months]"));
		$html .=$this->input("keyword",array("label"=>"Keywords","name"=>"data[keyword]"));
		$html .= $this->jobNumberSearchType();
		$html .=$this->textarea("job_number_list",array(
			"name"=>"data[job_number_list]",
			"description"=>"Values must be comma-separated"));

		return  $html;
	}
	function participantType() {
		$html = "";
		$html .=$this->radio("participant_type",
		array("personal"=>"Personal","professional"=>"Professional"),
		array("label"=>"Application Type"));

		return  $html;
	}
	function jobNumberSearchType() {
		$html = "";
		$opts =array("Exclude"=>"Exclude the following job numbers","Include"=>"Only include the following job numbers");
		$html .= $this->radio("job_number_search_type",$opts,array("name"=>"data[job_number_search_type]","label"=>"Job Number Filter","class"=>"no-margin"));
		return $html;
	}

	function tobaccoProducts() {
		$html = $this->relatedChecklist("TobaccoProduct",null,"Which ones?","tobaccoProductOpts");
		return  $html;
	}
	function alcoholProducts() {
		$html = $this->relatedChecklist("AlcoholProduct",null,"Which of these do you drink?","alcoholProductOpts");
		return  $html;
	}
	function gameSystems() {
//		pr("gameSystems() method  instead of ini file  thing");
		$html = $this->relatedChecklist("VideoGameConsole",null,"What game systems do you use?","gameSystemsOpts");
		return  $html;
	}
	function healthConditions() {
		$html = $this->relatedChecklist("HealthCondition",
		null,"Do you have any of the following health conditions?","healthConditionsOpts");
		return  $html;
	}
	function genderAgeSearch($fieldName="",$question="Age and Gender",$attributes=array()) {
		$html="";
//		$html .= $this->yesno("kids","Kids");
		$html .= $this->childGender("Child.0.gender",null,array(
	"associationModel"=>"Child",
	"label"=>"Child's Gender"
	));
		$html .= $this->age("age",null,array(
	"associationModel"=>"Child","class"=>"numeric"
		));
		return $html;
	}
	function spouseInfo() {
		if ($this->formMode!="submitting") return "";
		 
		$html = "<div id=\"spouseInfo\" style=\"display:none\">";
		$html .= $this->input("spouse_name",
			array("name"=>"data[Participant][spouse_name]")
			);
		$html .= $this->dob("spouse_dob",
			array("name"=>"data[Participant][spouse_dob]")
			);
		$html .= $this->spouseRace();
		$html .= $this->input("spouse_business_name");
//		$html .= $this->jobTitleTree("spouse_");
		$html .= "</div>";
		return $html;
	}
/*
 * Graveyard
 */
	//ajaxButton functionality should  be  built into our regular  overloaded button()
	function ajaxButton($label,$id,$function="") {
		$ajaxClass  = "ajax".$label;
		$html = "<button id=\"$id\" class=\"".strtolower($label)." ".$ajaxClass."\">";
		$html .=  "<img src=\"/img/icons/".strtolower($label).".png\" alt=\"$label\" /> $label";
		$html .= "</button>";
		return $html;
	}

	//exists  in Inflector, no?
	function camelize($str) {
		$words = explode("_",$str);
		$w=0;
		$camel = "";
		foreach ($words as $word) {
			$camel .= $w ? ucwords($word) : $word;
			$w++;
		}
		return $camel;
	}
	function modifiedCakeInput($fieldName, $options = array()) {
 	//copied from cake core  -- we want to append something to the label when 'required' field:
		$view =& ClassRegistry::getObject('view');
		$this->setEntity($fieldName);
		$entity = join('.', $view->entity());

		$defaults = array('before' => null, 'between' => null, 'after' => null);
		$options = array_merge($defaults, $options);

		if (!isset($options['type'])) {
			$options['type'] = 'text';

			if (isset($options['options'])) {
				$options['type'] = 'select';
			} elseif (in_array($this->field(), array('psword', 'passwd', 'password'))) {
				$options['type'] = 'password';
			} elseif (isset($this->fieldset['fields'][$entity])) {
				$fieldDef = $this->fieldset['fields'][$entity];
				$type = $fieldDef['type'];
				$primaryKey = $this->fieldset['key'];
			} elseif (ClassRegistry::isKeySet($this->model())) {
				$model =& ClassRegistry::getObject($this->model());
				$type = $model->getColumnType($this->field());
				$fieldDef = $model->schema();

				if (isset($fieldDef[$this->field()])) {
					$fieldDef = $fieldDef[$this->field()];
				} else {
					$fieldDef = array();
				}
				$primaryKey = $model->primaryKey;
			}

			if (isset($type)) {
				$map = array(
					'string'  => 'text',     'datetime'  => 'datetime',
					'boolean' => 'checkbox', 'timestamp' => 'datetime',
					'text'    => 'textarea', 'time'      => 'time',
					'date'    => 'date',     'float'     => 'text'
				);

				if (isset($this->map[$type])) {
					$options['type'] = $this->map[$type];
				} elseif (isset($map[$type])) {
					$options['type'] = $map[$type];
				}
				if ($this->field() == $primaryKey) {
					$options['type'] = 'hidden';
				}
			}

			if ($this->model() === $this->field()) {
				$options['type'] = 'select';
				if (!isset($options['multiple'])) {
					$options['multiple'] = 'multiple';
				}
			}
		}
		$types = array('text', 'checkbox', 'radio', 'select');

		if (!isset($options['options']) && in_array($options['type'], $types)) {
			$view =& ClassRegistry::getObject('view');
			$varName = Inflector::variable(
				Inflector::pluralize(preg_replace('/_id$/', '', $this->field()))
			);
			$varOptions = $view->getVar($varName);
			if (is_array($varOptions)) {
				if ($options['type'] !== 'radio') {
					$options['type'] = 'select';
				}
				$options['options'] = $varOptions;
			}
		}

		$autoLength = (!array_key_exists('maxlength', $options) && isset($fieldDef['length']));
		if ($autoLength && $options['type'] == 'text') {
			$options['maxlength'] = $fieldDef['length'];
		}
		if ($autoLength && $fieldDef['type'] == 'float') {
			$options['maxlength'] = array_sum(explode(',', $fieldDef['length']))+1;
		}

		$out = '';
		$div = true;
		$divOptions = array();

		if (array_key_exists('div', $options)) {
			$div = $options['div'];
			unset($options['div']);
		}

		if (!empty($div)) {
			$divOptions['class'] = 'field input';
			$divOptions = $this->addClass($divOptions, $options['type']);
			if (is_string($div)) {
				$divOptions['class'] = $div;
			} elseif (is_array($div)) {
				$divOptions = array_merge($divOptions, $div);
			}
			if (in_array($this->field(), $this->fieldset['validates'])) {
				$divOptions = $this->addClass($divOptions, 'required');
			}
			if (!isset($divOptions['tag'])) {
				$divOptions['tag'] = 'div';
			}
		}

		$label = null;
		if (isset($options['label']) && $options['type'] !== 'radio') {
			$label = $options['label'];
			unset($options['label']);
		}

		if ($options['type'] === 'radio') {
			$label = false;
			if (isset($options['options'])) {
				if (is_array($options['options'])) {
					$radioOptions = $options['options'];
				} else {
					$radioOptions = array($options['options']);
				}
				unset($options['options']);
			}
		}

		if ($label !== false) {
			$labelAttributes = $this->domId(array(), 'for');
			if (in_array($options['type'], array('date', 'datetime'))) {
				$labelAttributes['for'] .= 'Month';
			} else if ($options['type'] === 'time') {
				$labelAttributes['for'] .= 'Hour';
			}

			if (is_array($label)) {
				$labelText = null;
				if (isset($label['text'])) {
					$labelText = $label['text'];
					unset($label['text']);
				}
				$labelAttributes = array_merge($labelAttributes, $label);
			} else {
				$labelText = $label;
			}

			if (isset($options['id'])) {
				$labelAttributes = array_merge($labelAttributes, array('for' => $options['id']));
			}
			$out = $this->label($fieldName, $labelText, $labelAttributes);
			if  (strstr($options["class"],"required"))  {
				$out = str_replace("</label>"," <span class=\"required\">*</span></label>",$out);
			}
		}

		$error = null;
		if (isset($options['error'])) {
			$error = $options['error'];
			unset($options['error']);
		}

		$selected = null;
		if (array_key_exists('selected', $options)) {
			$selected = $options['selected'];
			unset($options['selected']);
		}
		if (isset($options['rows']) || isset($options['cols'])) {
			$options['type'] = 'textarea';
		}

		$empty = false;
		if (isset($options['empty'])) {
			$empty = $options['empty'];
			unset($options['empty']);
		}

		$timeFormat = 12;
		if (isset($options['timeFormat'])) {
			$timeFormat = $options['timeFormat'];
			unset($options['timeFormat']);
		}

		$dateFormat = 'MDY';
		if (isset($options['dateFormat'])) {
			$dateFormat = $options['dateFormat'];
			unset($options['dateFormat']);
		}

		$type	 = $options['type'];
		$before	 = $options['before'];
		$between = $options['between'];
		$after	 = $options['after'];
		unset($options['type'], $options['before'], $options['between'], $options['after']);

		switch ($type) {
			case 'hidden':
				$out = $this->hidden($fieldName, $options);
				unset($divOptions);
			break;
			case 'checkbox':
				$out = $before . $this->checkbox($fieldName, $options) . $between . $out;
			break;
			case 'radio':
				$out = $before . $out . $this->radio($fieldName, $radioOptions, $options) . $between;
			break;
			case 'text':
			case 'password':
				$out = $before . $out . $between . $this->{$type}($fieldName, $options);
			break;
			case 'file':
				$out = $before . $out . $between . $this->file($fieldName, $options);
			break;
			case 'select':
				$options = array_merge(array('options' => array()), $options);
				$list = $options['options'];
				unset($options['options']);
				$out = $before . $out . $between . $this->select(
					$fieldName, $list, $selected, $options, $empty
				);
			break;
			case 'time':
				$out = $before . $out . $between . $this->dateTime(
					$fieldName, null, $timeFormat, $selected, $options, $empty
				);
			break;
			case 'date':
				$out = $before . $out . $between . $this->dateTime(
					$fieldName, $dateFormat, null, $selected, $options, $empty
				);
			break;
			case 'datetime':
				$out = $before . $out . $between . $this->dateTime(
					$fieldName, $dateFormat, $timeFormat, $selected, $options, $empty
				);
			break;
			case 'textarea':
			default:
				$out = $before . $out . $between . $this->textarea($fieldName, array_merge(
					array('cols' => '30', 'rows' => '6'), $options
				));
			break;
		}

		if ($type != 'hidden') {
			$out .= $after;
			if ($error !== false) {
				$errMsg = $this->error($fieldName, $error);
				if ($errMsg) {
					$out .= $errMsg;
					$divOptions = $this->addClass($divOptions, 'error');
				}
			}
		}
		if (isset($divOptions) && isset($divOptions['tag'])) {
			$tag = $divOptions['tag'];
			unset($divOptions['tag']);
			$out = $this->Html->tag($tag, $out, $divOptions);
		}
		return $out;
	}
 	function initModelMap($modelMap)  {
		$this->modelMap = $modelMap;
	}

}
/*
 * //			if ($label) {
//				$optTitle =  sprintf($this->Html->tags['label'], $tagName, null, $optTitle);
//			}
//			$myout  = sprintf(
//				$this->Html->tags['radio'], $attributes['name'],
//				$tagName, $parsedOptions, $optTitle
//			);
//

*/
?>