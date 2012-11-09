<?php

/* Display the contact form */
function contact_form() {
	global $document, $user, $core;

	$document->append_template("simple_template",array('title'=>"Contact", 'text'=>"Please fill in the form below to contact us"));
	$window['title'] = "Contact us";
	$window['content'] = "";

	//Need to be a registered user
	if (!$user->is_registered()) { 
		fatal_user_error("You need to be a registered user to use the contact form",
		'Please <a href="$siteurl/register.php">register</a> or <a href="$siteurl/login.php">login</a>');
	}

	//Create form
	$form = $document->make_form("contact","contact","/contact.php","post",false);
	$form->start_fieldset("sendmessage","Send Message");
	#$form->add_element_only("to","To","hidden",$core->get('contact_email'));
	#$form->add_element("from","From","hidden_text",$user->get('user_email'));
	$form->add_element("subject","Subject","text","","Subject of message");
	$form->add_element("message","Message","textarea","","Your message to send");
	$form->append('<div class="center">');
	$form->add_element_only("submit","Send","submit","Send");
	$form->append('</div>');
	$form->end_fieldset();
	$window['content'] .= $form->output();

	$document->append_template("window",$window);
}

/* Send a contact message */
function send_message($subject,$message,$to2,$from2) {
	global $document, $user, $core;

	//Need to be a registered user
	if (!$user->is_registered()) { 
		fatal_user_error("You need to be a registered user to use the contact form",
		'Please <a href="$siteurl/register.php">register</a> or <a href="$siteurl/login.php">login</a>');
	}

	//Set up email information
	$subject = "Contact form: $subject";
	$email = $from2;
	$to = $to2;
	$headers = "From: $email";

	//Send email - this has been disabled for obvious reasons
	//mail($to, $subject, $message, $headers);

}

?>
