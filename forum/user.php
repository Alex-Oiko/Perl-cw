<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* Above page-specific */
$document->header("User");

/* ================= Page Specific ====================== */

//Include user functions
require_once("./functions/core/user.php");

//Handle validation and input:


//Save profile
if (isset($_POST['user_id'])) {
	//Check for missing data
	if (!isset($_POST['user_id']) || !isset($_POST['user_email']) || !isset($_POST['user_homepage']) || !isset($_POST['user_bio'])) {
		fatal_user_error("Please fill in all the required form components","Incomplete data submitted");
	}

	//Setup user
	$id = make_safe("int",$_POST['user_id']);
	$thisuser = new User($core);
	if (!$thisuser->load_from_userid($id,false)) { 	fatal_user_error("Invalid user","Unable to edit invalid user"); }
	
	//Validate email and homepage
	if (!validate("email",$_POST['user_email'])) { 
		fatal_user_error("Invalid email address","Please ensure you have provided a valid email address"); }
	
	//Set user homepage
	$update['user_homepage'] = $_POST['user_homepage'];	

	//Set user type
	if (isset($_POST['user_type'])) { $update['user_type'] = $_POST['user_type']; }

	//Set uesr email
	$update['user_email'] = make_safe("text",$_POST['user_email']);

	//Set user bio
	$update['user_bio'] = make_safe("text",$_POST['user_bio']);

	//Image upload
	$update['user_picture'] = "";

	//New picture URL
	if (isset($_POST['user_imageurl']) && $_POST['user_imageurl'] != "") { 
		if (!validate("url",$_POST['user_imageurl'])) {
			fatal_user_error("Invalid image URL specified","Please go back and try again");
		}
		$update['user_picture'] = make_safe("url",$_POST['user_imageurl']);

	//New picture upload
	} else if (isset($_FILES['user_imagefile']['name']) && $_FILES['user_imagefile']['name'] != "") { 
		$update['user_picture'] = $core->do_upload('user_imagefile'); 

	//No picture change
	} else { unset($update['user_picture']); }

	//Validate password
	if (isset($_POST['user_password1']) && isset($_POST['user_password2'])) {
		if ($_POST['user_password1'] != "" && $_POST['user_password2'] != "") {
			//Check for matching first and second confirm passwords
			if ($_POST['user_password1'] != $_POST['user_password2']) {
				fatal_user_error("Non-matching passwords","The password and the confirmation do not match");
			}

			//Compare passwords and prepare new password
			$current_password = $thisuser->get("user_password");
			$current_password2 = make_safe("text",$_POST['user_passwordcurrent']);
			$new_password = make_safe("text",$_POST['user_password1']);
			
			//Check for valid password or be an admin			
			if ($current_password == $current_password2 || $user->is_admin()) {
				$update['user_password'] = $new_password;
			} else {
				fatal_user_error("Invalid password","The current password you entered was incorrect");	
			}
		}
	}

	//Update the user
	$updateduser = update_profile($id,$update);
	show_profile($updateduser);	
}

//Admin edit any profile
else if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
	//Check if admin
	if (!$user->is_admin()) { fatal_user_error("Access denied","You do not have permission to edit this profile"); }

	//Validate input
	$id = make_safe("int",$_GET['id']);
	if (!is_numeric($id)) { fatal_user_error("Invalid user ID specified","Please try again"); }

	//Check if user exists
	$profile = new User($core);
	if (!$profile->load_from_userid($id)) { fatal_user_error("No such user","No user exists with the ID specified"); }	

	//If user exists, edit profile
	edit_profile($profile);
}

//View profile
else if (isset($_GET['id'])) {
	//Validate input
	$id = make_safe("int",$_GET['id']);
	if (!is_numeric($id)) { fatal_user_error("Invalid user ID specified","Please try again"); }
	
	//Check if user exists
	$profile = new User($core);
	if (!$profile->load_from_userid($id)) { fatal_user_error("No such user","No user exists with the ID specified"); }

	//If user exists, display profile
	show_profile($profile);
} 

//Edit current profile
else {
	edit_profile($user);
}

/* End page-specific */
$document->footer();
$document->output();


?>
