<?php
/**
 * Autocomplete Helper
 *
 * @author  Nik Chankov
 * @website http://nik.chankov.net
 * @version 1.0.0
 *
 * @updated   2008-02-13
 * @author    Abdullah
 * @website   http://abdullahsolutions.com
 * @changes   Used helpers array. Also used beforeRender so that the javascripts and theme is automatically loaded
 *
 * @updated   2008-02-17
 * @author    Abdullah
 * @website   http://abdullahsolutions.com
 * @changes   Used classregistry getobject to get a view and addscript function of view to set javascript load in header
 */

class DatePickerHelper extends FormHelper {

    var $format = '%Y-%m-%d';
    var $helpers = array('Javascript','Html');

    /**
     *Setup the format if exist in Configure class
     */
    function _setup(){
        $format = Configure::read('DatePicker.format');
        if($format != null){
            $this->format = $format;
        }
        else{
            $this->format = '%m-%d-%Y';
        }
    }

    function beforeRender(){
        $view = ClassRegistry::getObject('view');
        
        $view->addScript($this->Javascript->link('jscalendar/calendar.js'));
        $view->addScript($this->Javascript->link('jscalendar/lang/calendar-en.js'));
        $view->addScript($this->Javascript->link('common.js'));
        $view->addScript($this->Html->css('../js/jscalendar/skins/aqua/theme'));
    }

    /**
     * The Main Function - picker
     *
     * @param string $field Name of the database field. Possible usage with Model.
     * @param array $options Optional Array. Options are the same as in the usual text input field.
     */  
    function timepicker($fieldName, $ind,$suffix="date") {
        $output ="<select name='data[Registration][$ind][{$fieldName}_$suffix]'>";
        switch($suffix) {
	        case "date":
        	$range = range(25,30);
        	break;
        	case "time":
        	$range = range(1,12);
        	break;
        	case "ampm":
        	$range = array("AM","PM");
        	break;
    	}
        foreach($range as $day) {
        	 if ($fieldName =="departure") {
        	 	$output .= "<option value='$day'>Sep $day</option>";
        	 }else{
	        	$output .= "<option value='$day'>$day</option>";
        	 }
        }
        if ($fieldName =="departure") {
        		$output .= "<option value='Oct 1'>Oct 1</option>";
        }
        $output .= "</select>";
//        $output .= "<select name='data[Registration][$ind][{$fieldName}_ampm]'>";
//        $l = array("AM","PM");
//        foreach($l  as $ampm) {
//        	$output .= "<option value='$ampm'>$ampm</option>";
//        }
//        $output .= "</select>";
        return $output;
    }

    function flat($fieldName, $options = array()){
        $this->_setup();
        $this->setFormTag($fieldName);
        $htmlAttributes = $this->domId($options);      
        $divOptions['class'] = 'date';
        $options['type'] = 'hidden';
        $options['div']['class'] = 'date';
        $hoder = '
';
        $output = $this->input($fieldName, $options).$hoder;

        return $output;
    }
}
?>