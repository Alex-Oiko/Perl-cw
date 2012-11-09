<?php

//Enable all error reporting
error_reporting(-1);

//Initialise core
require_once('./include/init.php');

//Include security
require_once('./functions/security/security.php');

//Useful variables
$document = $core->document;
$user = $core->user;
$db = $core->db;
$session = $core->session;

//Handle global functions
if (isset($_GET['user_style'])) { 
	$style = $_GET['user_style'];
	//Check for valid style
	$valid_styles = explode(",",$core->get('valid_styles'));
	if (in_array($style,$valid_styles)) { 
		$session->set("style",$style); $user->set("user_style",$style); 
		$document->append_template("window",array('title'=>"Style change",
			'content'=>"Your style has been succesfully changed.<br/>
			The change will take affect after your next page load."));
	}	
	//Invalid style
	else { fatal_user_error("Invalid style selected","Please select a different style"); }
}

?>
