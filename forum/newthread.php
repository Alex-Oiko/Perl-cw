<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* Above page-specific */
$document->header("New Thread"); 

/* ================= Page Specific ====================== */
//Include forum functions
require('./modules/forums.php');

//You need to be a registered user to post
if ($user->get('user_type') < 1) {
	fatal_error("Only registered users can post to this forum");
}

//Show the post a new thread form
if (!isset($_GET['do'])) {
	//Show the new thread window
	new_thread_window();
} else {
//Actually post a new thread
	post_new_thread();
}


/* End page-specific */
$document->footer();
$document->output();

/* Display a new thread window */
function new_thread_window() {
	global $core, $document, $user, $boards, $threads, $db;

	//Check a board has been supplied
	if(!isset($_GET['b'])) { fatal_error("No board specified"); }

	//Get the board
	$boardid = make_safe("int",$_GET['b']);
	$board = $boards->get_board($boardid);
	$boardname = $board['board_name'];

	$form = new_thread_form($boardid);

	$window['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
	$window['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / ';
	$window['breadcrumb'] .= '<a href="$siteurl/forumdisplay.php?f=' . $boardid . '">' . $boardname . '</a> / ';
	$window['breadcrumb'] .= 'New Thread</p>';
	$window['heading'] = "Post New Thread";
	$window['description'] = "Fill in all the details required below to post a new thread to the forum";
	$window['content'] = $form;
	$document->append_template("forum_form",$window);


}

/* Post a new thread to the forum */
function post_new_thread() {
	global $core, $document, $user, $boards, $threads, $posts, $db;

	if (!isset($_REQUEST['board_id']) || !isset($_REQUEST['post_name']) || !isset($_REQUEST['post_message'])) {
		fatal_error("Some or all of the information needed to post a new message are missing. Please try again");
	}

	//Make fields safe
	$boardid = make_safe("int",$_REQUEST['board_id']);
	$postname = make_safe("text",$_REQUEST['post_name']);
	$postmessage = make_safe("text",$_REQUEST['post_message']);

	//Sanity check fields
	if (strlen($postname) < 2 || strlen($postmessage) < 10) { 
		fatal_error("Your message title or message text is too short. Please try again");
	}
	if (!$boards->get_board($boardid)) {
		fatal_error("Invalid board specified. Please try again");
	}

	//Now post the message
	$threadid = $threads->new_thread($user->get("user_id"),$boardid,$postname);
	$posts->new_post($user->get("user_id"),$boardid,$threadid,$postname,$postmessage);

	//Now go back to the forum
	$core->do_redirect("forumdisplay.php?f=$boardid");		
}



?>
