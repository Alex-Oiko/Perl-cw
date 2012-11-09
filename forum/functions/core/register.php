<?php

/* Build a pretty login form */
function make_register_form() {
	global $core, $document;
	
	$form = $document->make_form("register","register","/register.php","post");
	$form->start_fieldset("details","Create a New Account");
	$form->append("<p>Please complete the details below to register for an account..</p>");
	$form->add_element("user_name","User Name","text","","The username you would like",'style="width: 200px;"');
	$form->add_element("user_password","Password","password","","The password you would like",'style="width: 200px;"');
	$form->add_element("user_password2","Confirm Password","password","","Please confirm your password",'style="width: 200px;"');
	$form->add_element("user_email","Email","text","","Your email address",'style="width: 200px;"');
	$form->append('<div class="center">');
	$form->add_element_only("submit","Register","submit","Register","Register");
	$form->append('</div>');
	$form->end_fieldset();
	return $form->output();
}

/* Attempt to perform a login */
function do_register($username,$password_hash,$email) {
	global $core;	
	
	//Make data safe
	if (!validate("email",$email)) { fatal_user_error("Invalid email address supplied","Please ensure you entered it correctly"); }
	$username = make_safe("text",$username);
	$email = make_safe("text",$email);

	//Check for sane length
	if (strlen($username) > 20 || strlen($username) < 3) { fatal_user_error("The username you have entered is invalid","Usernames must be between 3 and 20 characters in length"); }

	//Check for sane characters in username
	if (!validate("alphanumeric",$username)) { fatal_user_error("The username you have entered is invalid","Usernames can only consist of simple letters and numbers with no other characters or spaces"); }

	//Email address validation
	if (!validate("email",$email)) {
		fatal_user_error("The email address that you have entered is invalid","Please <a href=\"\$siteurl/register.php\">try again</a> with a valid email address.");
	}

	//Check if user already exists
	if ($core->user->exists($username)) {
		fatal_user_error("That username has already been registered","Please <a href=\"\$siteurl/register.php\">try again</a> with a different username.");
	}
	//Check for number of characters in password
	if(strlen($password_hash)<8){
		fatal_user_error("Your password is too short. It must be more than 8 characters");
	}
	
	//Otherwise add user to database
	$query = $core->db->make_query("user","INSERT");
	$query->add_data("user_name",$username);
	$query->add_data("user_email",$email);
	$query->add_data("user_password",$password_hash);
	$query->add_data("user_type",1);
	$query->add_data("user_ip",$_SERVER['REMOTE_ADDR']);
	$query->execute();

	//Registration successful
	return true;
}

/* Helper functions */
function register_form() {
	global $document;
	$page['title'] = "Register";
	$page['text'] = "If you would like to register for my website, please enter the requested details below:<br/><br/>Registration is fast, free and simple!";
	$document->append_template("simple_template",$page);

	$window['title'] = "Register";
	$window['content'] = make_register_form();
	$document->append_template("window",$window);
}

function register_over() {
	global $document;
	$page['title'] = "Register";
	$document->append_template("title",$page);
	$window['title'] = "Registration Complete";
	$window['content'] = "You are now registered and can <a href=\"\$siteurl/login.php\">log in</a>.";
	$document->append_template("window",$window);
}

?>
