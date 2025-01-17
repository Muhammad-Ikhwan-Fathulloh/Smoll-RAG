<?php

/*
 * class for handling errors
 *
 * @category	Libraries
 * @author		hygsan
 * @copyright	2015
 * @version		1.0 (27.05.2015)
 * 
 */

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

class Error {
	
	public function showErrorMsg(array $errors) {
		if (count($errors) < 1) {
			echo $errors[0];
		} else {			
			$error = implode('<br />', $errors);
			
			echo $error;
		}
	}
}