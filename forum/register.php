<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* Above page-specific */
$document->header("Register");

/* ================= Page Specific ====================== */
//Include login functions
require_once('./functions/core/register.php');

//If registering, do that
$success = false;
if (isset($_POST['user_name']) && isset($_POST['user_password']) && isset($_POST['user_password2']) && isset($_POST['user_email'])) {	
	if (strlen($_POST['user_name']) == 0 || 
		strlen($_POST['user_password']) == 0 || 
		strlen($_POST['user_email']) == 0) {
		fatal_user_error("Some of the registration information was not filled in correctly",
		"Please <a href=\"/register.php\">try again</a>");
	}
	if ($_POST['user_password'] != $_POST['user_password2']) { fatal_user_error("Your two passwords did not match",
		"Please <a href=\"/register.php\">try again</a>");  }
	$success = do_register(
		$_POST['user_name'],
		$_POST['user_password'],
		$_POST['user_email']
		);	
}
//Otherwise display login form
else { register_form(); }

if ($success) { register_over(); }

/* End page-specific */
$document->footer();
$document->output();

?>
