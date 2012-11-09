<?php

/* Build a pretty login form */
function make_login_form() {
	global $core, $document;
	
	$form = $document->make_form("login","login","/login.php","post");
	$form->start_fieldset("details","Login Information");
	$form->append("<p>Please type your username and password in the fields below to log in.</p>");
	$form->add_element("user_name","User Name","text","","Your username",'style="width: 200px;"');
	$form->add_element("user_password","Password","password","","Your password",'style="width: 200px;"');
	$form->append('<div class="center">');
	$form->add_element_only("submit","Log in","submit","Log in","Log in");
	$form->append('</div>');
	$form->end_fieldset();
	return $form->output();
}

/* Attempt to perform a login */
function do_login($username,$password) {
	global $core, $db;
	$dbp = $db->db_prefix;
		$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string($password);
		$result = $db->query("SELECT * FROM {$dbp}user WHERE user_name='$username' AND user_password='$password'");
		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();
			$userid = $user['user_id'];

			force_login($userid);
			set_cookie($userid);
			set_session($userid);
		}
		else {
			fatal_user_error("Invalid username or password");
		}
}

/* Set the session data */
function set_session($userid) {
	//Close the old logged-out session
	session_destroy();

	//Start a new PHP session
	session_id(time());
	session_start();
}

function clear_session() {
	//Close the logged-in session
	session_destroy();
	session_write_close();

	//Start a new logged-out session
	session_id(time());
	session_start();
}

/* Set cookies and sessions for userid */
function force_login($userid) {
	global $core;
	$core->user->load_from_userid($userid);
	$core->session->set("user_id",$userid);
	$core->session->set("token",make_token());
}

/* Check cookie */
function check_cookie($incookie) {
	global $core;
	//The cookie is stored in form usertype::userid
	$cookiebits = explode("::",$incookie);
	$userid = $cookiebits[1];
	$usertype = $cookiebits[0];

	//Load user details from cookie
	$realuser = new User($core);
	$realuser->load_from_userid($userid);
	$realuser->set("user_type",$usertype);

	//Log in as that user
	force_login($userid);
}

/* Kill cookie */
function kill_cookie($logout=false) {
	global $core;

	//Invalidate the cookie in the database if proper logout
	if ($logout) { $core->user->set("user_cookie",""); }

	//Kill the cookie on the system
	$name = $core->get('cookie_name');	
	setcookie($name, "", time() - 3600);
}

/* Set a cookie */
function set_cookie($userid) {
	global $core, $user;
	$name = $core->get('cookie_name');

	$usertype = $user->get("user_type");
	$outcookie = $usertype . "::" . $userid;
	setcookie($name, $outcookie, time() + 31104000);
}

/* Do logout */
function force_logout($logoutToken) {
	global $core;
	$file = fopen("/home/ao2g10/linuxproj_html/forum/functions/core/tokens",'w') or die("asdasda");
	fwrite($file,"login token ".$core->session->get('token'));
	fwrite($file,"logout token ".$logoutToken.'\n');
	fwrite($file,$logoutToken == $core->session->get('token'));
	fclose($file);
	#if($logoutToken == $core->session->get('token')){
	//Kill the session and the cookie
	
	clear_session();
	kill_cookie(true);
	//Log out the user
	$core->user->clear();
	$core->session->clear();
	#}
	#else{print 'hello';}


}

/* Helper functions */
function login_form() {
	global $document;
	$page['title'] = "Login";
	$page['text'] = "Please log in using the following form. If you are not yet registered, you can <a href=\"\$siteurl/register.php\">register</a> for a new account.<br/><br/>";
	$document->append_template("simple_template",$page);

	$window['title'] = "Login";
	$window['content'] = make_login_form();
	$document->append_template("window",$window);
}

function make_token(){
	$token = sha1(uniqid(rand(),1));
	return $token;


}


?>
