<?php
function __autoload($class_name){
	$dir = array('fw/','app/','app/controller/','app/model/');
	foreach($dir as $d){
		if(file_exists($d . $class_name . '.class.php')){
			require_once $d . $class_name . '.class.php';			
		}		
	}    
}
?>