<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* Above page-specific */
$document->header("New Reply"); 

/* ================= Page Specific ====================== */
//Include forum functions
require('./modules/forums.php');

//You need to be a registered user to post
if ($user->get('user_type') < 1) {
	fatal_error("Only registered users can post to this forum");
}

//Show the post a new reply form
if (!isset($_GET['do'])) {

	//Show the new reply window
	new_reply_window();

} else if ($_GET['do'] == "edit") {

	//Edit a post
	if (!isset($_GET['p'])) { fatal_error("Please specify a post"); }

	$postid = make_safe("int",$_GET['p']);
	new_reply_window($postid);

} else if ($_GET['do'] == "edit2") {

	//Edit message
	edit_message();

} else {

	//Actually post a new reply 
	post_new_reply();

}

/* End page-specific */
$document->footer();
$document->output();

/* Display a new window */
function new_reply_window($postid = 0) {
	global $core, $document, $user, $boards, $threads, $db, $posts;

	//Check if we are quoting a post
	if (isset($_GET['p'])) {
		$post = make_safe("int",$_GET['p']);
		$quote = $posts->get_post($post);
		$_GET['t'] = $quote['thread_id'];
		if (!$quote) { fatal_error("Invalid post to quote"); }
		$quotemsg = $quote['post_message'];
		$quoteauthor = $quote['user_name'];
	} else {
		$quotemsg = "";
		$quoteauthor = "";
	}

	//Post a new reply
	if ($postid == 0) { 
		if(!isset($_GET['t'])) { fatal_error("No thread specified"); }
		$threadid = make_safe("int",$_GET['t']);
	//Edit a previous post
	} else {
		if(!isset($_GET['p'])) { fatal_error("No post specified"); }
		$post = $posts->get_post($postid);
		if (!$post) { fatal_error("Invalid post specified"); }
		$threadid = $post['thread_id'];		
	}

	//Get the board
	$thread = $threads->get_thread($threadid);
	$threadname = $thread['thread_name'];
	$boardid = $thread['board_id'];
	$board = $boards->get_board($boardid);
	$boardname = $board['board_name'];
	
	//Build breadcrumb
	$window['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
	$window['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / ';
	$window['breadcrumb'] .= '<a href="$siteurl/forumdisplay.php?f=' . $boardid . '">' . $boardname . '</a> / ';
	$window['breadcrumb'] .= '<a href="$siteurl/showthread.php?t=' . $threadid . '">' . $threadname . '</a> / ';

	//New post
	if ($postid == 0) {
		$window['breadcrumb'] .= 'Post Reply</p>';
		$window['heading'] = "Post New Reply";
		$window['description'] = "Fill in all the details required below to post a new reply to the forum";
		$form = new_reply_form($boardid,$threadid,"RE: $threadname",$quotemsg,$quoteauthor); 
	//Edit
	} else {
		$subject = $post['post_name'];
		$message = $post['post_message'];
		$window['breadcrumb'] .= 'Edit Post</p>';
		$window['heading'] = "Edit Post";
		$window['description'] = "Fill in all the details required below to edit this post";
		$form = edit_reply_form($boardid,$threadid,$postid,$subject,$message);
	}

	$window['content'] = $form;
	$document->append_template("forum_form",$window);
}

/* Post a new reply to the forum */
function post_new_reply() {
	global $core, $document, $user, $boards, $threads, $posts, $db;

	if (!isset($_POST['thread_id']) || !isset($_POST['board_id']) || !isset($_POST['post_name']) || !isset($_POST['post_message'])) {
		fatal_error("Some or all of the information needed to post a new message are missing. Please try again");
	}

	//Make fields safe
	$boardid = make_safe("int",$_POST['board_id']);
	$threadid = make_safe("int",$_POST['thread_id']);
	$postname = make_safe("text",$_POST['post_name']);
	$postmessage = make_safe("text",$_POST['post_message']);

	//Sanity check fields
	if (strlen($postname) < 2 || strlen($postmessage) < 10) { 
		fatal_error("Your message title or message text is too short. Please try again");
	}
	if (!$boards->get_board($boardid)) {
		fatal_error("Invalid board specified. Please try again");
	} 
	if (!$threads->get_thread($threadid)) {
		fatal_error("Invalid thread specified. Please try again");
	}

	//Now post the message
	$posts->new_post($user->get("user_id"),$boardid,$threadid,$postname,$postmessage);

	//Now go back to the forum
	$core->do_redirect("forumdisplay.php?f=$boardid");		
}

/* Edit a message in the system */
function edit_message() {
	global $core, $document, $user, $boards, $threads, $posts, $db;

	if (!isset($_POST['thread_id']) || !isset($_POST['board_id']) || !isset($_POST['post_id']) || !isset($_POST['post_name']) || !isset($_POST['post_message'])) {
		fatal_error("Some or all of the information needed to post a new message are missing. Please try again");
	}

	//Make fields safe
	$postid = make_safe("int",$_POST['post_id']);
	$boardid = make_safe("int",$_POST['board_id']);
	$threadid = make_safe("int",$_POST['thread_id']);
	$postname = make_safe("text",$_POST['post_name']);
	$postmessage = make_safe("text",$_POST['post_message']);

	//Sanity check fields
	if (strlen($postname) < 2 || strlen($postmessage) < 10) { 
		fatal_error("Your message title or message text is too short. Please try again");
	}
	if (!$posts->get_post($postid)) {
		fatal_error("Invalid post specified. Please try again");
	}
	if (!$boards->get_board($boardid)) {
		fatal_error("Invalid board specified. Please try again");
	} 
	if (!$threads->get_thread($threadid)) {
		fatal_error("Invalid thread specified. Please try again");
	}

	//Now post the message
	$posts->edit_post($postid,$postname,$postmessage);

	//Now go back to the forum
	$core->do_redirect("showthread.php?t=$threadid");		
}


?>
