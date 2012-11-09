<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* Above page-specific */
//$document->header("Login"); Not for login page

/* ================= Page Specific ====================== */
//Include login functions
require_once('./functions/core/login.php');

//Attempt to log in if login information sent
if (isset($_POST['user_name']) && isset($_POST['user_password'])) {
	do_login($_POST['user_name'],$_POST['user_password']);
	$core->do_redirect("index.php");
}

//Log out
elseif (isset($_GET['action']) && $_GET['action'] == "logout") {
	force_logout($core->session->get('token'));
	$core->do_redirect("index.php");
}
//Display login form
else { 
	$document->header("Login"); 
	login_form(); 
}

/* End page-specific */
$document->footer();
$document->output()

?>
