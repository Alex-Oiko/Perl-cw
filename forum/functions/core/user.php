<?php

/* Display a form to edit a profile of a given user */
function edit_profile($edituser) {
	global $core, $document,$user;
	$siteurl = $core->config['Paths']['web'];

	//Anti-guest check
	if ($edituser->get("user_id") == -1) {
		fatal_user_error("You must be logged in to edit your profile",'Please <a href="$siteurl/login.php">login</a> or <a href="' . $siteurl . '/register.php">register</a>');
	}

	//Admin check
	if ($edituser->get('user_id') != $user->get('user_id') && !$user->is_admin()) {
		fatal_user_error("Access denied","You do not have permission to edit this profile");
	}

	//Build form
	$form = $document->make_form("profile","profile","/user.php","post",true);
	$form->start_fieldset("editprofile",$edituser->get('user_name'));
	$form->add_element_only("user_id","ID","hidden",$edituser->get('user_id'));
	$form->add_element("user_email","Email","text",$edituser->get_default('user_email',""),"Please enter your email address");
	$form->add_element("user_homepage","Homepage","text",$edituser->get_default('user_homepage',""),"Please enter your homepage");
	$form->add_element("user_bio","About Me","textarea",$edituser->get_default('user_bio',""),"Please enter some information about yourself");
	$form->end_fieldset();

	//User type
	$isuser = ($edituser->get_default("user_type",1) == 1) ? "selected=selected" : "";
	$isadmin = ($edituser->get_default("user_type",1) == 2) ? "selected=selected" : "";
	if ($user->get('user_type') > 1) {
		$form->start_fieldset("edittype","User Type");
		$form->add_element("user_type","User Type","list",
			"<option value=\"1\" $isuser>Member</option>
			 <option value=\"2\" $isadmin>Administrator</option>"
			,"Type of user");
		$form->end_fieldset();
	}
	
	//Password
	$form->start_fieldset("editpassword","Change Password");
	$form->append('<small>If you wish to keep your current password, leave these fields blank</small>');
	if (!$user->is_admin()) { 
		$form->add_element("user_passwordcurrent","Current Password","password","","Please enter your current password"); 
	}
	else {
		$form->add_element_only("user_passwordcurrent","Current Password","hidden");
	}
	$form->add_element("user_password1","New Password","password","","Please enter your new password");
	$form->add_element("user_password2","Confirm New Password","password","","Please confirm your new password");
	$form->end_fieldset();

	//Picture
	$form->start_fieldset("editpicture","Change Avatar Image");
	$form->add_element("user_imageurl","Avatar Image URL","text","","If your user image is already online, enter the URL here");
	$form->add_element("user_imagefile","Avatar Image Upload","upload","","If your user image is not online, upload it here");
	$form->end_fieldset();

	//Submit
	$form->append('<div class="center">');
	$form->add_element_only("submit","Submit","submit","Submit");
	$form->append('</div>');
	$form->end_fieldset();
	$formhtml = $form->output();

	$document->append_template("simple_template",array('title'=>"Edit Settings", 'text'=>"Please fill in the form below to edit your profile"));
	$document->append_template("window",array('title'=>"Edit Settings", 'content'=>$formhtml));

}

/* Display the profile of the given user */
function show_profile($profile) {
	global $core, $document, $user;
	$siteurl = $core->config['Paths']['web'];

	//Sanity check
	if (!is_object($profile)) { fatal_error("Invalid user object passed to show profile"); }	

	//Get info object
	$userinfo =& $profile->info;
	//For safety
	$userinfo['user_password'] = ""; $userinfo['user_cookie'] = "";

	//Fill in blanks
	if (!isset($userinfo['user_homepage']) || $userinfo['user_homepage'] == "") { 
		$userinfo['user_homepage'] = "<i>None</i>"; }
	else {
		$userinfo['user_homepage'] = '<a href="' . $userinfo['user_homepage'] . '">Homepage</a>';
	}
	if (!isset($userinfo['user_bio']) || $userinfo['user_bio'] == "") { 
		$userinfo['user_bio'] = "<i>Not available</i>"; }
	else {
		$userinfo['user_bio'] = $core->make_formatted("plaintext",$userinfo['user_bio']);
	}
	if (!isset($userinfo['user_picture']) || $userinfo['user_picture'] == "") { 
		$site_url = $core->config['Paths']['web'];
		$style = $user->get("user_style");
		$userinfo['user_picture'] = "$site_url/resources/images/$style/nopicture.png"; }

	//Build breadcrumb
	$userinfo['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
	$userinfo['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / ';
	$userinfo['breadcrumb'] .= '<a href="$siteurl/user.php?id=' . $userinfo['user_id'] . '" title="Home">' . $userinfo['user_name'] . '</a>';

	//Push to user template
	$document->append_template("title",array('title' => "Profile"));
	$document->append_template("user_profile",$userinfo);

	//Administrative options
	if ($user->is_admin()) {

		$admin = '<br/><br/><small class="windowbg">Admin: ' .
				'<a href="' . $siteurl . '/user.php?action=edit&amp;uid=' . $userinfo['user_id'] . '">Edit User</a></small>';
		$document->append($admin);	
	}
}

/* Update the user profile with the given options */
function update_profile($userid,$options) {
	global $core, $user;
	$siteurl = $core->config['Paths']['web'];

	$edituser = new User($core);
	//Check for valid user
	if (!$edituser->load_from_userid($userid,false)) {
		fatal_user_error("Invalid user to edit","Unable to edit the user with the ID specified");
	}

	//Anti-guest check
	if ($edituser->get("user_id") == -1) {
		fatal_user_error("You must be logged in to edit your profile",'Please <a href="' . $siteurl . '/login.php">login</a> or <a href="' . $siteurl . '/register.php">register</a>');
	}

	$edituser->set_all($options);
	return $edituser;
}



?>
