<?php

//Ideally, put any security related functions in this file!

/* Perform validation on text given a type */
function validate($type,$text) {
		switch ($type) {
			//Validation of plain numbers
			case "int":
				return is_numeric($text);
				break;
			//Validation of letters and numbers only
			case "alphanumeric":
				return preg_match("/^[a-zA-Z0-9]*$/",$text);
				break;
			//Validation of emails
			case "email":
				return filter_var($text, FILTER_VALIDATE_EMAIL);
				break;
			//Validation of plain URLs
			case "url":
				return filter_var($text, FILTER_VALIDATE_URL);
				break;
			//Invalid validation
			default:
				fatal_error("Invalid validation type: $type");
		}
	}

/* Take user input and make it safe based on its type */
function make_safe($type,$text) {

	//We should really get around to make some kind of function to make input safe
	//But we'll never get around to it
	if($type = "int" && is_numeric($text)){
		return $text;
	}

	else if($type="text"){
		$text = mysql_real_escape_string($text);
		return $text;
	}
	else fatal_user_error("Something went wrong wiht your input :/");


}

?>
